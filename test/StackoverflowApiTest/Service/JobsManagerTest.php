<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Service;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Jobs\Entity\Job;
use StackoverflowApi\Client\Client;
use StackoverflowApi\Client\Response;
use StackoverflowApi\Entity\ApiResponse;
use StackoverflowApi\Entity\JobData;
use StackoverflowApi\Service\JobsManager;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Log\Writer\Mock;

/**
 * Tests for \StackoverflowApi\Service\JobsManager
 * 
 * @covers \StackoverflowApi\Service\JobsManager
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Service
 */
class JobsManagerTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestUsesTraitsTrait;

    /**
     *
     *
     * @var array|JobsManager|\ReflectionClass|null
     */
    private $target = [
        JobsManager::class,
        'getConstructorArgs',
        'post' => 'setupLogger',
        '@testInheritance' => ['as_reflection' => true],
        '@testUsesTraits'  => '@testInheritance',
        '@testConstruct'   => false,
    ];

    private $logWriter;

    private $client;

    private $inheritance = [ LoggerAwareInterface::class ];

    private $traits = [ LoggerAwareTrait::class ];

    private function getConstructorArgs()
    {
        $this->client = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendJob', 'deleteJob'])
            ->getMock()
        ;

        return [ $this->client ];
    }

    private function setupLogger()
    {
        $log = new Logger();
        $this->logWriter = new Mock();
        $log->addWriter($this->logWriter);

        $this->target->setLogger($log);
    }

    public function testConstruct()
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $repository = $this->getMockBuilder(DocumentRepository::class)->disableOriginalConstructor()->getMock();

        $target = new JobsManager($client, $repository);

        $this->assertAttributeSame($client, 'client', $target);
    }

    public function testSendUsesAttachedJobData()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(false);
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity', 'getAttachedEntity'])->getMock();
        $job->setId('test');
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(true);
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        $this->client->expects($this->once())->method('sendJob')->willReturn(new Response());

        $this->assertFalse($this->target->send($job, []));
    }

    public function testSendCreatesAttachedJobData()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(false);
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity', 'createAttachedEntity'])->getMock();
        $job->setId('test');
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(false);
        $job->expects($this->once())->method('createAttachedEntity')->with(JobData::class, ['jobId' => $job->getId()], 'stackoverflow')->willReturn($jobData);

        $this->client->expects($this->once())->method('sendJob')->willReturn(new Response());

        $this->assertFalse($this->target->send($job, []));
    }

    public function testSendSetsCorrectUpdateData()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(true);
        $jobData->setExternalId('externalid');
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity', 'getAttachedEntity'])->getMock();
        $job->setId('test');
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(true);
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        $this->client->expects($this->once())->method('sendJob')->with($job, ['action' => 'put', 'externalId' => 'externalid'])->willReturn(new Response());

        $this->target->send($job, []);

    }

    private function getJobAndJobData()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(false);
        $jobData->setExternalId('externalid');
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity', 'getAttachedEntity'])->getMock();
        $job->setId('test');
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(true);
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        return [$job, $jobData];
    }

    public function testSendSetsCorrectPostData()
    {
        list($job,) = $this->getJobAndJobData();

        $this->client->expects($this->once())->method('sendJob')->with($job, ['action' => 'post'])->willReturn(new Response());

        $this->target->send($job, []);

    }

    public function testSendReturnsTrueOnSuccess()
    {
        list($job, $jobData) = $this->getJobAndJobData();
        $response = new Response();
        $response->setContent('<?xml version="1.0" encoding="utf-8"?><response><result>success</result><jobid>jobid</jobid><joburl>url</joburl></response>');
        $this->client->expects($this->once())->method('sendJob')->willReturn($response);

        $this->assertTrue($this->target->send($job, []));
        $this->assertTrue($jobData->isOnline());
        $this->assertEquals('jobid', $jobData->getExternalId());
        $this->assertEquals('url', $jobData->getExternalUrl());
    }

    public function testSendReturnsFalseOnFailure()
    {
        list($job, $jobData) = $this->getJobAndJobData();
        $response = new Response();
        $response->setContent('<?xml version="1.0" encoding="utf-8"?><response><result>error</result><errors><error>error</error></errors></response>');
        $this->client->expects($this->once())->method('sendJob')->willReturn($response);

        $this->assertFalse($this->target->send($job, []));
        $this->assertFalse($jobData->isOnline());
    }

    public function testRemoveJobsReturnsFalseIfJobIsNotOnline()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(false);
        $job = $this->getMockBuilder(Job::class)->setMethods(['getAttachedEntity'])->getMock();
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        $this->client->expects($this->never())->method('deleteJob');

        $this->assertFalse($this->target->remove($job));
    }

    public function testRemoveJobReturnsTrueOnSuccess()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(true)->setExternalId('externalid');

        $job = $this->getMockBuilder(Job::class)->setMethods(['getAttachedEntity'])->getMock();
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        $response = new Response();
        $response->setContent('<?xml version="1.0" encoding="utf-8"?><response><result>success</result></response>');

        $this->client->expects($this->once())->method('deleteJob')->with('externalid')->willReturn($response);

        $this->assertTrue($this->target->remove($job));
        $this->assertInstanceOf(ApiResponse::class, $jobData->getLastResponse());
        $this->assertFalse($jobData->isOnline());

    }

    public function testRemoveJobReturnsFalseOnFailure()
    {
        $jobData = new JobData();
        $jobData->setIsOnline(true)->setExternalId('externalid');

        $job = $this->getMockBuilder(Job::class)->setMethods(['getAttachedEntity'])->getMock();
        $job->expects($this->once())->method('getAttachedEntity')->with('stackoverflow')->willReturn($jobData);

        $response = new Response();
        $response->setContent('<?xml version="1.0" encoding="utf-8"?><response><result>error</result></response>');

        $this->client->expects($this->once())->method('deleteJob')->with('externalid')->willReturn($response);

        $this->assertFalse($this->target->remove($job));
        $this->assertInstanceOf(ApiResponse::class, $jobData->getLastResponse());
        $this->assertTrue($jobData->isOnline());

    }

}
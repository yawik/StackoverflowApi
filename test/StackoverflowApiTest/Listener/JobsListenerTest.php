<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Listener;

use Jobs\Entity\Job;
use Jobs\Entity\Status;
use Jobs\Listener\Events\JobEvent;
use Jobs\Listener\Response\JobResponse;
use StackoverflowApi\Listener\JobsListener;
use StackoverflowApi\Service\JobsManager;

/**
 * Tests for \StackoverflowApi\Listener\JobsListener
 * 
 * @covers \StackoverflowApi\Listener\JobsListener
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Listener
 */
class JobsListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     *
     * @var JobsListener
     */
    private $target;

    /**
     *
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $manager;

    public function setup()
    {
        $this->manager = $this
            ->getMockBuilder(JobsManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['send', 'remove'])
            ->getMock()
        ;

        $this->target = new JobsListener($this->manager);
    }

    public function testConstructionSetsJobsManager()
    {
        $this->assertAttributeSame($this->manager, 'manager', $this->target);
    }

    private function getEvent($extra = null)
    {
        $event = new JobEvent();
        $job = new Job();

        $event->addPortal('stackoverflow');
        $event->setJobEntity($job);

        $data = $extra ? ['channels' => [ 'stackoverflow' => $extra]] : [];

        $event->setParam('extraData', $data);

        return $event;
    }

    public function testOnJobAcceptedReturnsDeniedResponseWhenEventHasNotRightPortal()
    {
        $event = new JobEvent();
        $response = $this->target->onJobAccepted($event);

        $this->assertEquals(JobResponse::RESPONSE_DENIED, $response->getStatus());
    }

    public function testOnJobAcceptedReturnsExpectedResponse()
    {
        $extraData = ['extra' => 'data'];
        $event = $this->getEvent();
        $event2 = $this->getEvent($extraData);
        $this->manager->expects($this->exactly(2))->method('send')
            ->withConsecutive(
                [$event->getJobEntity(), []],
                [$event2->getJobEntity(), $extraData]
            )->will($this->onConsecutiveCalls(true, false));

        $response = $this->target->onJobAccepted($event);
        $response2 = $this->target->onJobAccepted($event2);

        $this->assertEquals(JobResponse::RESPONSE_OK, $response->getStatus());
        $this->assertEquals(JobResponse::RESPONSE_FAIL, $response2->getStatus());
    }

    public function testOnJobStatusChangedReturnsDeniedResponseWhenJobHasNoAttachedEntity()
    {
        $event = new JobEvent();
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity'])->getMock();
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(false);
        $event->setJobEntity($job);

        $response = $this->target->onJobStatusChanged($event);

        $this->assertEquals(JobResponse::RESPONSE_DENIED, $response->getStatus());
    }

    public function testOnJobStatusChangedReturnsDeniedResponseWhenJobIsNotInactive()
    {
        $event = new JobEvent();
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity'])->getMock();
        $job->setStatus(Status::ACTIVE);
        $job->expects($this->once())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(true);
        $event->setJobEntity($job);

        $response = $this->target->onJobStatusChanged($event);

        $this->assertEquals(JobResponse::RESPONSE_DENIED, $response->getStatus());
    }

    public function testOnJobStatusChangedReturnsExpectedResponse()
    {
        $event = new JobEvent();
        $job = $this->getMockBuilder(Job::class)->setMethods(['hasAttachedEntity'])->getMock();
        $job->expects($this->any())->method('hasAttachedEntity')->with('stackoverflow')->willReturn(true);
        $job->setStatus(Status::INACTIVE);
        $event->setJobEntity($job);
        $this->manager->expects($this->exactly(2))->method('remove')->with($job)->will($this->onConsecutiveCalls(true, false));

        $response = $this->target->onJobStatusChanged($event);
        $response2 = $this->target->onJobStatusChanged($event);

        $this->assertEquals(JobResponse::RESPONSE_OK, $response->getStatus());
        $this->assertEquals(JobResponse::RESPONSE_FAIL, $response2->getStatus());
    }
}
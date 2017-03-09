<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Client;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use CoreTestUtils\TestCase\TestSetterGetterTrait;
use CoreTestUtils\TestCase\TestUsesTraitsTrait;
use Jobs\Entity\Job;
use StackoverflowApi\Client\Client;
use StackoverflowApi\Client\DataTransformer;
use StackoverflowApi\Client\Response;
use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerAwareTrait;
use Zend\Log\Writer\Noop;

/**
 * Tests for \StackoverflowApi\Client\Client
 * 
 * @covers \StackoverflowApi\Client\Client
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Client
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, TestSetterGetterTrait, TestUsesTraitsTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|Client|\ReflectionClass
     */
    private $target = [
        Client::class,
        'mock' => ['reset' => 1, 'setRawBody', 'setMethod', 'send'],
        'post' => 'targetPost',
        'args' => false,
        '@testInheritance' => [ 'as_reflection' => true ],
        '@testUsesTraits'  => '@testInheritance',
        '@testSetterAndGetter' => [
            'ignore' => ['mock', 'post'],
            'args' => ['auth-code'],
        ]
    ];

    private $inheritance = [ '\Zend\Http\Client', LoggerAwareInterface::class ];

    private $traits = [ LoggerAwareTrait::class ];

    private function targetPost()
    {
        $log = new Logger();
        $log->addWriter(new Noop());
        $this->target->setLogger($log);

        $this->target->expects($this->once())->method('send')->willReturn(new Response());
    }

    public function propertiesProvider()
    {
        return [
            ['logger', '@\Zend\Log\Logger'],
            ['response', ['default@' => Response::class]],
            ['response', [
                'pre' => function() { $this->target->getResponse(); },
                'default@' => Response::class
            ]],
            ['transformer', ['value' => new DataTransformer(), 'default@' => DataTransformer::class]],
        ];
    }

    public function testSendJob()
    {
        $job = new Job();
        $data = ['test' => 'data'];
        $transformer = $this->getMockBuilder(DataTransformer::class)->setMethods(['transform'])->getMock();
        $transformer->expects($this->once())->method('transform')->with($job, $data)->willReturn('xml-content');

        $this->target->setTransformer($transformer);
        $this->target->expects($this->once())->method('setRawBody')->with('xml-content');
        $this->target->expects($this->once())->method('setMethod')->with(Request::METHOD_POST);

        $this->assertInstanceOf(Response::class, $this->target->sendJob($job, $data));
    }

    public function testDeleteJob()
    {
        $this->target->expects($this->once())->method('setRawBody')
            ->with('<?xml version="1.0" encoding="UTF-8"?>'
                   . PHP_EOL
                   . '<job><action>delete</action><jobid>external-id</jobid><test>true</test></job>' . PHP_EOL);
        $this->target->expects($this->once())->method('setMethod')->with(Request::METHOD_POST);

        $this->assertInstanceOf(Response::class, $this->target->deleteJob('external-id'));
    }

}
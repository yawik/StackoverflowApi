<?php
/**
 * YAWIK Stacloverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Factory\Listener;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use StackoverflowApi\Factory\Listener\JobsListenerFactory;
use StackoverflowApi\Listener\JobsListener;
use StackoverflowApi\Service\JobsManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \StackoverflowApi\Factory\Listener\JobsListenerFactory
 * 
 * @covers \StackoverflowApi\Factory\Listener\JobsListenerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Factory
 * @group StackoverflowApi.Factory.Listener
 */
class JobsListenerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|JobsListenerFactory
     */
    private $target = JobsListenerFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokeCreatesJobsListenerInstance()
    {
        $jobsManager = $this->getMockBuilder(JobsManager::class)->disableOriginalConstructor()->getMock();
        $container = $this->createServiceManagerMock([
            JobsManager::class => $jobsManager
        ]);

        $listener = $this->target->__invoke($container, 'irrelevant');

        $this->assertAttributeSame($jobsManager, 'manager', $listener);
    }
    
}

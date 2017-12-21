<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license    MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */

/** */
namespace StackoverflowApiTest\Factory\Service;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use StackoverflowApi\Client\Client;
use StackoverflowApi\Factory\Service\JobsManagerFactory;
use Zend\Log\Logger;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \StackoverflowApi\Factory\Service\JobsManagerFactory
 *
 * @covers \StackoverflowApi\Factory\Service\JobsManagerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group  StackoverflowApi
 * @group  StackoverflowApi.Factory
 * @group  StackoverflowApi.Factory.Service
 *
 */
class JobsManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|\PHPUnit_Framework_MockObject_MockObject|JobsManagerFactory
     */
    private $target = JobsManagerFactory::class;

    private $inheritance = [FactoryInterface::class];

    public function testInvokeCreatesJobsManagerInstance()
    {
        $log          = new Logger();
        $client       = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $container    = $this->createServiceManagerMock(
                             [
                                 'Log/StackoverflowApi' => $log,
                                 Client::class          => $client,
                             ]
        );

        $manager = $this->target->__invoke($container, 'irrelevant');

        $this->assertAttributeSame($client, 'client', $manager);
        $this->assertSame($log, $manager->getLogger());
    }
}

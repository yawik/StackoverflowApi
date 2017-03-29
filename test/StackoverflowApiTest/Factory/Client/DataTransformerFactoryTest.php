<?php
/**
 * YAWIK StackoverflowApi
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApiTest\Factory\Client;

use CoreTestUtils\TestCase\ServiceManagerMockTrait;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use Jobs\View\Helper\ApplyUrl;
use Organizations\ImageFileCache\Manager;
use StackoverflowApi\Client\DataTransformer;
use StackoverflowApi\Factory\Client\DataTransformerFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\View\Helper\ServerUrl;

/**
 * Tests for \StackoverflowApi\Factory\Client\DataTransformerFactory
 * 
 * @covers \StackoverflowApi\Factory\Client\DataTransformerFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group StackoverflowApi
 * @group StackoverflowApi.Factory
 * @group StackoverflowApi.Factory.Client
 */
class DataTransformerFactoryTest extends \PHPUnit_Framework_TestCase
{
    use TestInheritanceTrait, ServiceManagerMockTrait;

    /**
     *
     *
     * @var array|DataTransformerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $target = [
        DataTransformerFactory::class,
        '@testCreateService' => [
            'mock' => ['__invoke'],
        ],
    ];

    private $inheritance = [ FactoryInterface::class ];

    public function testCreateService()
    {
        $container = $this->getServiceManagerMock();

        $this->target
            ->expects($this->once())
            ->method('__invoke')
            ->with($container, DataTransformer::class);

        $this->target->createService($container);
    }

    public function testInvokationCreatesService()
    {
        $applyUrl = new ApplyUrl();
        $serverUrl = new ServerUrl();
        $imageManager = $this->getMockBuilder(Manager::class)->disableOriginalConstructor()->getMock();
        $viewHelperManager = $this->getPluginManagerMock([
                'ApplyUrl' => $applyUrl,
                'ServerUrl' => $serverUrl,
        ]);

        $container = $this->getServiceManagerMock([
                'Organizations\ImageFileCache\Manager' => $imageManager,
                'ViewHelperManager' => $viewHelperManager,
            ]);

        $transformer = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(DataTransformer::class, $transformer);
        $this->assertSame($applyUrl, $transformer->getApplyUrlHelper());
        $this->assertSame($serverUrl, $transformer->getServerUrlHelper());
        $this->assertSame($imageManager, $transformer->getOrganizationImageManager());
    }
}
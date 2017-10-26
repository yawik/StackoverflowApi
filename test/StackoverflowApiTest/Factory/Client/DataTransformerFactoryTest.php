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
use Zend\ServiceManager\Factory\FactoryInterface;
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
    private $target = DataTransformerFactory::class;

    private $inheritance = [ FactoryInterface::class ];

    public function testInvokationCreatesService()
    {
        $applyUrl = new ApplyUrl();
        $serverUrl = new ServerUrl();
        $imageManager = $this->getMockBuilder(Manager::class)->disableOriginalConstructor()->getMock();
        $viewHelperManager = $this->createPluginManagerMock([
                'applyUrl' => $applyUrl,
                'serverUrl' => $serverUrl,
        ]);

        $container = $this->getServiceManagerMock();
        $container->setService('Organizations\ImageFileCache\Manager', $imageManager);
        $container->setService('ViewHelperManager', $viewHelperManager);

        $transformer = $this->target->__invoke($container, 'irrelevant');

        $this->assertInstanceOf(DataTransformer::class, $transformer);
        $this->assertSame($applyUrl, $transformer->getApplyUrlHelper());
        $this->assertSame($serverUrl, $transformer->getServerUrlHelper());
        $this->assertSame($imageManager, $transformer->getOrganizationImageManager());
    }
}

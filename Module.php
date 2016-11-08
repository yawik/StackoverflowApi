<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace YawikStackoverflowApi;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Module class for YawikStackoverflowAPI
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/',
                    __NAMESPACE__ . 'Test' => __DIR__ . '/test/' . __NAMESPACE__ . 'Test',
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/config');
    }
}

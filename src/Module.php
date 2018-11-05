<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace StackoverflowApi;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Module class for YawikStackoverflowAPI
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/../config');
    }
}

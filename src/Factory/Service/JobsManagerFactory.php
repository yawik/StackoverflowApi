<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license    MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */

/** */
namespace StackoverflowApi\Factory\Service;

use Interop\Container\ContainerInterface;
use StackoverflowApi\Client\Client;
use StackoverflowApi\Service\JobsManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \StackoverflowApi\Service\JobsManager
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class JobsManagerFactory implements FactoryInterface
{

    /**
     * Create a JobsManager instance
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return JobsManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $log          = $container->get('Log/StackoverflowApi');
        $client       = $container->get(Client::class);

        $manager = new JobsManager($client);
        $manager->setLogger($log);

        return $manager;
    }
}

<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Factory\Listener;

use Interop\Container\ContainerInterface;
use StackoverflowApi\Listener\JobsListener;
use StackoverflowApi\Service\JobsManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for \StackoverflowApi\Listener\JobsListener
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class JobsListenerFactory implements FactoryInterface
{
    /**
     * Creates a job listener instance.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return JobsListener
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $manager = $container->get(JobsManager::class);

        return new JobsListener($manager);
    }
}

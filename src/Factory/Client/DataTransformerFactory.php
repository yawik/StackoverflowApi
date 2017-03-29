<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace StackoverflowApi\Factory\Client;

use Interop\Container\ContainerInterface;
use StackoverflowApi\Client\DataTransformer;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for \StackoverflowApi\Client\DataTransformer
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.1.0
 */
class DataTransformerFactory implements FactoryInterface
{
    /**
     * Create a DataTransformer
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     *
     * @return DataTransformer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelperManager = $container->get('ViewHelperManager');
        $applyUrlHelper    = $viewHelperManager->get('ApplyUrl');
        $serverUrlHelper   = $viewHelperManager->get('ServerUrl');
        $orgImageManager   = $container->get('Organizations\ImageFileCache\Manager');
        $transformer       = new DataTransformer();

        $transformer
            ->setApplyUrlHelper($applyUrlHelper)
            ->setServerUrlHelper($serverUrlHelper)
            ->setOrganizationImageManager($orgImageManager)
        ;

        return $transformer;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DataTransformer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, DataTransformer::class);
    }
}
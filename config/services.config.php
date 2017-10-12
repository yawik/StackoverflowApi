<?php
/**
 * YAWIK StackoverflowApi
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */

namespace StackoverflowApi;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            Service\JobsManager::class    => Factory\Service\JobsManagerFactory::class,
            Client\Client::class          => Factory\Client\ClientFactory::class,
            Client\DataTransformer::class => Factory\Client\DataTransformerFactory::class,
            Listener\JobsListener::class  => Factory\Listener\JobsListenerFactory::class,
        ],
        'aliases' => [
            'StackoverflowApi/Client' => Client\Client::class,
        ],
    ],
    
    'filters' => [
        'factories' => [
            Client\JobDescriptionFilter::class => InvokableFactory::class,
        ],
    ],
];

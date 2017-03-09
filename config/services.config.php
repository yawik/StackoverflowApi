<?php
/**
 * YAWIK StackoverflowApi
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */

return [
    'service_manager' => [
        'factories' => [
            \StackoverflowApi\Service\JobsManager::class => \StackoverflowApi\Factory\Service\JobsManagerFactory::class,
            \StackoverflowApi\Client\Client::class => \StackoverflowApi\Factory\Client\ClientFactory::class,
            \StackoverflowApi\Listener\JobsListener::class => \StackoverflowApi\Factory\Listener\JobsListenerFactory::class,
            //\StackoverflowApi\Options\ModuleOptions::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
        ],
        'aliases' => [
            'StackoverflowApi/Client' => \StackoverflowApi\Client\Client::class,
            //'StackoverflowApi/ModuleOptions' => \StackoverflowApi\Options\ModuleOptions::class,
        ],
    ],
];
<?php
/**
 * YAWIK Stackoverflow API
 *
 * @filesource
 * @license MIT
 * @copyright  2016 Cross Solution <http://cross-solution.de>
 */

use Jobs\Listener\Events\JobEvent;

return [

    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'StackoverflowApi\Entity' => 'annotation',
                ],
            ],
            'annotation' => [
                'paths' => [ __DIR__ . '/../src/Entity']
            ],
        ],
    ],

    'options' => [
        'StackoverflowApi/ModuleOptions' => [
                'class' => 'StackoverflowApi\Options\ModuleOptions',
        ],
    ],

    'log' => [
        'Log/StackoverflowApi' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'options' => [
                        'stream' => getcwd() . '/var/log/stackoverflow-api.log',
                        'formatter' => [
                            'name' => 'simple',
                            'options' => [
                                'dateTimeFormat' => 'Y-m-d H:i:s',
                                'format' => '%timestamp% {%uniqueId%} [%priorityName%]: %message% %extra%'
                            ],
                        ],
                    ],
                ],
            ],
            'processors' => [
                [
                    'name' => 'Core/UniqueId',
                ],
            ],
        ],
    ],

    'event_manager' => [
        'Jobs/Events' => [ 'listeners' => [
            \StackoverflowApi\Listener\JobsListener::class => [
                'events' => [
                    JobEvent::EVENT_JOB_ACCEPTED => 'onJobAccepted',
                    JobEvent::EVENT_STATUS_CHANGED => 'onJobStatusChanged',
                ],
                'lazy' => true,
            ],
        ]],
    ],
];

<?php
/**
 * YAWIK Stackoverflow API
 * 
 * @filesource
 * @copyright (c) 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return [
    'modules' => array_merge(
        include_once __DIR__.'/../../../config/common.modules.php',
        [ 'Core', 'Auth', 'Geo', 'Organizations', 'Jobs', 'StackoverflowApi' ]
    ),

    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php',
        ],
    ],
];
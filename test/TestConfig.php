<?php
/**
 * YAWIK Stackoverflow API
 * 
 * @filesource
 * @copyright (c) 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

return [
    'modules' => [
        'Core', 'StackoverflowApi',
    ],

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
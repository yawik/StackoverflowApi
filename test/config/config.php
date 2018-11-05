<?php

chdir(__DIR__.'/../sandbox');

return [
    'modules' => \Core\Yawik::generateModuleConfiguration([
        'Core',
        'Cv',
        'Auth',
        'Jobs',
        'Applications',
        'Organizations',
        'Geo',
        'Settings',
        'StackoverflowApi',
    ]),
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        // What configuration files should be autoloaded
        'config_glob_paths' => array(
            __DIR__.'/autoload/{,*.}{global,local}.php',
        ),
        'cache_dir' => realpath(__DIR__.'/../sandbox')."/var/cache"
    ],
];

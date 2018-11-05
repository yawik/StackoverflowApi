<?php

require __DIR__.'/../../../vendor/autoload.php';

use Core\Yawik;

// Retrieve configuration
$appConfig = include __DIR__.'/../../config/config.php';
Yawik::runApplication($appConfig);

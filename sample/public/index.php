<?php

/**
 * Composer Autoloader
 * */
require __DIR__ . '/../../vendor/autoload.php';

/**
 * Application Bootstrap
 */
$app = require __DIR__ . '/../bootstrap.php';


/**
 * Handle Request
 */
$app->handle();

<?php

/**
 * Services are globally registered in this file
 */

$di = new \Phalcon\Di\FactoryDefault();

$config = require __DIR__ . '/config/config.php';

$di->setShared('config', $config);

/**
 * Set Router
 */
$di->setShared('router', function () {
    $router = new \Phalcon\Mvc\Router(false);
    $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
    $router->removeExtraSlashes(true);

    return $router;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
    $dbConfig = $config->database->toArray();
    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $adapter;

    return new $class($dbConfig);
});

/**
 * OAuth2 Server Service
 */
$di->setShared('oauth', \Phalcon\OAuth2\Server\Gateway::phqlStorage());

/**
 * Starting the application
 * Assign service locator to the application
 */
$app = new \Phalcon\Mvc\Micro($di);

/**
 * Routes
 */
require __DIR__ . '/routes.php';

return $app;
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
 * Resource Server Service
 */
$di->setShared('resource', function () {
    $server = new \League\OAuth2\Server\ResourceServer(
        new \Phalcon\OAuth2\Server\Storage\Phql\SessionStorage(),
        new \Phalcon\OAuth2\Server\Storage\Phql\AccessTokenStorage(),
        new \Phalcon\OAuth2\Server\Storage\Phql\ClientStorage(),
        new \Phalcon\OAuth2\Server\Storage\Phql\ScopeStorage()
    );

    return $server;
});

/**
 * Authorization Server Service
 */
$di->setShared('authorization', function () {
    $server = new \League\OAuth2\Server\AuthorizationServer;

    $server->setSessionStorage(new \Phalcon\OAuth2\Server\Storage\Phql\SessionStorage());
    $server->setAccessTokenStorage(new \Phalcon\OAuth2\Server\Storage\Phql\AccessTokenStorage());
    $server->setClientStorage(new \Phalcon\OAuth2\Server\Storage\Phql\ClientStorage());
    $server->setScopeStorage(new \Phalcon\OAuth2\Server\Storage\Phql\ScopeStorage());

    $server->addGrantType(new \League\OAuth2\Server\Grant\ClientCredentialsGrant());
    $server->addGrantType(new \League\OAuth2\Server\Grant\AuthCodeGrant());
    $server->addGrantType(new \League\OAuth2\Server\Grant\PasswordGrant());
    $server->addGrantType(new \League\OAuth2\Server\Grant\RefreshTokenGrant());

    $server->setRequest(\Symfony\Component\HttpFoundation\Request::createFromGlobals());

    return $server;
});

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
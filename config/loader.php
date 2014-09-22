<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        $config->application->modelsDir,
    )
)->registerNamespaces([
    'Sumeko' => __DIR__ . '/../src/Sumeko/',
    'League' => __DIR__ . '/../oauth2-server/src/',
])->register();

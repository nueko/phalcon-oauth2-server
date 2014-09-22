<?php

error_reporting(E_ALL);
function dump($var) {
    foreach (func_get_args() as $arg) {
        var_dump($arg);
        echo "\n************\n";
    }
    exit(0);
}
try {

    require __DIR__ ."/../vendor/autoload.php";
    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../config/config.php";

    /**
     * Include Services
     */
    include __DIR__ . '/../config/services.php';

    /**
     * Include Autoloader
     */
    include __DIR__ . '/../config/loader.php';

    /**
     * Starting the application
    */
    $app = new \Phalcon\Mvc\Micro();

    /**
     * Assign service locator to the application
     */
    $app->setDi($di);

    /**
     * Incude Application
     */
    include __DIR__ . '/../app.php';

    /**
     * Handle the request
     */
    $app->handle();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage(), $e->getFile(), $e->getLine(),
    $e->getCode();
} catch (PDOException $e) {
    echo $e->getMessage() . PHP_EOL . $e->getFile() .PHP_EOL . $e->getLine() . PHP_EOL . $e->getCode();
}

<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'    => 'Mysql',
        'host'       => 'localhost',
        'username'   => 'root',
        'password'   => '',
        'dbname'     => 'oauth2dev',
    ),
    'application' => array(
        'modelsDir'      => __DIR__ . '/../models/',
        'baseUri'        => '/oauth2/',
    )
));

<?php

return new \Phalcon\Config([

    'database' => [
        'adapter'    => 'Mysql',
        'host'       => 'localhost',
        'username'   => 'webservice',
        'password'   => '123456',
        'dbname'     => 'oauth2',
        'persistent' => false,
        'charset'    => 'utf8',
    ],
]);

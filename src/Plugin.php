<?php

namespace Phalcon\OAuth2\Server;

use Phalcon\Mvc\User\Plugin as PhalconPlugin;
use Phalcon\OAuth2\Server\Storage;

/**
 * Class Plugin
 * @package Phalcon\OAuth2\Server\Storage
 */
class Plugin extends PhalconPlugin
{
    use Storage;
}
<?php

namespace Phalcon\OAuth2\Server;

use Phalcon\Mvc\User\Component as PhalconComponent;

/**
 * Class Plugin
 * @package Phalcon\OAuth2\Server\Storage
 */
abstract class Component extends PhalconComponent
{
    use Storage;

    /**
     * @param null $params
     *
     * @return \Phalcon\Mvc\Model\Query\BuilderInterface
     */
    public function getBuilder($params = null)
    {
        return $this->modelsManager->createBuilder($params);
    }
}
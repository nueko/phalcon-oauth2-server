<?php
namespace Sumeko\Phalcon\Oauth2\Server\Storage;

interface AdapterInterface
{
    /**
     * Set the server
     * @param \League\OAuth2\Server\AbstractServer $server
     */
    public function setServer($server);

    /**
     * Return the server
     * @return \League\OAuth2\Server\AbstractServer
     */
    public function getServer();
} 
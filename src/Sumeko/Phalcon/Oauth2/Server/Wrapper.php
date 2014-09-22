<?php
namespace Sumeko\Phalcon\Oauth2\Server;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\InvalidGrantException;
use League\OAuth2\Server\ResourceServer;
use Phalcon\Exception;
use Phalcon\Mvc\User\Plugin;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\AccessToken as AccessTokenStorage;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\AuthCode as AuthCodeStorage;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Client as ClientStorage;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\RefreshToken as RefreshTokenStorage;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Scope as ScopeStorage;
use Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Session as SessionStorage;

class Wrapper extends Plugin
{

    /** @var \League\OAuth2\Server\AuthorizationServer  */
    public $resource;
    /** @var ResourceServer  */
    public $authorize;
    protected $defaultScope = "basic";
    protected $grants = [
        'authorize_code' => 'League\OAuth2\Server\Grant\AuthCodeGrant',
        'client_credentials' => 'League\OAuth2\Server\Grant\ClientCredentialsGrant',
        'password' => 'League\OAuth2\Server\Grant\PasswordGrant',
        'refresh_token' => 'League\OAuth2\Server\Grant\RefreshTokenGrant'
    ];

    public function initAuthorizationServer()
    {
        if(! $this->authorize) {
            $authorize = new AuthorizationServer;
            $authorize->setDefaultScope($this->defaultScope);
            $authorize->setSessionStorage(new SessionStorage);
            $authorize->setAccessTokenStorage(new AccessTokenStorage);
            $authorize->setRefreshTokenStorage(new RefreshTokenStorage);
            $authorize->setClientStorage(new ClientStorage);
            $authorize->setScopeStorage(new ScopeStorage);
            $authorize->setAuthCodeStorage(new AuthCodeStorage);
            $this->authorize = $authorize;
        }
        return $this;
    }

    public function initResourceServer()
    {
        if(! $this->resource) {
            $this->resource = new ResourceServer(
                new SessionStorage,
                new AccessTokenStorage,
                new ClientStorage,
                new ScopeStorage
            );
        }
        return $this;
    }

    public function enableGrant($name)
    {
        if(!$this->authorize)
            $this->initAuthorizationServer();

        if(array_key_exists($name, $this->grants)) {
            $grants = $this->grants[$name];
            $this->authorize->addGrantType(new $grants);
        } else {
            throw new InvalidGrantException("$name grant unknown");
        }

        return $this;
    }

    public function enableAllGrants()
    {
        if(!$this->authorize)
            $this->initAuthorizationServer();

        foreach ($this->grants as $type => $grant) {
            $grant = new $grant;
            if ($type == 'password') {
                $grant->setVerifyCredentialsCallback(function ($username, $password) {
                    $user = \Users::findFirstByUsername($username);

                    if ($user && $this->security->checkHash($password, $user->password)) {
                        return $user->username;
                    }

                    return false;
                });
            }
            $this->authorize->addGrantType(new $grant);
        }
    }

}
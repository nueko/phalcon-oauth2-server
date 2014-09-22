<?php

namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo;


use League\OAuth2\Server\Entity\AbstractTokenEntity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use Phalcon\Db;
use Phalcon\Mvc\User\Plugin;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite db
 */
class AccessToken extends Plugin implements AccessTokenInterface
{
    use AdapterTrait;

    /**
     * Get an instance of Entity\AccessTokenEntity
     * @param  string $token The access token
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function get($token)
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM oauth_access_tokens WHERE access_token = ? AND expire_time >= ?",
            Db::FETCH_ASSOC,
            [$token, time()]
        );

        if (count($result) === 1) {
            $token = (new AccessTokenEntity($this->server))
                ->setId($result[0]['access_token'])
                ->setExpireTime($result[0]['expire_time']);

            return $token;
        }

        return null;
    }

    /**
     * Get the scopes for an access token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token
     * @return array                                            Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AbstractTokenEntity $token)
    {
        $result = $this->db->fetchAll(
            "SELECT os.id, os.description FROM oauth_access_token_scopes oats JOIN oauth_scopes os ON oats.scope = os.id WHERE access_token = ?",
            Db::FETCH_ASSOC,
            [$token->getId()]
        );

        $response = [];

        if (count($result) > 0) {
            foreach ($result as $row) {
                $scope = (new ScopeEntity($this->server))->hydrate([
                    'id'            =>  $row['id'],
                    'description'   =>  $row['description']
                ]);
                $response[] = $scope;
            }
        }

        return $response;
    }

    /**
     * Creates a new access token
     * @param  string $token The access token
     * @param  integer $expireTime The expire time expressed as a unix timestamp
     * @param  string|integer $sessionId The session ID
     * @return void
     */
    public function create($token, $expireTime, $sessionId)
    {
        $this->db->insert(
            'oauth_access_tokens',
            [$token, $sessionId, $expireTime],
            ['access_token', 'session_id', 'expire_time']
        );
    }

    /**
     * Associate a scope with an acess token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(AbstractTokenEntity $token, ScopeEntity $scope)
    {
        $this->db->insert(
            'oauth_access_token_scopes',
            [$token->getId(), $scope->getId()],
            ['access_token', 'scope']
        );
    }

    /**
     * Delete an access token
     * @param  \League\OAuth2\Server\Entity\AbstractTokenEntity $token The access token to delete
     * @return void
     */
    public function delete(AbstractTokenEntity $token)
    {
        $this->db->delete(
            "oauth_access_token_scopes",
            "access_token = ?",
            [$token->getId()]
        );
    }
}
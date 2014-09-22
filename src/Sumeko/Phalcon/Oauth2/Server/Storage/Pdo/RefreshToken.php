<?php

namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;
use Phalcon\Db;
use Phalcon\Mvc\User\Plugin;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite $db
 */
class RefreshToken extends Plugin implements RefreshTokenInterface
{

    use AdapterTrait;

    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshTokenEntity
     * @param  string $token
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function get($token)
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM oauth_refresh_tokens WHERE refresh_token = ? AND expire_time >= ?",
            Db::FETCH_ASSOC,
            [$token, time()]
        );

        if (count($result) === 1) {
            $token = (new RefreshTokenEntity($this->server))
                ->setId($result[0]['refresh_token'])
                ->setExpireTime($result[0]['expire_time'])
                ->setAccessTokenId($result[0]['access_token']);

            return $token;
        }

        return null;
    }

    /**
     * Create a new refresh token_name
     * @param  string $token
     * @param  integer $expireTime
     * @param  string $accessToken
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function create($token, $expireTime, $accessToken)
    {
        $this->db->insert(
            'oauth_refresh_tokens',
            [$token, $accessToken, $expireTime],
            ['refresh_token', 'access_token', 'expire_time']
        );
    }

    /**
     * Delete the refresh token
     * @param  \League\OAuth2\Server\Entity\RefreshTokenEntity $token
     * @return void
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->db->delete(
            "oauth_refresh_tokens",
            "refresh_token = ?",
            [$token->getId()]
        );
    }
}
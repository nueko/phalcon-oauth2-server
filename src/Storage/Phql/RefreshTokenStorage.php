<?php

namespace Phalcon\OAuth2\Server\Storage\Phql;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;
use Phalcon\OAuth2\Server\Component;
use Phalcon\OAuth2\Server\Models\RefreshToken;

/**
 * Class RefreshTokenStorage
 * @package Phalcon\OAuth2\Server\Storage
 */
class RefreshTokenStorage extends Component implements RefreshTokenInterface
{
    /**
     * Return a new instance of \League\OAuth2\Server\Entity\RefreshTokenEntity.
     *
     * @param string $token
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function get($token)
    {
        $result = $this->getBuilder()->from(RefreshToken::class)
            ->where('id = :id:')
            ->andWhere('expire_time = :time:')
            ->getQuery()
            ->getSingleResult([
                'id'   => $token,
                'time' => time(),
            ]);
        if (!$result) {
            return null;
        }

        /** @type RefreshToken $result */
        return (new RefreshTokenEntity($this->getServer()))
            ->setAccessTokenId($result->access_token_id)
            ->setId($result->id)
            ->setExpireTime((int)$result->expire_time);
    }

    /**
     * Create a new refresh token_name.
     *
     * @param  string $token
     * @param  int $expireTime
     * @param  string $accessToken
     *
     * @return \League\OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function create($token, $expireTime, $accessToken)
    {
        $token = new RefreshToken();
        $token->save([
            'id'              => $token,
            'expire_time'     => $expireTime,
            'access_token_id' => $accessToken,
        ]);

        return (new RefreshTokenEntity($this->getServer()))
            ->setAccessTokenId($accessToken)
            ->setId($token)
            ->setExpireTime((int)$expireTime);
    }

    /**
     * Delete the refresh token.
     *
     * @param  \League\OAuth2\Server\Entity\RefreshTokenEntity $token
     *
     * @return void
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->modelsManager
            ->executeQuery(
                "DELETE FROM [" . RefreshToken::class . "] WHERE id = :id:", ['id' => $token->getId()]
            );
    }
}
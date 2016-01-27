<?php

namespace Phalcon\OAuth2\Server\Storage\Phql;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;
use Phalcon\OAuth2\Server\Component;
use Phalcon\OAuth2\Server\Models\AccessToken;
use Phalcon\OAuth2\Server\Models\AccessTokenScope;
use Phalcon\OAuth2\Server\Models\Scope;

class AccessTokenStorage extends Component implements AccessTokenInterface
{

    /**
     * Get an instance of Entity\AccessTokenEntity
     *
     * @param string $token The access token
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity | null
     */
    public function get($token)
    {
        $result = $this->getBuilder()
            ->from(AccessToken::class)
            ->where('id = :token:')
            ->getQuery()->getSingleResult(compact('token'));

        if ($result === false) {
            return null;
        }

        /** @type AccessToken $result */
        return (new AccessTokenEntity($this->getServer()))
            ->setId($result->id)
            ->setExpireTime(intval($result->expire_time));
    }

    /**
     * Get the scopes for an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity[] Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AccessTokenEntity $token)
    {
        $query = $this->getBuilder()
            ->from(['ats' => AccessTokenScope::class])
            ->join(Scope::class, 'ats.scope_id = s.id', 's')
            ->columns('s.id, s.description')
            ->where('ats.access_token_id = :id:')
            ->getQuery()
            ->execute(['id' => $token->getId()]);

        $scopes = [];

        foreach ($query as $scope) {
            /** @type \Phalcon\Mvc\Model $scope */
            $client = new ScopeEntity($this->getServer());
            $scopes[] = $client->hydrate($scope->toArray());
        }

        return $scopes;
    }

    /**
     * Creates a new access token
     *
     * @param string $token The access token
     * @param integer $expireTime The expire time expressed as a unix timestamp
     * @param string|integer $sessionId The session ID
     *
     * @return \League\OAuth2\Server\Entity\AccessTokenEntity
     */
    public function create($token, $expireTime, $sessionId)
    {
        $accessToken = new AccessToken();

        $saved = $accessToken->save([
            'id'          => $token,
            'expire_time' => $expireTime,
            'session_id'  => $sessionId,
        ]);

        if (!$saved) {
            return null;
        }

        return (new AccessTokenEntity($this->getServer()))
            ->setId($token)
            ->setExpireTime(intval($expireTime));
    }

    /**
     * Associate a scope with an acess token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token
     * @param \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     *
     * @return void
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $accessTokenScope = new AccessTokenScope();
        $accessTokenScope->save([
            'access_token_id' => $token->getId(),
            'scope_id'        => $scope->getId(),
        ]);
    }

    /**
     * Delete an access token
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AccessTokenEntity $token)
    {
        $this->modelsManager
            ->executeQuery("DELETE FROM [" . AccessToken::class . "] WHERE id = :id:", ['id' => $token->getId()]);
    }

}
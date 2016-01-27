<?php

namespace Phalcon\OAuth2\Server\Storage\Phql;

use Phalcon\OAuth2\Server\Component;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AuthCodeInterface;
use Phalcon\OAuth2\Server\Models\AuthCode;
use Phalcon\OAuth2\Server\Models\AuthCodeScope;
use Phalcon\OAuth2\Server\Models\Scope;

class AuthCodeStorage extends Component implements AuthCodeInterface
{
    /**
     * Get the auth code.
     *
     * @param  string $code
     *
     * @return \League\OAuth2\Server\Entity\AuthCodeEntity
     */
    public function get($code)
    {
        $result = $this->getBuilder()->from(['auth' => AuthCode::class])
            ->where('auth.id = :code:', compact('code'))
            ->andWhere('auth.expire_time >= :time:', ['time' => time()])
            ->getQuery()
            ->getSingleResult();

        if (!$result) {
            return null;
        }

        /** @type AuthCode $result */
        return (new AuthCodeEntity($this->getServer()))
            ->setRedirectUri($result->redirect_uri)
            ->setExpireTime(intval($result->expire_time))
            ->setId($result->id);
    }

    /**
     * Get the scopes for an access token.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     *
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $result = $this->getBuilder()
            ->from(['auth' => AuthCodeScope::class])
            ->join(Scope::class, 'auth.scope_id = scope.id', 'scope')
            ->columns('scope.id, scope.description')
            ->where('auth.auth_code_id = :id:')
            ->getQuery()
            ->execute(['id' => $token->getId()]);

        $scopes = [];

        foreach ($result as $scope) {
            /** @type Scope $scope */
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate($scope->toArray());
        }

        return $scopes;
    }

    /**
     * Associate a scope with an access token.
     *
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     *
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $authScope = new AuthCodeScope();
        $authScope->save([
            'auth_code_id' => $token->getId(),
            'scope_id'     => $scope->getId(),
        ]);
    }

    /**
     * Delete an access token.
     *
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     *
     * @return void
     */
    public function delete(AuthCodeEntity $token)
    {
        if ($auth = AuthCode::findFirst($token->getId())) {
            $auth->delete();
        }
    }

    /**
     * Create an auth code.
     *
     * @param string $token The token ID
     * @param int $expireTime Token expire time
     * @param int $sessionId Session identifier
     * @param string $redirectUri Client redirect uri
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $authCode = new AuthCode();
        $authCode->save([
            'id'           => $token,
            'session_id'   => $sessionId,
            'redirect_uri' => $redirectUri,
            'expire_time'  => $expireTime,
        ]);
    }
}
<?php

namespace Phalcon\OAuth2\Server\Storage\Phql;

use Phalcon\OAuth2\Server\Component;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\SessionInterface;
use Phalcon\OAuth2\Server\Models\AccessToken;
use Phalcon\OAuth2\Server\Models\AuthCode;
use Phalcon\OAuth2\Server\Models\Scope;
use Phalcon\OAuth2\Server\Models\Session;
use Phalcon\OAuth2\Server\Models\SessionScope;

class SessionStorage extends Component implements SessionInterface
{

    /**
     * Get a session from it's identifier.
     *
     * @param string $sessionId
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function get($sessionId)
    {
        $result = Session::findFirst($sessionId);

        if (! $result) {
            return null;
        }

        return (new SessionEntity($this->getServer()))
            ->setId($result->id)
            ->setOwner($result->owner_type, $result->owner_id);
    }

    /**
     * Get a session from an access token.
     *
     * @param \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $result = $this->getBuilder()
            ->from(['session' => Session::class])
            ->join(AccessToken::class, 'session.id = token.session_id', 'token')
            ->where('token.id = :token:', ['token' => $accessToken->getId()])
            ->columns('session.*')
            ->getQuery()
            ->getSingleResult();

        if (!$result) {
            return null;
        }

        /** @type Session $result */
        return (new SessionEntity($this->getServer()))
            ->setId($result->id)
            ->setOwner($result->owner_type, $result->owner_id);
    }

    /**
     * Get a session's scopes.
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity
     *
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session)
    {
        $result = $this->getBuilder()
            ->from(['session' => SessionScope::class])
            ->join(Scope::class, 'session.scope_id = scope.id', 'scope')
            ->columns('scope.*')
            ->where('session.session_id = :sid:', ['sid' => $session->getId()])
            ->getQuery()
            ->execute();

        $scopes = [];

        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->getServer()))->hydrate([
                'id' => $scope->id,
                'description' => $scope->description,
            ]);
        }

        return $scopes;
    }

    /**
     * Create a new session.
     *
     * @param string $ownerType Session owner's type (user, client)
     * @param string $ownerId Session owner's ID
     * @param string $clientId Client ID
     * @param string $clientRedirectUri Client redirect URI (default = null)
     *
     * @return int The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        $session = new Session();
        $session->save([
            'client_id' => $clientId,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'client_redirect_uri' => $clientRedirectUri,
        ]);
        return $session->id;
    }

    /**
     * Associate a scope with a session.
     *
     * @param \League\OAuth2\Server\Entity\SessionEntity $session
     * @param \League\OAuth2\Server\Entity\ScopeEntity $scope The scopes ID might be an integer or string
     *
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $sessionScope = new SessionScope();
        $sessionScope->save([
            'session_id' => $session->getId(),
            'scope_id' => $scope->getId(),
        ]);
    }

    /**
     * Get a session from an auth code.
     *
     * @param \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $result = $this->getBuilder()
            ->from(['session' => Session::class])
            ->join(AuthCode::class, 'session.id = auth.session_id', 'auth')
            ->where('auth.id = :id:')
            ->columns('session.*')
            ->getQuery()
            ->getSingleResult(['id' => $authCode->getId()]);

        if (!$result) {
            return null;
        }

        /** @type Session $result */
        return (new SessionEntity($this->getServer()))
            ->setId($result->id)
            ->setOwner($result->owner_type, $result->owner_id);
    }
}
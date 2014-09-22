<?php
namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Standalone;

use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\SessionInterface;
use Phalcon\Db;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;


/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite db
 */
class Session implements SessionInterface
{
    use AdapterTrait;

    protected $db;

    public function __construct($db)
    {
        if(! $this->db) {
            $this->db = $db;
        }
    }

    /**
     * Get a session from an access token
     * @param  \League\OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $result = $this->db->fetchAll(
            "SELECT s.id, s.owner_type, s.owner_id, s.client_id, s.client_redirect_uri FROM oauth_sessions s " .
            "JOIN oauth_access_tokens t ON t.session_id = s.id " .
            "WHERE t.access_token = ?",
            Db::FETCH_ASSOC,
            [$accessToken->getId()]
        );

        if (count($result) === 1) {
            $session = new SessionEntity($this->server);
            $session->setId($result[0]['id']);
            $session->setOwner($result[0]['owner_type'], $result[0]['owner_id']);

            return $session;
        }

        return NULL;
    }

    /**
     * Get a session from an auth code
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     * @return \League\OAuth2\Server\Entity\SessionEntity
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $result = $this->db->fetchAll(
            "SELECT s.id, s.owner_type, s.owner_id, s.client_id, s.client_redirect_uri FROM oauth_sessions s " .
            "JOIN oauth_auth_codes c ON c.session_id = s.id " .
            "WHERE c.auth_code = ?",
            Db::FETCH_ASSOC,
            [$authCode->getId()]
        );

        if (count($result) === 1) {
            $session = new SessionEntity($this->server);
            $session->setId($result[0]['id']);
            $session->setOwner($result[0]['owner_type'], $result[0]['owner_id']);

            return $session;
        }

        return NULL;
    }

    /**
     * Get a session's scopes
     * @param  \League\OAuth2\Server\Entity\SessionEntity
     * @return array Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session)
    {
        $result = $this->db->fetchAll(
            "SELECT oauth_scopes.* FROM oauth_sessions
            JOIN oauth_session_scopes on oauth_sessions.id = oauth_session_scopes.session_id
            JOIN oauth_scopes on oauth_scopes.id = oauth_session_scopes.scope
            WHERE oauth_sessions.id = ?",
            Db::FETCH_ASSOC,
            [$session->getId()]
        );

        $scopes = [];

        foreach ($result as $scope) {
            $scopes[] = (new ScopeEntity($this->server))->hydrate([
                'id'          => $scope['id'],
                'description' => $scope['description']
            ]);
        }

        return $scopes;
    }

    /**
     * Create a new session
     * @param  string $ownerType Session owner's type (user, client)
     * @param  string $ownerId Session owner's ID
     * @param  string $clientId Client ID
     * @param  string $clientRedirectUri Client redirect URI (default = null)
     * @return integer The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = NULL)
    {
        $this->db->insert(
            "oauth_sessions",
            [$ownerType, $ownerId, $clientId],
            ['owner_type', 'owner_id', 'client_id']
        );

        return $this->db->lastInsertId();
    }

    /**
     * Associate a scope with a session
     * @param  \League\OAuth2\Server\Entity\SessionEntity $scope The scope
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $this->db->insert(
            "oauth_session_scopes",
            [$session->getId(), $scope->getId()],
            ['session_id', 'scope']
        );
    }
}
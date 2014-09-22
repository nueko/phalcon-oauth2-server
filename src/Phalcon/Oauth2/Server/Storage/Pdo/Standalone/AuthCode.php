<?php
namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Standalone;

use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AuthCodeInterface;
use Phalcon\Db;
use Phalcon\Mvc\User\Plugin;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite db
 */
class AuthCode implements AuthCodeInterface
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
     * Get the auth code
     * @param  string $code
     * @return \League\OAuth2\Server\Entity\AuthCodeEntity
     */
    public function get($code)
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM oauth_auth_codes WHERE auth_code = ? AND expire_time >= ?",
            Db::FETCH_ASSOC,
            [$code, time()]
        );

        if (count($result) === 1) {
            $token = new AuthCodeEntity($this->server);
            $token->setId($result[0]['auth_code']);
            $token->setRedirectUri($result[0]['client_redirect_uri']);
            return $token;
        }

        return null;
    }

    /**
     * Create an auth code.
     * @param string $token The token ID
     * @param integer $expireTime Token expire time
     * @param integer $sessionId Session identifier
     * @param string $redirectUri Client redirect uri
     *
     * @return void
     */
    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $cols = [
            'auth_code'     =>  $token,
            'client_redirect_uri'  =>  $redirectUri,
            'session_id'    =>  $sessionId,
            'expire_time'   =>  $expireTime
        ];
        $this->db->insert(
            'oauth_auth_codes',
            array_values($cols),
            array_keys($cols)
        );
    }

    /**
     * Get the scopes for an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @return array                                       Array of \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $result = $this->db->fetchAll(
            "SELECT os.id, os.description FROM oauth_auth_code_scopes oacs JOIN oauth_scopes os ON oacs.scope = os.id WHERE auth_code =?",
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
     * Associate a scope with an acess token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The auth code
     * @param  \League\OAuth2\Server\Entity\ScopeEntity $scope The scope
     * @return void
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $this->db->insert(
            'oauth_auth_code_scopes',
            [$token->getId(), $scope->getId()],
            ['auth_code', 'scope']
        );
    }

    /**
     * Delete an access token
     * @param  \League\OAuth2\Server\Entity\AuthCodeEntity $token The access token to delete
     * @return void
     */
    public function delete(AuthCodeEntity $token)
    {
        $this->db->delete(
            "oauth_auth_codes",
            "auth_code = ?",
            [$token->getId()]
        );
    }
}
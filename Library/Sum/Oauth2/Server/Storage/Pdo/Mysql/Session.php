<?php
namespace Sum\Oauth2\Server\Storage\Pdo\Mysql;

use League\OAuth2\Server\Storage\SessionInterface;
use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db;

class Session implements SessionInterface
{

    protected $db;
    protected $tables = array(
        'oauth_sessions'                => 'oauth_sessions',
        'oauth_session_authcodes'       => 'oauth_session_authcodes',
        'oauth_session_redirects'       => 'oauth_session_redirects',
        'oauth_session_access_tokens'   => 'oauth_session_access_tokens',
        'oauth_session_refresh_tokens'  => 'oauth_session_refresh_tokens',
        'oauth_session_token_scopes'    => 'oauth_session_token_scopes',
        'oauth_session_authcode_scopes' => 'oauth_session_authcode_scopes'
    );


    /**
     * @param $db \Phalcon\Db\Adapter\Pdo
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Create a new session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_sessions (client_id, owner_type,  owner_id)
     *  VALUE (:clientId, :ownerType, :ownerId)
     * </code>
     *
     * @param  string $clientId The client ID
     * @param  string $ownerType The type of the session owner (e.g. "user")
     * @param  string $ownerId The ID of the session owner (e.g. "123")
     * @return int               The session ID
     */
    public function createSession($clientId, $ownerType, $ownerId)
    {
        $this->db->insert(
            $this->tables['oauth_sessions'],
            [$clientId, $ownerType, $ownerId],
            ['client_id', 'owner_type', 'owner_id']
        );
        return $this->db->lastInsertId();
    }

    /**
     * Delete a session
     *
     * Example SQL query:
     *
     * <code>
     * DELETE FROM oauth_sessions WHERE client_id = :clientId AND owner_type = :type AND owner_id = :typeId
     * </code>
     *
     * @param  string $clientId The client ID
     * @param  string $ownerType The type of the session owner (e.g. "user")
     * @param  string $ownerId The ID of the session owner (e.g. "123")
     * @return void
     */
    public function deleteSession($clientId, $ownerType, $ownerId)
    {
        $this->db->delete(
            $this->tables['oauth_sessions'],
            'client_id = :clientId AND owner_type = :type AND owner_id = :typeId',
            ['type' => $ownerType, 'typeId' => $ownerId, 'clientId' => $clientId]
        );
    }

    /**
     * Associate a redirect URI with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_redirects (session_id, redirect_uri) VALUE (:sessionId, :redirectUri)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $redirectUri The redirect URI
     * @return void
     */
    public function associateRedirectUri($sessionId, $redirectUri)
    {
        $this->db->insert(
            $this->tables['oauth_session_redirects'],
            [$sessionId, $redirectUri],
            ['session_id', 'redirect_uri']
        );
    }

    /**
     * Associate an access token with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_access_tokens (session_id, access_token, access_token_expires)
     *  VALUE (:sessionId, :accessToken, :accessTokenExpire)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $accessToken The access token
     * @param  int $expireTime Unix timestamp of the access token expiry time
     * @return int                 The access token ID
     */
    public function associateAccessToken($sessionId, $accessToken, $expireTime)
    {
        $this->db->insert(
            $this->tables['oauth_session_access_tokens'],
            [$sessionId, $accessToken, $expireTime],
            ['session_id', 'access_token', 'access_token_expires']
        );
        return $this->db->lastInsertId();
    }

    /**
     * Associate a refresh token with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_refresh_tokens (session_access_token_id, refresh_token, refresh_token_expires,
     *  client_id) VALUE (:accessTokenId, :refreshToken, :expireTime, :clientId)
     * </code>
     *
     * @param  int $accessTokenId The access token ID
     * @param  string $refreshToken The refresh token
     * @param  int $expireTime Unix timestamp of the refresh token expiry time
     * @param  string $clientId The client ID
     * @return void
     */
    public function associateRefreshToken($accessTokenId, $refreshToken, $expireTime, $clientId)
    {
        $this->db->insert(
            $this->tables['oauth_session_refresh_tokens'],
            [$accessTokenId, $refreshToken, $expireTime, $clientId],
            ['session_access_token_id', 'refresh_token', 'refresh_token_expires', 'client_id']
        );
    }

    /**
     * Assocate an authorization code with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_authcodes (session_id, auth_code, auth_code_expires)
     *  VALUE (:sessionId, :authCode, :authCodeExpires)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $authCode The authorization code
     * @param  int $expireTime Unix timestamp of the access token expiry time
     * @return int                The auth code ID
     */
    public function associateAuthCode($sessionId, $authCode, $expireTime)
    {
        $this->db->insert(
            $this->tables['oauth_session_authcodes'],
            [$sessionId, $authCode, $expireTime],
            ['session_id', 'auth_code', 'auth_code_expires']
        );
        return $this->db->lastInsertId();
    }

    /**
     * Remove an associated authorization token from a session
     *
     * Example SQL query:
     *
     * <code>
     * DELETE FROM oauth_session_authcodes WHERE session_id = :sessionId
     * </code>
     *
     * @param  int $sessionId The session ID
     * @return void
     */
    public function removeAuthCode($sessionId)
    {
        $this->db->delete(
            $this->tables['oauth_session_authcodes'],
            'session_id = :sessionId',
            ['sessionId' => $sessionId]
        );
    }

    /**
     * Validate an authorization code
     *
     * Example SQL query:
     *
     * <code>
     * SELECT oauth_sessions.id AS session_id, oauth_session_authcodes.id AS authcode_id FROM oauth_sessions
     *  JOIN oauth_session_authcodes ON oauth_session_authcodes.`session_id` = oauth_sessions.id
     *  JOIN oauth_session_redirects ON oauth_session_redirects.`session_id` = oauth_sessions.id WHERE
     * oauth_sessions.client_id = :clientId AND oauth_session_authcodes.`auth_code` = :authCode
     *  AND `oauth_session_authcodes`.`auth_code_expires` >= :time AND
     *  `oauth_session_redirects`.`redirect_uri` = :redirectUri
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array(
     *     'session_id' =>  (int)
     *     'authcode_id'  =>  (int)
     * )
     * </code>
     *
     * @param  string $clientId The client ID
     * @param  string $redirectUri The redirect URI
     * @param  string $authCode The authorization code
     * @return array|bool              False if invalid or array as above
     */
    public function validateAuthCode($clientId, $redirectUri, $authCode)
    {
        $oS = $this->db->escapeIdentifier($this->tables['oauth_sessions']);
        $oSA = $this->db->escapeIdentifier($this->tables['oauth_session_authcodes']);
        $oSR = $this->db->escapeIdentifier($this->tables['oauth_session_redirects']);

        $row = $this->db->fetchOne(
            "SELECT $oS.id AS session_id, $oSA.id AS authcode_id
            FROM $oS
            JOIN $oSA
            ON $oSA.session_id = $oS.id
            JOIN $oSR
            ON $oSR.session_id = $oS.id
            WHERE $oS.client_id = :clientId
            AND $oSA.auth_code = :authCode
            AND $oSA.auth_code_expires >= :time
            AND $oSR.redirect_uri = :redirectUri",
            Db::FETCH_ASSOC,
            ['clientId' => $clientId, 'redirect_uri' => $redirectUri, 'auth_code' => $authCode, 'time' => time()]
        );

        if (!empty($row)) {
            return $row;
        }
        return FALSE;
    }

    /**
     * Validate an access token
     *
     * Example SQL query:
     *
     * <code>
     * SELECT session_id, oauth_sessions.`client_id`, oauth_sessions.`owner_id`, oauth_sessions.`owner_type`
     *  FROM `oauth_session_access_tokens` JOIN oauth_sessions ON oauth_sessions.`id` = session_id WHERE
     *  access_token = :accessToken AND access_token_expires >= UNIX_TIMESTAMP(NOW())
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array(
     *     'session_id' =>  (int),
     *     'client_id'  =>  (string),
     *     'owner_id'   =>  (string),
     *     'owner_type' =>  (string)
     * )
     * </code>
     *
     * @param  string $accessToken The access token
     * @return array|bool              False if invalid or an array as above
     */
    public function validateAccessToken($accessToken)
    {
        $row = $this->db->fetchOne(
            'SELECT session_id, oauth_sessions.client_id, oauth_sessions.owner_id, oauth_sessions.owner_type
              FROM oauth_session_access_tokens JOIN oauth_sessions ON oauth_sessions.id = session_id WHERE
              access_token = :accessToken AND access_token_expires >= UNIX_TIMESTAMP(NOW())',
            Db::FETCH_ASSOC,
            ['accessToken' => $accessToken]
        );
        if (empty($row))
            return FALSE;
        return $row;
    }

    /**
     * Removes a refresh token
     *
     * Example SQL query:
     *
     * <code>
     * DELETE FROM `oauth_session_refresh_tokens` WHERE refresh_token = :refreshToken
     * </code>
     *
     * @param  string $refreshToken The refresh token to be removed
     * @return void
     */
    public function removeRefreshToken($refreshToken)
    {
        $this->db->delete(
            $this->tables['oauth_session_refresh_tokens'],
            "refresh_token = :refreshToken",
            ['refreshToken' => $refreshToken]
        );
    }

    /**
     * Validate a refresh token
     *
     * Example SQL query:
     *
     * <code>
     * SELECT session_access_token_id FROM `oauth_session_refresh_tokens` WHERE refresh_token = :refreshToken
     *  AND refresh_token_expires >= UNIX_TIMESTAMP(NOW()) AND client_id = :clientId
     * </code>
     *
     * @param  string $refreshToken The refresh token
     * @param  string $clientId The client ID
     * @return int|bool               The ID of the access token the refresh token is linked to (or false if invalid)
     */
    public function validateRefreshToken($refreshToken, $clientId)
    {
        $row = $this->db->fetchOne(
            "SELECT session_access_token_id FROM oauth_session_refresh_tokens " .
            "WHERE refresh_token = :refreshToken AND refresh_token_expires >= UNIX_TIMESTAMP(NOW()) AND client_id = :clientId",
            Db::FETCH_ASSOC,
            ['refreshToken' => $refreshToken, 'clientId' => $clientId]
        );

        if (!empty($row))
            return $row['session_access_token_id'];
        return FALSE;
    }

    /**
     * Get an access token by ID
     *
     * Example SQL query:
     *
     * <code>
     * SELECT * FROM `oauth_session_access_tokens` WHERE `id` = :accessTokenId
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array(
     *     'id' =>  (int),
     *     'session_id' =>  (int),
     *     'access_token'   =>  (string),
     *     'access_token_expires'   =>  (int)
     * )
     * </code>
     *
     * @param  int $accessTokenId The access token ID
     * @return array
     */
    public function getAccessToken($accessTokenId)
    {
        return $this->db->fetchOne(
            'SELECT * FROM oauth_session_access_tokens WHERE id = :accessTokenId',
            Db::FETCH_ASSOC,
            ['accessTokenId' => $accessTokenId]
        );
    }

    /**
     * Associate scopes with an auth code (bound to the session)
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO `oauth_session_authcode_scopes` (`oauth_session_authcode_id`, `scope_id`) VALUES
     *  (:authCodeId, :scopeId)
     * </code>
     *
     * @param  int $authCodeId The auth code ID
     * @param  int $scopeId The scope ID
     * @return void
     */
    public function associateAuthCodeScope($authCodeId, $scopeId)
    {
        $this->db->insert(
            $this->tables['oauth_session_authcode_scopes'],
            [$authCodeId, $scopeId],
            ['oauth_session_authcode_id', 'scope_id']
        );
    }

    /**
     * Get the scopes associated with an auth code
     *
     * Example SQL query:
     *
     * <code>
     * SELECT scope_id FROM `oauth_session_authcode_scopes` WHERE oauth_session_authcode_id = :authCodeId
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array(
     *     array(
     *         'scope_id' => (int)
     *     ),
     *     array(
     *         'scope_id' => (int)
     *     ),
     *     ...
     * )
     * </code>
     *
     * @param  int $oauthSessionAuthCodeId The session ID
     * @return array
     */
    public function getAuthCodeScopes($oauthSessionAuthCodeId)
    {
        return $this->db->fetchAll(
            'SELECT scope_id FROM oauth_session_authcode_scopes WHERE oauth_session_authcode_id = :authCodeId',
            Db::FETCH_ASSOC,
            ['authCodeId' => $oauthSessionAuthCodeId]
        );
    }

    /**
     * Associate a scope with an access token
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO `oauth_session_token_scopes` (`session_access_token_id`, `scope_id`) VALUE (:accessTokenId, :scopeId)
     * </code>
     *
     * @param  int $accessTokenId The ID of the access token
     * @param  int $scopeId The ID of the scope
     * @return void
     */
    public function associateScope($accessTokenId, $scopeId)
    {
        $this->db->insert(
            $this->tables['oauth_session_token_scopes'],
            [$accessTokenId, $scopeId],
            ['session_access_token_id', 'scope_id']
        );
    }

    /**
     * Get all associated access tokens for an access token
     *
     * Example SQL query:
     *
     * <code>
     * SELECT oauth_scopes.* FROM oauth_session_token_scopes JOIN oauth_session_access_tokens
     *  ON oauth_session_access_tokens.`id` = `oauth_session_token_scopes`.`session_access_token_id`
     *  JOIN oauth_scopes ON oauth_scopes.id = `oauth_session_token_scopes`.`scope_id`
     *  WHERE access_token = :accessToken
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array (
     *     array(
     *         'id'     =>  (int),
     *         'scope'  =>  (string),
     *         'name'   =>  (string),
     *         'description'    =>  (string)
     *     ),
     *     ...
     *     ...
     * )
     * </code>
     *
     * @param  string $accessToken The access token
     * @return array
     */
    public function getScopes($accessToken)
    {
        return $this->db->fetchAll(
            'SELECT oauth_scopes.* FROM oauth_session_token_scopes JOIN oauth_session_access_tokens
            ON oauth_session_access_tokens.id = oauth_session_token_scopes.session_access_token_id
            JOIN oauth_scopes ON oauth_scopes.id = oauth_session_token_scopes.scope_id
            WHERE access_token = :accessToken',
            Db::FETCH_ASSOC,
            ['accessToken' => $accessToken]
        );
    }
}

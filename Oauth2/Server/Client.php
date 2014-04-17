<?php
namespace Sum\Oauth2\Server\Storage\Pdo\Mysql;

use League\OAuth2\Server\Storage\ClientInterface;
use Phalcon\Db;
use Phalcon\Mvc\Model\Resultset;

class Client implements ClientInterface
{

    protected $db;
    protected $tables = [
        'oauth_clients'          => 'oauth_clients',
        'oauth_client_endpoints' => 'oauth_client_endpoints',
    ];

    /**
     * @param $db \Phalcon\Db\Adapter\Pdo
     * @param array $tables
     */
    public function __construct($db, $tables = array())
    {
        $this->db = $db;
        if ($tables)
            foreach ($tables as $key => $table) {
                $this->tables[$key] = $table;
            }
    }


    /**
     * Validate a client
     *
     * Example SQL query:
     *
     * <code>
     * # Client ID + redirect URI
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name,
     * oauth_clients.auto_approve
     *  FROM oauth_clients LEFT JOIN oauth_client_endpoints ON oauth_client_endpoints.client_id = oauth_clients.id
     *  WHERE oauth_clients.id = :clientId AND oauth_client_endpoints.redirect_uri = :redirectUri
     *
     * # Client ID + client secret
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_clients.name, oauth_clients.auto_approve FROM oauth_clients
     * WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret
     *
     * # Client ID + client secret + redirect URI
     * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name,
     * oauth_clients.auto_approve FROM oauth_clients LEFT JOIN oauth_client_endpoints
     * ON oauth_client_endpoints.client_id = oauth_clients.id
     * WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret AND
     * oauth_client_endpoints.redirect_uri = :redirectUri
     * </code>
     *
     * Response:
     *
     * <code>
     * Array
     * (
     *     [client_id] => (string) The client ID
     *     [client secret] => (string) The client secret
     *     [redirect_uri] => (string) The redirect URI used in this request
     *     [name] => (string) The name of the client
     *     [auto_approve] => (bool) Whether the client should auto approve
     * )
     * </code>
     *
     * @param  string $clientId The client's ID
     * @param  string $clientSecret The client's secret (default = "null")
     * @param  string $redirectUri The client's redirect URI (default = "null")
     * @param  string $grantType The grant type used in the request (default = "null")
     * @return bool|array               Returns false if the validation fails, array on success
     */
    public function getClient($clientId, $clientSecret = NULL, $redirectUri = NULL, $grantType = NULL)
    {
        if ($clientSecret AND $redirectUri) {
            $row = $this->db->fetchOne(
                'SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name,' .
                'oauth_clients.auto_approve FROM oauth_clients LEFT JOIN oauth_client_endpoints ON ' .
                'oauth_client_endpoints.client_id = oauth_clients.id ' .
                'WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret AND ' .
                'oauth_client_endpoints.redirect_uri = :redirectUri',
                Db::FETCH_ASSOC,
                ['clientId' => $clientId, 'clientSecret' => $clientSecret, 'redirectUri' => $redirectUri]
            );
        } else if ($clientSecret) {
            $row = $this->db->fetchOne(
                'SELECT oauth_clients.id, oauth_clients.secret, oauth_clients.name, oauth_clients.auto_approve ' .
                'FROM oauth_clients WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret',
                Db::FETCH_ASSOC,
                ['clientId' => $clientId, 'clientSecret' => $clientSecret]
            );
        } elseif ($redirectUri) {
            $row = $this->db->fetchOne(
                'SELECT ' .
                'c.id, c.secret, e.redirect_uri, c.name, c.auto_approve ' .
                'FROM oauth_clients c ' .
                'LEFT JOIN oauth_client_endpoints e ' .
                'ON e.client_id = c.id ' .
                'WHERE c.id = :clientId AND e.redirect_uri = :redirectUri',
                Db::FETCH_ASSOC,
                ['clientId' => $clientId, 'redirectUri' => $redirectUri]
            );
        } else {
            $row = $this->db->fetchOne(
                'SELECT * FROM oauth_clients WHERE id = :clientId',
                DB::FETCH_ASSOC,
                ['clientId' => $clientId]
            );
        }
        if (empty($row))
            return FALSE;
        return $row;
    }
}

<?php
namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo;

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
    public function __construct($db, $tables = [])
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

    public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $cols = 'c.id, c.secret, c.name, c.auto_approve';
        $join = '';
        $cond = 'WHERE c.id = :clientId AND ';

        if ($clientSecret) {
            $cond .= "c.secret = :clientSecret ";
        }
        if ($redirectUri) {
            $cols .= ',e.redirect_uri';
            $join .= 'LEFT JOIN oauth_client_endpoints e ON e.client_id = c.id';
            $cond .= 'AND e.redirect_uri = :redirectUri';
        }
        return $this->db->fetchOne(
            "SELECT $cols FROM oauth_clients c $join $cond",
            Db::FETCH_ASSOC,
            array_filter(
                compact('clientId', 'clientSecret', 'redirectUri')
            )
        );
    }
}

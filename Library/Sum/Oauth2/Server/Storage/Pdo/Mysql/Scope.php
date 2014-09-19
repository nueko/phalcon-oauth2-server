<?php
namespace Sum\Oauth2\Server\Storage\Pdo\Mysql;

use League\OAuth2\Server\Storage\ScopeInterface;
use Phalcon\Db;

class Scope implements ScopeInterface
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
     * Return information about a scope
     *
     * Example SQL query:
     *
     * <code>
     * SELECT * FROM oauth_scopes WHERE scope = :scope
     * </code>
     *
     * Response:
     *
     * <code>
     * Array
     * (
     *     [id] => (int) The scope's ID
     *     [scope] => (string) The scope itself
     *     [name] => (string) The scope's name
     *     [description] => (string) The scope's description
     * )
     * </code>
     *
     * @param  string $scope The scope
     * @param  string $clientId The client ID (default = "null")
     * @param  string $grantType The grant type used in the request (default = "null")
     * @return bool|array If the scope doesn't exist return false
     */
    public function getScope($scope, $clientId = NULL, $grantType = NULL)
    {
        $row = $this->db->fetchOne(
            'SELECT * FROM oauth_scopes WHERE scope = :scope',
            Db::FETCH_ASSOC,
            ['scope' => $scope]
        );

        if (empty($row))
            return FALSE;
        return $row;
    }
}

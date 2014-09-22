<?php

namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo\Standalone;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;
use Phalcon\Db;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite $db
 */
class Scope implements ScopeInterface
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
     * Return information about a scope
     * @param  string $scope The scope
     * @param  string $grantType The grant type used in the request (default = "null")
     * @return \League\OAuth2\Server\Entity\ScopeEntity
     */
    public function get($scope, $grantType = NULL)
    {
        $result = $this->db->fetchAll("SELECT * FROM oauth_scopes WHERE id = ?", Db::FETCH_ASSOC, [$scope]);

        if (count($result) === 0) {
            return null;
        }

        return (new ScopeEntity($this->server))->hydrate([
            'id'            =>  $result[0]['id'],
            'description'   =>  $result[0]['description']
        ]);

    }

}
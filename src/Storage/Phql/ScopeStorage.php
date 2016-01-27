<?php

namespace Phalcon\OAuth2\Server\Storage\Phql;

use Phalcon\OAuth2\Server\Component;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;
use Phalcon\OAuth2\Server\Models\ClientScope;
use Phalcon\OAuth2\Server\Models\GrantScope;
use Phalcon\OAuth2\Server\Models\Scope;

/**
 * Class ScopeStorage
 * @package Phalcon\OAuth2\Server\Storage
 */
class ScopeStorage extends Component implements ScopeInterface
{
    /*
         * Limit clients to scopes.
         *
         * @var bool
         */
    protected $limitClientsToScopes = false;

    /*
     * Limit scopes to grants.
     *
     * @var bool
     */
    protected $limitScopesToGrants = false;

    /**
     * Create a new fluent scope instance.
     *
     * @param bool|false $limitClientsToScopes
     * @param bool|false $limitScopesToGrants
        public function __construct(Resolver $resolver, $limitClientsToScopes = false, $limitScopesToGrants = false)
        {
            $this->limitClientsToScopes = $limitClientsToScopes;
            $this->limitScopesToGrants = $limitScopesToGrants;
        }
     */

    /**
     * Set limit clients to scopes.
     *
     * @param bool|false $limit
     */
    public function limitClientsToScopes($limit = false)
    {
        $this->limitClientsToScopes = $limit;
    }

    /**
     * Set limit scopes to grants.
     *
     * @param bool|false $limit
     */
    public function limitScopesToGrants($limit = false)
    {
        $this->limitScopesToGrants = $limit;
    }

    /**
     * Check if clients are limited to scopes.
     *
     * @return bool|false
     */
    public function areClientsLimitedToScopes()
    {
        return $this->limitClientsToScopes;
    }

    /**
     * Check if scopes are limited to grants.
     *
     * @return bool|false
     */
    public function areScopesLimitedToGrants()
    {
        return $this->limitScopesToGrants;
    }

    /**
     * Return information about a scope.
     *
     * Example SQL query:
     *
     * <code>
     * SELECT * FROM oauth_scopes WHERE scope = :scope
     * </code>
     *
     * @param string $scope The scope
     * @param string $grantType The grant type used in the request (default = "null")
     * @param string $clientId The client id used for the request (default = "null")
     *
     * @return \League\OAuth2\Server\Entity\ScopeEntity|null If the scope doesn't exist return false
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
        /** @type Scope $result */

        $query = $this->getBuilder()->from(['scope' => Scope::class])
            ->where('scope.id = :scope:', compact('scope'));

        if ($this->limitClientsToScopes === true && !is_null($clientId)) {
            $query = $query->join(ClientScope::class, 'scope.id = client.scope_id', 'client')
                ->where('client.client_id = :clientId: ', compact('clientId'));
        }

        if ($this->limitScopesToGrants === true && !is_null($grantType)) {
            $query = $query->join(GrantScope::class, 'scope.id = grant.grant_id', 'grant')
                ->where('grant.id = :grantId: ', compact('grantId'));
        }

        $result = $query->getQuery()->getSingleResult();

        if (! $result) {
            return null;
        }

        $scope = new ScopeEntity($this->getServer());
        $scope->hydrate([
            'id'          => $result->id,
            'description' => $result->description,
        ]);

        return $scope;
    }
}
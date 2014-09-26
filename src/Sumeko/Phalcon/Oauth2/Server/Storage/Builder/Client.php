<?php
namespace Sumeko\Phalcon\Oauth2\Server\Storage\Builder;

use Sumeko\Phalcon\Oauth2\Server\Model\OauthClients;
use Sumeko\Phalcon\Oauth2\Server\Model\OauthClientEndpoints;

class Client extends \Phalcon\Mvc\User\Component implements \League\OAuth2\Server\Storage\ClientInterface
{

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
        $client = $this->modelsManager->createBuilder();
        $columns = ['c.id', 'c.secret', 'c.name', 'c.auto_approve'];
        $client->from(["c" => 'Sumeko\Phalcon\Oauth2\Server\Model\OauthClients'])
            ->where("c.id = :clientId:", compact('clientId'));
        if($clientSecret)
            $client->andWhere("c.secret = :clientSecret:", compact('clientSecret'));
        if ($redirectUri) {
            array_push($columns, 'e.redirect_uri');
            $client->join('Sumeko\Phalcon\Oauth2\Server\Model\OauthClientEndpoints', 'c.id = e.client_id', 'e')
                ->andWhere('e.redirect_uri = :redirectUri:', compact('redirectUri'));
        }
        if ($client = $client->columns($columns)->getQuery()->getSingleResult()) {
            return $client->toArray();
        }
        return false;
    }
}
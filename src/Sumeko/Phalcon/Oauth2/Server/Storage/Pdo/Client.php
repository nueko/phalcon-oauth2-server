<?php

namespace Sumeko\Phalcon\Oauth2\Server\Storage\Pdo;


use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use Phalcon\Db;
use Phalcon\Mvc\User\Plugin;
use Sumeko\Phalcon\Oauth2\Server\Storage\AdapterTrait;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Sqlite db
 */
class Client extends Plugin implements ClientInterface
{
    use AdapterTrait;

    public function get($clientId, $clientSecret = NULL, $redirectUri = NULL, $grantType = NULL)
    {
        $cols = ["oauth_clients.*"];
        $joins = "";
        $params['id'] = $clientId;
        $where = ['oauth_clients.id = :id'];
        if ($clientSecret !== NULL) {
            $params['secret'] = $clientSecret;
            $where[] = "oauth_clients.secret = :secret";
        }

        if ($redirectUri) {
            $cols[] = "oauth_client_redirect_uris.redirect_uri, oauth_client_redirect_uris.client_id";
            $joins = "JOIN oauth_client_redirect_uris on oauth_clients.id = oauth_client_redirect_uris.client_id ";
            $params['redirect'] = $redirectUri;
            $where[] = "oauth_client_redirect_uris.redirect_uri = :redirect";
        }

        $query = sprintf("SELECT %s FROM oauth_clients $joins WHERE %s", join(",", $cols), join(" AND ", $where));
        $result = $this->db->fetchAll($query, Db::FETCH_ASSOC, $params);
        //echo($query) .PHP_EOL;
        //dump($result);

        if (count($result) === 1) {
            $client = new ClientEntity($this->server);
            $client->hydrate([
                'id'   => $result[0]['id'],
                'name' => $result[0]['name']
            ]);

            return $client;
        }

        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySession(SessionEntity $session)
    {
        $result = $this->db->fetchAll(
            "SELECT c.id, c.name FROM oauth_clients c JOIN oauth_sessions s ON c.id = s.client_id WHERE s.id = ?",
            Db::FETCH_ASSOC,
            [$session->getId()]
        );

        if (count($result) === 1) {
            $client = new ClientEntity($this->server);
            $client->hydrate([
                'id'   => $result[0]['id'],
                'name' => $result[0]['name']
            ]);

            return $client;
        }

        return NULL;
    }

} 
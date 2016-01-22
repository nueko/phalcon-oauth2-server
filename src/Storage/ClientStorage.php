<?php

namespace Phalcon\OAuth2\Server\Storage;

use League\OAuth2\Server\Entity\ClientEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\ClientInterface;
use Phalcon\OAuth2\Server\Models\Client;
use Phalcon\OAuth2\Server\Models\ClientEndpoint;
use Phalcon\OAuth2\Server\Models\ClientGrant;
use Phalcon\OAuth2\Server\Models\Grant;
use Phalcon\OAuth2\Server\Models\Session;

class ClientStorage extends Component implements ClientInterface
{
    /**
     * Limit clients to grants.
     *
     * @var bool
     */
    protected $limitClientsToGrants = false;

    /**
     * Create a new fluent client instance.
     *
     * @param bool $limitClientsToGrants
     */

    public function __construct($limitClientsToGrants = false)
    {
        $this->limitClientsToGrants = $limitClientsToGrants;
    }

    /**
     * Check if clients are limited to grants.
     *
     * @return bool
     */
    public function areClientsLimitedToGrants()
    {
        return $this->limitClientsToGrants;
    }

    /**
     * Whether or not to limit clients to grants.
     *
     * @param bool $limit
     */
    public function limitClientsToGrants($limit = false)
    {
        $this->limitClientsToGrants = $limit;
    }

    /**
     * Get the client.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $grantType
     *
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        $columns = ['c.id', 'c.secret', 'c.name'];
        $sql = $this->getBuilder()
            ->from(['c' => Client::class])
            ->where('c.id = :cid:', ['cid' => $clientId]);

        if ($clientSecret) {
            $sql->andWhere('c.secret = :sec:', ['sec' => $clientSecret]);
        }

        if ($redirectUri) {
            $columns[] = 'e.redirect_uri';

            $sql->join(ClientEndpoint::class, 'c.id = e.client_id', 'e');
            $sql->join(ClientEndpoint::class, 'c.id = e.client_id', 'e')
                ->andWhere('e.redirect_uri = :uri:', ['uri' => $redirectUri]);
        }


        if ($this->limitClientsToGrants === true && !is_null($grantType)) {
            $sql->join(ClientGrant::class, 'c.id = cg.client_id', 'cg')
                ->join(Grant::class, 'g.id = cg.grant_id', 'g')
                ->where('g.id = :gtype:', ['gtype' => $grantType]);
        }

        $result = $sql->columns($columns)->getQuery()->getSingleResult();

        return $this->hydrateEntity($result);

    }

    /**
     * Get the client associated with a session.
     *
     * @param  \League\OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return null|\League\OAuth2\Server\Entity\ClientEntity
     */
    public function getBySession(SessionEntity $session)
    {
        $result = $this->getBuilder()->from(['c' => Client::class])
            ->join(Session::class, 's.client_id = c.id', 's')
            ->where('s.id = :id:', ['id' => $session->getId()])
            ->getQuery()->getSingleResult();

        return $this->hydrateEntity($result);
    }

    /**
     * Create a new client.
     *
     * @param string $name The client's unique name
     * @param string $id The client's unique id
     * @param string $secret The clients' unique secret
     *
     * @return void
     */
    public function create($name, $id, $secret)
    {
        $client = new Client();
        $client->save([
            'id'     => $id,
            'name'   => $name,
            'secret' => $secret,
        ]);
    }

    /**
     * Hydrate the entity.
     *
     * @param \Phalcon\Mvc\Model $result
     *
     * @return \League\OAuth2\Server\Entity\ClientEntity | null
     */
    protected function hydrateEntity($result)
    {
        if (!$result) {
            return null;
        }

        $client = new ClientEntity($this->getServer());
        $client->hydrate($result->toArray());

        return $client;
    }
}
<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class Client
 * @package Phalcon\OAuth2\Server\Models
 */
class Client extends OAuth
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var string
     */
    public $secret;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $created_at;

    /**
     *
     * @var integer
     */
    public $updated_at;

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Client[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Client
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', ClientEndpoint::class, 'client_id');
        $this->hasMany('id', ClientGrant::class, 'client_id');
        $this->hasMany('id', ClientScope::class, 'client_id');
        $this->hasMany('id', Session::class, 'client_id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_clients';
    }

}

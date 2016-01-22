<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class ClientGrant
 * @package Phalcon\OAuth2\Server\Models
 */
class ClientGrant extends OAuth
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $client_id;

    /**
     *
     * @var string
     */
    public $grant_id;

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
     * @return ClientGrant[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ClientGrant
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
        $this->belongsTo('client_id', Client::class, 'id');
        $this->belongsTo('grant_id', Grant::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_client_grants';
    }

}

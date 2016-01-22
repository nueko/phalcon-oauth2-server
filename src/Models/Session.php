<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class Session
 * @package Phalcon\OAuth2\Server\Models
 */
class Session extends OAuth
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
    public $owner_type;

    /**
     *
     * @var string
     */
    public $owner_id;

    /**
     *
     * @var string
     */
    public $client_redirect_uri;

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
     * @return Session[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Session
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
        $this->hasMany('id', AccessToken::class, 'session_id');
        $this->hasMany('id', AuthCode::class, 'session_id');
        $this->hasMany('id', SessionScope::class, 'session_id');
        $this->belongsTo('client_id', Client::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_sessions';
    }

}

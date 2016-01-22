<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class AuthCode
 * @package Phalcon\OAuth2\Server\Models
 */
class AuthCode extends OAuth
{

    /**
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $session_id;

    /**
     *
     * @var string
     */
    public $redirect_uri;

    /**
     *
     * @var integer
     */
    public $expire_time;

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
     * @return AuthCode[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AuthCode
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
        $this->hasMany('id', AuthCodeScope::class, 'auth_code_id');
        $this->belongsTo('session_id', Session::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_auth_codes';
    }

}

<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class AccessToken
 * @package Phalcon\OAuth2\Server\Models
 */
class AccessToken extends OAuth
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
     * @return AccessToken[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return AccessToken
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
        $this->hasMany('id', AccessTokenScope::class, 'access_token_id');
        $this->hasMany('id', RefreshToken::class, 'access_token_id');
        $this->belongsTo('session_id', Session::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_access_tokens';
    }

}

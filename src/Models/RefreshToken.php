<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class RefreshToken
 * @package Phalcon\OAuth2\Server\Models
 */
class RefreshToken extends OAuth
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
    public $access_token_id;

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
     * @return RefreshToken[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RefreshToken
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
        $this->belongsTo('access_token_id', AccessToken::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_refresh_tokens';
    }

}

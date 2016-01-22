<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class Grant
 * @package Phalcon\OAuth2\Server\Models
 */
class Grant extends OAuth
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
     * @return Grant[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Grant
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
        $this->hasMany('id', ClientGrant::class, 'grant_id');
        $this->hasMany('id', GrantScope::class, 'grant_id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_grants';
    }

}

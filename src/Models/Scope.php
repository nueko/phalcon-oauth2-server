<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class Scope
 * @package Phalcon\OAuth2\Server\Models
 */
class Scope extends OAuth
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
    public $description;

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
     * @return Scope[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Scope
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
        $this->hasMany('id', AccessTokenScope::class, 'scope_id');
        $this->hasMany('id', AuthCodeScope::class, 'scope_id');
        $this->hasMany('id', ClientScope::class, 'scope_id');
        $this->hasMany('id', GrantScope::class, 'scope_id');
        $this->hasMany('id', SessionScope::class, 'scope_id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_scopes';
    }

}

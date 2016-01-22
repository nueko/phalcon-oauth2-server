<?php

namespace Phalcon\OAuth2\Server\Models;

/**
 * Class SessionScope
 * @package Phalcon\OAuth2\Server\Models
 */
class SessionScope extends OAuth
{

    /**
     *
     * @var integer
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
    public $scope_id;

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
     * @return SessionScope[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SessionScope
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
        $this->belongsTo('scope_id', Scope::class, 'id');
        $this->belongsTo('session_id', Session::class, 'id');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'oauth_session_scopes';
    }

}

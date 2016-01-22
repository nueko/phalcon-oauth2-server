<?php

namespace Phalcon\OAuth2\Server\Models;

use Phalcon\Mvc\Model;

/**
 * Class OAuth
 * @package Phalcon\OAuth2\Server\Models
 */
abstract class OAuth extends Model
{

    /**
     * Insert value for created and updated at column
     */
    public function beforeSave()
    {
        $this->created_at = time();
        $this->updated_at = time();
    }

    /**
     * Update value for updated at column
     */
    public function beforeUpdate()
    {
        $this->updated_at = time();
    }

}
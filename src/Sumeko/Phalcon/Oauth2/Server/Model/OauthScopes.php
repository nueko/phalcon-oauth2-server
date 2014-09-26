<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthScopes extends \Phalcon\Mvc\Model
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
    public $scope;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    public function getSource()
    {
        return 'oauth_scopes';
    }

}

<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthClients extends \Phalcon\Mvc\Model
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
    public $secret;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $auto_approve;

    public function getSource()
    {
        return 'oauth_clients';
    }

}

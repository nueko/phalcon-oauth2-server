<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthClientEndpoints extends \Phalcon\Mvc\Model
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
    public $redirect_uri;

    public function getSource()
    {
        return 'oauth_client_endpoints';
    }

}

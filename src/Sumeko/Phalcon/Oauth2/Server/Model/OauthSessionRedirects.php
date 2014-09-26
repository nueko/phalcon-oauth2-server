<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionRedirects extends \Phalcon\Mvc\Model
{

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

    public function getSource()
    {
        return 'oauth_session_redirects';
    }

}

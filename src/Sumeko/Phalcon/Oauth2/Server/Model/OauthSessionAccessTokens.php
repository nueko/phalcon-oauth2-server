<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionAccessTokens extends \Phalcon\Mvc\Model
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
    public $access_token;

    /**
     *
     * @var integer
     */
    public $access_token_expires;

    public function getSource()
    {
        return 'oauth_session_access_tokens';
    }

}

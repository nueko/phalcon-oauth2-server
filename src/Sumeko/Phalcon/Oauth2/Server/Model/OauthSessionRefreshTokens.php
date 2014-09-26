<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionRefreshTokens extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $session_access_token_id;

    /**
     *
     * @var string
     */
    public $refresh_token;

    /**
     *
     * @var integer
     */
    public $refresh_token_expires;

    /**
     *
     * @var string
     */
    public $client_id;

    public function getSource()
    {
        return 'oauth_session_refresh_tokens';
    }

}

<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionAuthcodes extends \Phalcon\Mvc\Model
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
    public $auth_code;

    /**
     *
     * @var integer
     */
    public $auth_code_expires;

    public function getSource()
    {
        return 'oauth_session_authcodes';
    }

}

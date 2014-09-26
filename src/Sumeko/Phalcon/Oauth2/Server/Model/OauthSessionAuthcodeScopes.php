<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionAuthcodeScopes extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $oauth_session_authcode_id;

    /**
     *
     * @var integer
     */
    public $scope_id;

    public function getSource()
    {
        return 'oauth_session_authcode_scopes';
    }

}

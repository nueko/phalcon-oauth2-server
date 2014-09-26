<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessionTokenScopes extends \Phalcon\Mvc\Model
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
    public $session_access_token_id;

    /**
     *
     * @var integer
     */
    public $scope_id;

    public function getSource()
    {
        return 'oauth_session_token_scopes';
    }

}

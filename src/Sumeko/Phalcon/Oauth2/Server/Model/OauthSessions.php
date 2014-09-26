<?php

namespace Sumeko\Phalcon\Oauth2\Server\Model;

class OauthSessions extends \Phalcon\Mvc\Model
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
    public $owner_type;

    /**
     *
     * @var string
     */
    public $owner_id;

    public function getSource()
    {
        return 'oauth_sessions';
    }

}

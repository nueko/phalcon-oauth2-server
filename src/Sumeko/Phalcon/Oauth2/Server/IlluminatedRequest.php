<?php
namespace Sumeko\Phalcon\Oauth2\Server;

use Phalcon\Mvc\User\Component;

class IlluminatedRequest extends Component
{
    public  $query;

    public function __construct()
    {
        $this->query = $this->request;
    }
    /**
     * Returns the user.
     *
     * @return string|null
     */
    public function getUser()
    {
        return $this->request->getServer('PHP_AUTH_USER');
    }

    /**
     * Returns the password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->request->getServer('PHP_AUTH_PW');
    }

    public function get($index = NULL)
    {
        return $this->request->getQuery($index);
    }

    public function post($index = NULL)
    {
        return $this->request->getPost($index);
    }

    public function cookie($index = NULL)
    {
        return $this->cookies->get($index);
    }

    public function file($index = NULL)
    {
        if (is_null($index) && $this->request->hasFiles())
            return $_FILES;
        return isset($_FILES[$index]) ? $_FILES[$index] : NULL;
    }

    public function server($index = NULL)
    {
        return $this->request->getServer($index);
    }

    public function header($index = NULL)
    {
        return $this->request->getHeader($index);
    }
}

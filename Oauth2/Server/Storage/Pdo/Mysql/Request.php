<?php
namespace Sum\Oauth2\Server\Storage\Pdo\Mysql;

use League\OAuth2\Server\Util\RequestInterface;
use Phalcon\Http\Response\Cookies;

class Request implements RequestInterface
{

    public $request;

    public function __construct()
    {
        $this->request = new \Phalcon\Http\Request();
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
        $cook = new Cookies();
        return $cook->get($index);
    }

    public function file($index = NULL)
    {
        if (is_null($index))
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

<?php
namespace Sum\Oauth2\Server;

use League\OAuth2\Server\Util\RequestInterface;
use Phalcon\Http\Response\Cookies;

class Request implements RequestInterface {

    public $request;
    
    public function __construct(){
        $this->request = new \Phalcon\Http\Request();
    }

    public function get($index = null)
    {
        return $this->request->getQuery($index);
    }

    public function post($index = null)
    {
        return $this->request->getPost($index);
    }

    public function cookie($index = null)
    {
        $cook = new Cookies();
        return $cook->get($index);
    }

    public function file($index = null)
    {
        if(is_null($index))
            return $_FILES;

        return isset($_FILES[$index]) ? $_FILES[$index] : null;
    }

    public function server($index = null)
    {
        return $this->request->getServer($index);
    }

    public function header($index = null)
    {
        return $this->request->getHeader($index);
    }
}

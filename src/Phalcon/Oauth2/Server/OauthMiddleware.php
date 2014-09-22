<?php

namespace Sumeko\Phalcon\Oauth2\Server;


use Phalcon\Mvc\Micro\MiddlewareInterface;

class OauthMiddleware implements MiddlewareInterface {

    /**
     * @param $application \Phalcon\Mvc\Micro
     * @return bool
     */
    public function call($application)
    {
        //dump($application->oauth->resource->isValidRequest(FALSE));die;
        try{
            $application->oauth->resource->isValidRequest(false);
            return true;
        } catch (\League\OAuth2\Server\Exception\OAuthException $e) {
            $application->response->setStatusCode($e->httpStatusCode, NULL)->sendHeaders();
            $application->response->setJsonContent([
                'error'   => $e->errorType,
                'message' => $e->getMessage()
            ]);
            echo $application->response->getContent();
        }
        return false;
    }
}
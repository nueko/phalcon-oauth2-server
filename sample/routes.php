<?php

/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo "Phalcon OAuth 2.0";
});

$app->get('/resource', function () use ($app) {
    $server = $app->resource;
    $response = $app->response;

    /** @type \League\OAuth2\Server\ResourceServer $server */

    try {
        // Check that an access token is present and is valid
        $server->isValidRequest();

        // A successful response
        echo "HELLO";

    } catch (\League\OAuth2\Server\Exception\OAuthException $e) {

        // Catch an OAuth exception
        $response->setStatusCode($e->httpStatusCode);
        foreach ($e->getHttpHeaders() as $header => $httpHeader) {
            $response->setHeader($header, $httpHeader);
        }
        $response->setJsonContent([
            'error'     =>  $e->errorType,
            'message'   =>  $e->getMessage()
        ]);


    } catch (\Phalcon\Http\Request\Exception $e) {
        // A failed response (thrown by code)
        $response->setJsonContent(['status_code' => $e->getCode(), 'message' => $e->getMessage()]);

    } catch (\Exception $e) {
        // Other server error (500)
        $response->setStatusCode(500);
        $response->setJsonContent(['status_code' => 500, 'message' => $e->getMessage()]);

    } finally {

        // Return the response
        $response->send();

    }

});

$app->post('/token', function () use ($app) {
    $server = $app->authorization;

    /** @type \League\OAuth2\Server\AuthorizationServer $server */

    try {
        $response = $server->issueAccessToken();
        $app->response->setJsonContent($response)
            ->setHeader('Cache-Control', 'no-store')
            ->setHeader('Pragma', 'no-store');

    } catch (\League\OAuth2\Server\Exception\OAuthException $e) {
        $app->response->setJsonContent([
            'error'   => $e->errorType,
            'message' => $e->getMessage(),
        ])->setStatusCode($e->httpStatusCode);
        foreach ($e->getHttpHeaders() as $key => $header) {
            $app->response->setHeader($key, $header);
        }

    }
    $app->response->send();

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404)->sendHeaders();
    echo "Lost!";
});

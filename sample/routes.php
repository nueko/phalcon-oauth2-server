<?php

/**
 * Add your routes here
 */
$app->get('/', function () {
    echo "Phalcon OAuth 2.0";
});

$app->get('/resource', function () use ($app) {
    /** @type \Phalcon\OAuth2\Server\Gateway $oauth */
    $oauth = $app->oauth;

    if ($oauth->validateAccessToken()) {
        echo "\n";
        echo "Hello, world";
        echo "\n";
    }
});

$app->post('/token', function () use ($app) {
    $oauth = $app->oauth;

    /** @type \Phalcon\OAuth2\Server\Gateway $oauth */
    $response = $oauth->issueAccessToken();
    $app->response->setJsonContent($response)
        ->setHeader('Cache-Control', 'no-store')
        ->setHeader('Pragma', 'no-store');

    $app->response->send();
});

$app->error(function (\Exception $e) use ($app) {
    if ($e instanceof \League\OAuth2\Server\Exception\OAuthException) {
        foreach ($e->getHttpHeaders() as $key => $header) {
            $app->response->setHeader($key, $header);
        }

        $app->response->setJsonContent([
            'error'   => $e->errorType,
            'message' => $e->getMessage(),
        ])->setStatusCode($e->httpStatusCode)->send();

        return false;
    }

    $app->response
        ->setStatusCode(500)
        ->setJsonContent(['status_code' => 500, 'message' => $e->getMessage()]);

    return false;
});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404)->sendHeaders();
    echo "Lost!";
});
<?php

/**
 * Add your routes here
 */
$app->get('/', function () use ($app) {
    $content = array_merge([
        'phalcon' => Phalcon\Version::get(),
    ], $app->request->getQuery());

    return $app->response->setJsonContent($content);
});

// GET /token-info
$app->get('/token-info', function () {
    /** @type \Phalcon\OAuth2\Server\Gateway $oauth */
    $oauth = $this->oauth;
    $accessToken = $oauth->getAccessToken();
    $session = $oauth->getAuthorization()->getSessionStorage()->getByAccessToken($accessToken);
    $token = [
        'owner_id'     => $session->getOwnerId(),
        'owner_type'   => $session->getOwnerType(),
        'access_token' => $accessToken,
        'client_id'    => $session->getClient()->getId(),
        'scopes'       => $accessToken->getScopes(),
    ];

    return $this->response->setJsonContent($token);
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

$app->map('/authorize', function () use ($app) {
    /** @type \Phalcon\OAuth2\Server\Gateway $oauth */
    $oauth = $app->oauth;
    $oauth->checkAuthCodeRequest();
    if ($app->request->isPost()) {
        if (!$app->request->has('authorization')) {
            // show form
        }

        // If the user authorizes the request then redirect the user back with an authorization code

        if ($app->request->getPost('authorization') === 'Approve') {

            $redirectUri = $oauth->issueAuthCode('user', 1);

            return $app->response->redirect($redirectUri)->send();

        } else {
            // The user denied the request so redirect back with a message
            $error = new \League\OAuth2\Server\Exception\AccessDeniedException();

            $redirectUri = \League\OAuth2\Server\Util\RedirectUri::make(
                $oauth->getAuthCodeRequestParam('redirect_uri'),
                [
                    'error'   => $error->errorType,
                    'message' => $error->getMessage(),
                ]
            );

            return $app->response->redirect($redirectUri, true);
        }
    }

    $signedUser = false;
    if ($signedUser) {
        return $app->response->redirect('/authorize');
    } else {
        $html = "<h1>{$oauth->getAuthCodeRequestParam('name')} would like to access:</h1>";
        $html .= "<ul>";
        foreach ($oauth->getAuthCodeRequestParam('scopes') as $scope) {
            /** @type \League\OAuth2\Server\Entity\ScopeEntity $scope */
            $html .= "<li>{$scope->getId()}:{$scope->getDescription()}</li>";
        }
        $html .= "</ul>";
        $html .= '<form method="post">' .
            '<input type="submit" value="Approve" name="authorization">' .
            '<input type="submit" value="Deny" name="authorization"></form>';

        return $app->response->setContent($html);
    }
})->via(['GET', 'POST']);

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
        if ($e->shouldRedirect()) {
            return $app->response->redirect($e->getRedirectUri());
        }

        foreach ($e->getHttpHeaders() as $key => $header) {
            $app->response->setHeader($key, $header);
        }

        return $app->response->setJsonContent([
            'error'   => $e->errorType,
            'message' => $e->getMessage(),
        ])->setStatusCode($e->httpStatusCode);
    }

    return $app->response
        ->setStatusCode(500)
        ->setJsonContent(['status_code' => 500, 'message' => $e->getMessage()]);

});

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404)->sendHeaders();
    echo "Lost!";
});
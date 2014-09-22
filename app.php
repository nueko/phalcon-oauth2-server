<?php

$app->get("/access_token", function () use ($app) {
    try {
        $response = $app->oauth->authorize->issueAccessToken();
        $app->oauth->setData($response);
    } catch (\Exception $e) {
        $app->oauth->catcher($e);
    }
});

$app->get('/authorize', function () use ($app) {
    /** @var \League\OAuth2\Server\Grant\AuthCodeGrant $codeGrant */
    $authParams = null;
    try {
        $codeGrant = $app->oauth->authorize->getGrantType('authorization_code');
        $authParams = $codeGrant->checkAuthorizeParams();
    } catch (\Exception $e) {
        return $app->oauth->catcher($e);
    }
    if ($authParams) {
        // Normally at this point you would show the user a sign-in screen and ask them to authorize the requested scopes
        // Create a new authorize request which will respond with a redirect URI that the user will be redirected to
        //echo($redirectUri);
        //$app->response->redirect($redirectUri,true)->sendHeaders();
        $redirectUri = $codeGrant->newAuthorizeRequest('client', "testclient", $authParams);
        return $redirectUri;
    }
});

$app->after(function () use ($app) {
    $returned = $app->getReturnedValue();
    $app->response->sendHeaders();
    if ($returned) {
        if(is_scalar($returned))
            echo $returned;
        else
            $app->oauth->setData($returned);

    }
    $app->response->send();
});

$app->finish(function () use ($app) {
    $app->oauth->cleanData();
});
<?php
/** @type \Codeception\Scenario $scenario */
$I = new ApiTester($scenario);

$I->wantTo('Get the token and use it on secured area');

$I->sendPOST('token', ['client_id' => 't1', 'client_secret' => 'test', 'grant_type' => 'client_credentials']);
$I->seeHttpHeader('Cache-Control', 'no-store');
$I->seeHttpHeader('Pragma', 'no-store');
$I->seeResponseJsonMatchesXpath('//access_token');
$response = json_decode($I->grabResponse());

$I->amBearerAuthenticated($response->access_token);
$I->sendGET('resource');
$I->seeResponseCodeIs(200);
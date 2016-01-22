<?php

/**
 * @type  \Codeception\Scenario $scenario $scenario
 */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that the server works');
$I->amOnPage('/');
$I->see('Phalcon OAuth');
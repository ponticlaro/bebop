<?php

$I = new WebGuy($scenario);
$I->wantTo('Sign into WordPress');
$I->amOnPage('/wp-login.php');
$I->fillField('form input[type="text"]', 'admin');
$I->fillField('form input[type="password"]','123456789');
$I->click('#wp-submit');
$I->seeCurrentUrlEquals('/wp-admin/');

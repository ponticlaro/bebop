<?php

$I = new WebGuy($scenario);
$I->wantTo('Sign into WordPress');
$I->amOnPage('/wp-login.php');
$I->fillField('#user_login', 'admin');
$I->fillField('#user_pass','123456789');
$I->click('#wp-submit');
$I->seeCurrentUrlEquals('/wp-admin/');

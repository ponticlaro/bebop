<?php
$I = new WebGuy($scenario);

$I->amOnPage(LoginPage::$URL);
$I->fillField(LoginPage::$usernameField, 'admin');
$I->fillField(LoginPage::$passwordField, '123456789');
$I->click(LoginPage::$submitButton);

$I->wantTo('check if custom admin pages are created')
  ->amOnPage('/wp-admin/admin.php?page=custom-admin-page')
  ->see('Custom Admin Page', 'h2');
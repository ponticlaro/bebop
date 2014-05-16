<?php
$I = new WebGuy($scenario);

$I->amOnPage(LoginPage::$URL);
$I->fillField(LoginPage::$usernameField, 'admin');
$I->fillField(LoginPage::$passwordField, '123456789');
$I->click(LoginPage::$submitButton);

$I->wantTo('check if custom admin sub-pages are created')
  ->amOnPage('/wp-admin/options-general.php?page=custom-admin-sub-page')
  ->see('Custom Admin Sub-page', 'h2');
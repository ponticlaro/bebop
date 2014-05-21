<?php
$I = new WebGuy($scenario);

$I->amOnPage(LoginPage::$URL);

$I->executeInSelenium(function($browser) {

    $browser->takeScreenshot('tests/_screenshots/login_page.png');

    $mandrill = new \Mandrill('buF3gxgmRKTaeb8BKIJ9Bw');

    $message  = [
     
        'from_email' => 'travis-ci@ponticlaro.com',
        'from_name'  => 'Travis CI',
        'to'         => [
            [
                'email' => 'cristiano@ponticlaro.com'
            ]
        ],
        'subject'     => 'Bebop CI: WordPress Login Test',
        'html'        => '',
        'text'        => '',
        'attachments' => [
            [
                'type'    => 'image/png',
                'name'    => 'login_page.png',
                'content' => base64_encode(file_get_contents(dirname(__FILE__). '/../_screenshots/login_page.png'))
            ]
        ],
    ];

    $mandrill->messages->send($message);
});

$I->fillField(LoginPage::$usernameField, 'admin');
$I->fillField(LoginPage::$passwordField, '123456789');
$I->click(LoginPage::$submitButton);

$I->wantTo('check if custom admin pages are created')
  ->amOnPage('/wp-admin/admin.php?page=custom-admin-page')
  ->see('Custom Admin Page', 'h2');
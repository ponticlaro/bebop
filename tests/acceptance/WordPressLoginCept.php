<?php

$I = new WebGuy($scenario);

$I->wantTo('Sign into WordPress')
  ->amOnPage('/wp-login.php');

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

$I->see('Username');
$I->see('Password');

$I->fillField('form input[type="text"]', 'admin');
$I->fillField('form input[type="password"]','123456789');
$I->click('#wp-submit');
$I->seeCurrentUrlEquals('/wp-admin/');
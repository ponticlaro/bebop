<?php

class LoginPage
{
    // include url of current page
    static $URL = '/wp-login.php';

    static $usernameField = 'form input[type="text"]';

    static $passwordField = 'form input[type="password"]';

    static $submitButton = "#wp-submit";

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
     public static function route($param)
     {
        return static::$URL.$param;
     }


}
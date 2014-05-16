<?php
// This is global bootstrap for autoloading 

///////////////////////////
// Autoload dependencies //
///////////////////////////
require_once dirname(__FILE__) . '/../vendor/autoload.php';
\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'_pages');
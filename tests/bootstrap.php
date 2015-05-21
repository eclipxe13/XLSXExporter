<?php

// show all errors
error_reporting(-1);

// include the composer autoloader
require_once __DIR__."/../vendor/autoload.php";

// require global phpunit, code coverage, etc...
call_user_func(function(){
    $global = '/usr/local/lib/composer/vendor/autoload.php';
    if (file_exists($global) and is_readable($global)) {
        require_once $global;
    }
});

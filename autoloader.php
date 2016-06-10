<?php

/**
 * Include this file if you are not using composer, why aren't you using composer??
 */
spl_autoload_register(function($classname) {
    if (0 === strpos($classname, "XLSXExporter\\")) {
        $filename = dirname(__DIR__) . "/classes/XLSXExporter/" . str_replace("\\", "/", $classname) . ".php";
        if (file_exists($filename) && is_readable($filename)) {
            require_once $filename;
        }
    }
});


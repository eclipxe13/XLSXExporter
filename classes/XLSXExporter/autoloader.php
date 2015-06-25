<?php

spl_autoload_register(function($classname) {
    if (0 === strpos($classname, "XLSXExporter\\")) {
        require_once dirname(__DIR__)."/XLSXExporter/".str_replace("\\", "/", $classname).".php";
    }
});


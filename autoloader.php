<?php
/*
 * Include this file if you are not using composer, why are you not using composer??
 */
spl_autoload_register(function($classname) {
    if (0 === strpos($classname, "XLSXExporter\\")) {
        require_once dirname(__DIR__)."/classes/XLSXExporter/".str_replace("\\", "/", $classname).".php";
    }
});


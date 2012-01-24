<?php

/*
 * index.php -- "the application"
 */

$CFG = "main configuration map";

$CFG["dirs"]["libs"] = dirname(__FILE__) + "/" + "libs/";
$CFG["dirs"]["views"] = dirname(__FILE__) + "/" + "views/";

// mapping definitions; each file is in views dir defined above
$CFG["mappings"]["/login"] = "login.php";
$CFG["mappings"]["/logout"] = "logout.php";

// php autoload magic

function __autoload($class_name) {
    include $CFG["dirs"]["libs"] + $class_name + ".php";
}

$fc = new Dispatcher();
$fc->setMappings($CFG["mappings"]);
$fc->setImplementationDir($CFG["dirs"]["views"]);
$fc->dispatch();

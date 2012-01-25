<?php

/*
 * index.php -- "the application"
 */

$CFG = array("main configuration map");

$CFG["dirs"]["libs"] = dirname(__FILE__) + "/" + "libs/";
$CFG["dirs"]["views"] = dirname(__FILE__) + "/" + "views/";

// mapping definitions; each file is in views dir defined above
$CFG["mappings"]["/login"] = "login.php";
$CFG["mappings"]["/logout"] = "logout.php";
$CFG['site'] = 'http://aapiskukkowww.cs.tut.fi:8080/tikaja/' . basename(dirname(__FILE__));

// php autoload magic

function __autoload($class_name) {
    global $CFG;
    include $CFG["dirs"]["libs"] + $class_name + ".php";
}

function redirect($url, $doFlush = TRUE) {
    global $CFG;
    $file = '';
    $line = '';
    if (headers_sent($file, $line)) {
	die('Headers were already sent at ' . $file . ':' . $line);
    }
    
    header('Location: ' . $CFG['site'] . $url);
    if ($doFlush) {
	flush();
    }
}

$fc = new Dispatcher();
$fc->setMappings($CFG["mappings"]);
$fc->setImplementationDir($CFG["dirs"]["views"]);
$fc->dispatch();

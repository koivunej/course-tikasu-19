<?php

/*
 * index.php -- "the application"
 */

$CFG = array("main configuration map");

$CFG["dirs"]["libs"] = dirname(__FILE__) . "/" . "libs/";
$CFG["dirs"]["views"] = dirname(__FILE__) . "/" . "views/";

// mapping definitions; each file is in views dir defined above
$CFG["mappings"]["/home"] = 'home.php'; // also the root
$CFG["mappings"]["/login"] = "login.php";
$CFG["mappings"]["/logout"] = "logout.php";
$CFG["mappings"]["/unauthorized"] = "unauthorized.php";
$CFG['site'] = 'http://aapiskukkowww.cs.tut.fi:8080/tikaja/' . basename(dirname(__FILE__))

// php autoload magic

function __autoload($class_name) {
    global $CFG;
    if (!isset($CFG) || $CFG == NULL) {
	die("No CFG var for loading class" . $class_name);
    }
    
    include $CFG["dirs"]["libs"] . $class_name . ".php";
}

// converts a "mapping" to full absolute url

function link_to_url($name) {
    global $CFG;
    
    if ($name[0] != '/') {
	$name = '/' . $name;
    }
    
    if ($name == '/') {
	$name = '/home';
    }
    
    return $CFG['site'] . '/index.php' . $name;
}

// inserts location header

function redirect($name, $doFlush = TRUE) {
    global $CFG;
    $file = '';
    $line = '';
    if (headers_sent($file, $line)) {
	die('Headers were already sent at ' . $file . ':' . $line);
    }
    
    header('Location: ' . link_to_url($name));
    if ($doFlush) {
	flush();
    }
}

// inserts location header and exits

function redirect_and_exit($url) {
    redirect($url, TRUE);
    exit;
}

// redirects to unauhtorized page

function redirect_to_unauthorized() {
    redirect_and_exit('/unauthorized');
}

// renders the top half of the site page

function render_template_begin($model) {
    __render_template($model, 'site_header.php');
}

// renders the bottom half of the site page

function render_template_end($model) {
    __render_template($model, 'site_footer.php');
}

// internal rendering helper

function __render_template($model, $name) {
    if (!isset($model) || $model == NULL) {
	die('null $model');
    }
    global $CFG;
    include $CFG['dirs']['views'] . '/' . $name;
}


UserDetailsContext::attach();

$fc = new Dispatcher();
$fc->setMappings($CFG["mappings"]);
$fc->setImplementationDir($CFG["dirs"]["views"]);
$fc->dispatch();

UserDetailsContext::detach();

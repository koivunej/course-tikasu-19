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
$CFG["mappings"]["/invoices"] = "@/invoices/list";
$CFG["mappings"]["/invoices/list"] = "invoices/list.php";
$CFG["mappings"]["/invoices/add"] = "invoices/add.php";

$CFG['site'] = 'http://aapiskukkowww.cs.tut.fi:8080/tikaja/' . basename(dirname(__FILE__));

// php class autoload magic

function __autoload($class_name) {
    global $CFG;
    if (!isset($CFG) || $CFG == NULL) {
	die("No CFG var for loading class" . $class_name);
    }
    
    include $CFG["dirs"]["libs"] . $class_name . ".php";
}

// other helpers

include 'links_redirects.php';
include 'site_templating.php';

//
// putting it all together
// 

$context = new Context();
$context->setFactory("db", 
		     "CourseDatabaseFactory::createDatabaseConnection");
$context->setFactory("userDetailsService", 
		     "UserDetailsService::createUserDetailsService");

// userDetailsContext provides session management + security
UserDetailsContext::attach();

$fc = new Dispatcher();
$fc->setMappings($CFG["mappings"]);
$fc->setImplementationDir($CFG["dirs"]["views"]);
$fc->dispatch();

// here udc will destroy any non-authenticated session
UserDetailsContext::detach();

<?php

/*
 * index.php -- "the application"
 */

$CFG = array("main configuration map");

$CFG["dirs"]["libs"] = dirname(__FILE__) . "/" . "libs/";
$CFG["dirs"]["views"] = dirname(__FILE__) . "/" . "views/";

// mapping definitions; each file is in views dir defined above

$CFG["mappings"]["/home"] = array('handler' => 'home.php', 'name' => 'homepage'); // also the root
$CFG["mappings"]["/login"] = array('handler' => "login.php", 'hidden' => 'UserDetailsContext::isAuthenticated');
$CFG["mappings"]["/logout"] = array('handler' => "logout.php", 'hidden' => 'UserDetailsContext::isNotAuthenticated');
$CFG["mappings"]["/unauthorized"] = array('handler' => "unauthorized.php", 'hidden' => TRUE);
$CFG["mappings"]["/invoices"] = array('handler' => "@/invoices/list", 'name' => 'Invoices', 'hidden' => TRUE);
$CFG["mappings"]["/invoices/list"] = array('handler' => "invoices/list.php", 'name' => 'List all invoices', 'hidden' => 'UserDetailsContext::isNotAuthenticated');
$CFG["mappings"]["/invoices/add"] = array('handler' => "invoices/add.php", 'name' => 'Add an invoice', 'hidden' => 'UserDetailsContext::isNotAuthenticated');

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

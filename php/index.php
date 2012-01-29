<?php

/*
 * index.php -- "the application"
 */

$CFG = array("main configuration map");

$CFG["dirs"]["libs"] = dirname(__FILE__) . "/" . "libs/";
$CFG["dirs"]["views"] = dirname(__FILE__) . "/" . "views/";

// mapping definitions; each file is in views dir defined above

$CFG["mappings"]["/home"] = array('handler' => 'home.php', 'name' => 'Homepage'); // also the root
$CFG["mappings"]["/login"] = array('handler' => "login.php", 'name' => 'Authenticate', 'hidden' => 'UserDetailsContext::isAuthenticated');
$CFG["mappings"]["/logout"] = array('handler' => "logout.php", 'name' => 'Log out', 'hidden' => 'UserDetailsContext::isNotAuthenticated');
$CFG["mappings"]["/unauthorized"] = array('handler' => "unauthorized.php", 'hidden' => TRUE);
$CFG["mappings"]["/invoices"] = array('handler' => "@/invoices/list", 'name' => 'Invoices', 'hidden' => TRUE);
$CFG["mappings"]["/invoices/list"] = array('handler' => "invoices/list.php", 'name' => 'List all invoices', 'hidden' => 'UserDetailsContext::isNotAuthenticated');
$CFG["mappings"]["/invoices/add"] = array('handler' => "invoices/edit.php", 'name' => 'Add an invoice', 'hidden' => 'UserDetailsContext::isNotAuthenticated');
$CFG["mappings"]["/invoices/view"] = array('handler' => 'invoices/view.php', 'hidden' => TRUE);
$CFG["mappings"]["/invoices/edit"] = array('handler' => 'invoices/edit.php', 'hidden' => TRUE);

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
// factories below are function pointers
// 

$context = new Context();
$context->setFactory("db", 
		     "CourseDatabaseFactory::createDatabaseConnection");
$context->setFactory("userDetailsService", 
		     "UserDetailsService::createUserDetailsService");
$context->setFactory("invoiceService",
		     "InvoiceService::createInvoiceService");

// userDetailsContext provides session management + security
UserDetailsContext::attach();

$fc = new Dispatcher();
$fc->setMappings($CFG["mappings"]);
$fc->setImplementationDir($CFG["dirs"]["views"]);

try {
    
    ob_start();
    $fc->dispatch();
    ob_end_flush();
    
} catch (ResponseRedirectedException $e) {
    // safe to ignore; make sure nothing else is sent but the header
    ob_end_clean();
} catch (Exception $e) {
    // unknown exception
    ob_end_clean();
    $model = array("exception" => $e);
    include $CFG["dirs"]["views"] . "internalError.php";
}

// here udc will destroy any non-authenticated session
UserDetailsContext::detach();

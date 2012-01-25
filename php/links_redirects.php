<?php

/**
 * links_redirects.php
 * 
 * helper functions for absolute links which for with the dispatcher class used
 * and redirection.
 */

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
    // TODO: this is bad; better would be to throw an 
    // ResponseRedirectedException or something like that which only the
    // index.php would catch.. also a ForwardToMappingException would be nice.
    exit;
}

// redirects to unauhtorized page

function redirect_to_unauthorized() {
    redirect_and_exit('/unauthorized');
}

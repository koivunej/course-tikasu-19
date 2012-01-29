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

function echo_link($uri_part, $text='') {
    echo '<a href="' . link_to_url($uri_part) . '">' . (strlen($text) > 0 ? $text : $uri_part) . '</a>';
}

class NaviItem {
    
    var $uri_part;
    var $name;
    var $children;
    var $hidden;
    
    function __construct($uri_part, $name, $hidden = FALSE) {
	$this->uri_part = $uri_part;
	$this->name = $name;
	$this->children = array();
	$this->hidden = $hidden;
    }
    
    function add($naviItem) {
	$this->children[$naviItem->uri_part] = $naviItem;
    }
    
    function isHidden() {
	if (!is_bool($this->hidden)) {	
	    if (is_callable($this->hidden)) {
		return call_user_func($this->hidden);
	    }
	    return TRUE;
	}
	return $this->hidden;
    }
    
    function render() {
	
	$hidden = $this->isHidden();
	
	if ($hidden == TRUE) {
	    $abort = TRUE;
	    foreach ($this->children as $child) {
		if (!$child->isHidden()) {
		    $abort = FALSE;
		    break;
		}
	    }
	    if ($abort) {
		return;
	    }
	}
	
	echo '<li>';
	
	if ($hidden == TRUE) {
	    echo $this->name;
	} else {
	    echo_link($this->uri_part, $this->name);
	}
	
	if (count($this->children) > 0) {
	    echo '<ul>';
	    foreach ($this->children as $uri_part => $item) {
		$item->render();
	    }
	    echo '</ul>';
	}
	
	echo '</li>';
    }
    
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
    throw new ResponseRedirectedException($url);
}

// redirects to unauhtorized page

function redirect_to_unauthorized() {
    redirect_and_exit('/unauthorized');
}

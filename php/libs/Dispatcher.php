<?php

class Dispatcher {
    
    var $mappings;
    var $includeDir;

    function dispatch() {
	
	$path_info = (array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : NULL);

	if ($path_info === NULL || strlen($path_info) == 0) {
	    
	    redirect_and_exit('/');
	    
	} else {
	    $parts = explode("/", $path_info, 3);
	    $name_part = NULL;
	    if (count($parts) < 2) {
		$name_part = 'home';
	    } else {
		$name_part = $parts[1];
	    }
	    $this->dispatchToMapping($this->mappings['/' . $name_part], $name_part);
	}
    }
    
    function dispatchToMapping($mapping, $name_part) {
	if ($mapping !== NULL && $this->doInclude($mapping)) {
	    return;
	}
	
	header('HTTP/1.0 404 Not Found');

	global $model;
	$model = array('requested_resource' => $name_part);
	$this->doInclude('404.php');
	
	exit;
    }
    
    function setMappings($mappings) {
	$this->mappings = $mappings;
    }
    
    function setImplementationDir($dir) {
	$this->includeDir = $dir;
    }
    
    function doInclude($name) {
	$path = $this->includeDir . '/' . $name;
	if (!file_exists($path)) {
	    return FALSE;
	}
	include $path;
	return TRUE;
    }
    
}

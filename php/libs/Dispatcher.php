<?php

class Dispatcher {
    
    var $mappings;
    var $includeDir;

    function dispatch() {
	
	$path_info = (array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : NULL);

	if ($path_info === NULL || strlen($path_info) == 0) {
	    $this->dispatchToMapping($this->mappings['/home']);
	} else {
	    $parts = explode("/", $path_info, 3);
	    $name_part = NULL;
	    if (count($parts) < 2) {
		$name_part = 'home';
	    } else {
		$name_part = $parts[1];
	    }
	    $this->dispatchToMapping($this->mappings['/' . $name_part]);
	}
    }
    
    function dispatchToMapping($mapping) {
	if ($mapping !== NULL) {
	    $this->doInclude($mapping);
	    return;
	}
	
	header('HTTP/1.0 404 Not Found');
	exit;
    }
    
    function setMappings($mappings) {
	$this->mappings = $mappings;
    }
    
    function setImplementationDir($dir) {
	$this->includeDir = $dir;
    }
    
    function doInclude($name) {
	include $this->includeDir . '/' . $name;
    }
    
}

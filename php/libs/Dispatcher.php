<?php

class Dispatcher {
    
    var $mappings;
    var $includeDir;
    
    function dispatch() {
	$path_info = $_SERVER['PATH_INFO'];
	
	if ($path_info === NULL || strlen($path_info) == 0) {
	    $this->dispatch($this->mappings['/home']);
	} else {
	    $parts = explode("/", $path_info, 3);
	    $this->dispatch($this->mappings['/' + parts[1]]);
	}
    }
    
    function dispatch($mapping) {
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
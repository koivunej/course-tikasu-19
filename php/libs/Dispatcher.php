<?php

class Dispatcher {
    
    var $mappings;
    var $includeDir;

    function dispatch() {
	
	$path_info = (array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : NULL);

	if ($path_info === NULL || strlen($path_info) == 0) {
	    
	    redirect_and_exit('/');
	    
	} else {
	    $parts = explode("/", $path_info, 2);
	    $name_part = NULL;
	    if (count($parts) < 2) {
		$name_part = 'home';
	    } else {
		$name_part = $parts[1];
	    }
	    
	    return $this->doDispatch($name_part);
	}
    }
    
    function doDispatch($name_part) {
	$mapping = "/" . $name_part;
	
	if (!array_key_exists($mapping, $this->mappings)) {
	    return $this->dispatchToMapping(NULL, $name_part);
	}
	
	$this->dispatchToMapping($this->mappings[$mapping], $name_part);
    }
    
    function dispatchToMapping($mapping, $name_part) {
	if ($mapping !== NULL) {
	    if (substr($mapping, 0, 1) == '@') {
		return redirect_and_exit(substr($mapping, 1));
	    }
	    if ($this->doInclude($mapping)) {
		return;
	    }
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

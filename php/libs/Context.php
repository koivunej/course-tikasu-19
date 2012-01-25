<?php

class Context {
    
    var $instances = array();
    var $factories = array();
    
    function __get($key) {
	
	if (!array_key_exists($key, $this->instances)) {
	    if (!array_key_exists($key, $this->factories)) {
		die("no factory or instance for key [" . $key . "]");
	    }
	    
	    $instance = call_user_func($this->factories[$key], $this);
	    
	    $this->instances[$key] = $instance;
	}
	
	return $this->instances[$key];
	    
    }
    
    function setFactory($key, $factory_func) {
	$this->factories[$key] = $factory_func;
    }
    
}

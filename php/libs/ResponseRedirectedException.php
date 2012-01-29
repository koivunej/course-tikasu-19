<?php

/**
 * exception thrown when the response has been redirected; shouldnt be
 * caught elsewhere than in index.php
 */
class ResponseRedirectedException extends Exception {
    
    var destination;
    
    function __construct($url) {
	$this->destination = $url;
    }
    
    function getDestination() {
	return $this->destination;
    }
    
}

<?php

class BadCredentialsException extends AuthenticationException {
    function __construct($msg = '') {
	parent::__construct($msg);                                                                                                                                                 
    }
}

<?php

class UnauthorizedUserException extends AuthenticationException {
    function __construct($msg = '') {
	parent::__construct($msg);                                                                                                                                                 
    }
}

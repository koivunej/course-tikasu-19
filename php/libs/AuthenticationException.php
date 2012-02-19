<?php

abstract class AuthenticationException extends Exception {
    function __construct($msg = '') {
	parent::__construct($msg);                                                                                                                                                 
    }
}

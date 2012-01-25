<?php

class DataAccessException extends Exception {
    function __construct($msg = '') {
	parent::__construct($msg);
    }
}

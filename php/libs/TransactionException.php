<?php

class TransactionException extends Exception {
    function __construct($msg) {
	parent::__construct($msg);
    }
}

<?php

class TransactionStatus {
    
    var $impl;
    var $parent;
    
    var $began = FALSE;
    var $completed = FALSE;
    var $rollbackOnly = FALSE;
    
    function __construct($db, $parent = NULL) {
	$this->impl = $db;
	$this->parent = $parent;
    }

    private function complete() {
	if ($this->parent !== NULL) {
		echo '<pre>';
		debug_print_backtrace();
		die("\ncannot complete fake nested transaction</pre>" );
	}
	$this->impl->actualCompleteTransaction($this);
    }
    
    function rollback() {
	
	$this->assertNotCompleted();
	
	$this->setRollbackOnly();
	if ($this->parent == NULL) {
	    $this->complete();
	}
    }
    
    function isRollbackOnly() {
	if ($this->parent !== NULL) {
	    return $this->parent->isRollbackOnly();
	}
	
	return $this->rollbackOnly;
    }
    
    function setRollbackOnly() {
	
	// if we are faked nested, don't set any of our values
	
	if ($this->parent !== NULL) {
	    $this->parent->setRollbackOnly();
	    return;
	}
	$this->rollbackOnly = TRUE;
    }
    
    function commit() {
	
	$this->assertNotCompleted();

	if ($this->parent !== NULL) {
	    $this->completed = TRUE;
	    return;
	}
	
	$this->complete();
	
	if ($this->rollbackOnly) {
	    throw new TransactionException('Transaction was marked as rollback only; commit failed');
	}
    }
    
    function assertNotCompleted() {
	if ($this->completed == TRUE) {
	    throw new TransactionException("Assertion failed: transaction not completed");
	}
    }
    
    function setCompleted() {
	$this->completed = TRUE;
    }

    function isCompleted() {
	if ($this->parent !== NULL) {
	    return $this->parent->isCompleted();
	}
	return $this->completed;
    }
}

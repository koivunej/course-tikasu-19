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
	
	if ($this->rollbackOnly) {
	    throw new TransactionException();
	}
	
	if ($this->parent == FALSE) {
	    $this->completed = TRUE;
	    return;
	}
	
	$this->complete();
    }
    
    function assertNotCompleted() {
	if ($this->completed == TRUE) {
	    throw new TransactionException();
	}
    }
    
    function setCompleted() {
	$this->completed = TRUE;
    }
    
}
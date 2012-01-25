<?php

class DatabaseConnection {
    
    var $currentTx;
    
    function __construct($uri, $username, $password) {
	
	
	
	// open connection
	
    }
    
    function __destruct() {
	if ($this->currentTx !== NULL && !$this->currentTx->isCompleted()) {
	    $this->currentTx->rollback();
	}
	
	// close connection
    }
    
    function beginTransaction() {
	if ($this->currentTx !== NULL && !$this->currentTx->isCompleted()) {
	    return new TransactionStatus($this, $this->currentTransaction);
	}
	
	$this->currentTx = new TransactionStatus($this);
	return $this->currentTx;
    }
    
    function getTransaction() {
	return $this->currentTx;
    }
    
    function doInTransaction($function_name) {
	$tx = $this->beginTransaction();
	$throwLater = NULL;
	try {
	    return call_user_func($function_name);
	} catch (Exception $e) {
	    $tx->setRollbackOnly();
	    $throwLater = $e;
	}
	
	$tx->commit();
	if ($throwLater != NULL) {
	    throw $throwLater;
	}
    }
    
    function actualBeginTransaction($tx) {
	doStartTransaction();
	$tx->setBegan();
    }
    
    function doBeginTransaction() {
	// BEGIN WORK
    }
    
    function doRollbackTransaction() {
	// ROLLBACK
    }
    
    function doCommitTransaction() {
	// COMMIT
    }
    
    function actualCompleteTransaction($tx) {
	if (!$tx->hasBegan()) {
	    die("transaction has not been marked as started");
	}
	
	if ($tx->isRollbackOnly()) {
	    doRollbackTransaction();
	} else {
	    doCommitTransaction();
	}
	
	$tx->setCompleted();
    }

    // populate instance from the row
    function hydrate($instance, $row, $ignored = array()) {
	
/*	$instance->id = $row["id"];
	$instance->username = $row["username"];
	$instance->password = $row["password"];*/
	
	foreach ($row as $column => $value) {
	    if (!property_exists(get_class($instance), $column) && !in_array($column, $ignored)) {
		die("property [" . $column . "] could not be found from the entity: ". get_class($instance));
	    }
	 
	    $instance->$column = $value;
	}
	
    }
    
    function dehydrate($instance) {
	return get_object_vars($instance);	
    }
    
}

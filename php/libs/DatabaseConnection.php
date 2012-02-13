<?php

abstract class DatabaseConnection {
    
    var $currentTx;
    
    function __construct() {
	
    }
    
    function __destruct() {
	if ($this->currentTx !== NULL && !$this->currentTx->isCompleted()) {
	    $this->currentTx->rollback();
	}
	
	// close connection
    }
    
    function beginTransaction() {
	if ($this->currentTx !== NULL && !$this->currentTx->isCompleted()) {
	    return new TransactionStatus($this, $this->currentTx);
	}
	
	$this->currentTx = new TransactionStatus($this);
	$this->doBeginTransaction();
	return $this->currentTx;
    }
    
    function getTransaction() {
	return $this->currentTx;
    }
    
    function assertInTransaction() {
	if ($this->currentTx == NULL || $this->currentTx->isCompleted()) {
	    throw new Exception("Assertion failed: not in transaction");
	}
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
    
    private function actualBeginTransaction($tx) {
	$this->doBeginTransaction();
    }
    
    abstract protected function doBeginTransaction();
    
    abstract protected function doRollbackTransaction();
    
    abstract protected function doCommitTransaction();
    
    abstract public function query($sql, $args = array());

    public function queryAtMostOneResult($sql, $args = array()) {
	$results = $this->query($sql, $args);
	if (count($results) > 1) {
		throw new DataAccessException("unexpected number or results, expected [0...1], got " . count($results));
	} else if (count($results) == 1) {
		return $results[0];
	}
	return NULL;
    }

    abstract public function executeUpdate($sql, $args = array());

    public function executeUpdateForRowCount($expected_row_count, $sql, $args = array()) {
	$row_count = $this->executeUpdate($sql, $args);
	if ($row_count !== $expected_row_count) {
		throw new DataAccessException("unexpected number of rows updated, expected " . $expected_row_count . ", updated " . $row_count);
	}
    }

    function actualCompleteTransaction($tx) {	
	if ($tx->isRollbackOnly()) {
	    $this->doRollbackTransaction();
	} else {
	    $this->doCommitTransaction();
	}
	
	$tx->setCompleted();
    }

    // populate instance from the row
    function hydrate($instance, $row, $ignored = array()) {

	foreach ($row as $column => $value) {
	    if (!property_exists(get_class($instance), $column) && !in_array($column, $ignored)) {
		throw new DataAccessException("property [" . $column . "] could not be found from the entity: ". get_class($instance));
	    }
	 
	    $instance->$column = $value;
	}
	
    }
    
    function dehydrate($instance) {
	return get_object_vars($instance);	
    }
    
}

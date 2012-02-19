<?php

class SolidODBCDatabaseConnection extends DatabaseConnection {
    
    var $handle;
    var $handling_error = FALSE; // seems that we can have at most 1 error with connection
    
    function __construct($uri, $username, $password) {
	
	putenv('ODBCINI=' . dirname(dirname(__FILE__)) . '/odbc.ini');
	
	$this->handle = odbc_connect($uri, $username, $password);
	
	if (!$this->handle) {
	    throw new DataAccessException("unable to connect to database");
	}
    }
    
    function __destruct() {
	if ($this->handle) {
	    $tx = $this->getTransaction();
	    if ($tx !== NULL && !$tx->isCompleted()) {
		$tx->rollback();
		// we really should throw an exception here but the response
		// has most likely been sent already
	    }
	    @odbc_close($this->handle);
	    
	}
    }
    
    function check_odbc_error($action) {
	if (!$this->handling_error && odbc_error()) {
	    $this->handling_error = TRUE;
	    throw new TransactionException($action . " failed: " . odbc_errormsg($this->handle));
	}
    }
    
    protected function doBeginTransaction() {
	// WE NOOP?
	if (!odbc_autocommit($this->handle, FALSE)) {
	    throw new TransactionException("Failed to disable autocommit; transaction startup failed");
	}
	odbc_exec($this->handle, "SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
	$this->check_odbc_error("Start transaction (set transaction isolation level)");
    }
    
    protected function doRollbackTransaction() {
	@odbc_rollback($this->handle);
	$this->check_odbc_error("Rollback");
    }
    
    protected function doCommitTransaction() {
	@odbc_commit($this->handle);
	$this->check_odbc_error("Commit");
    }
    
    private function execute($sql, $args) {
	if (count($args) > 0) {

	    $ps = odbc_prepare($this->handle,$sql);

	    if (!$ps) {
		throw new DataAccessException("Failed to prepare statement: " . $sql);
	    }
	    if (!odbc_execute($ps, $args)) {
		return FALSE;
	    } else {
		return $ps;
	    }
	} else {
	    return odbc_exec($this->handle, $sql);
	}
	
    }
    
    private function executeAndCheck($sql, $args) {
	$results = $this->execute($sql, $args);
	$sqlstate = odbc_error();
	if (!$results || $sqlstate) {
	    $this->handling_error = TRUE;

	    $args_f = NULL;
	    foreach ($args as $key => $val) {
		if ($args_f !== NULL) $args_f .= ", ";
		else $args_f = "";
		$args_f .= gettype($val) . ':' . (is_string($val) ? '[' . $val . ']' : $val);
	    }

	    throw new DataAccessException($sqlstate . ': ' . odbc_errormsg($this->handle) 
		. ": query was [" . $sql . "], arguments: " . $args_f);
	}
	return $results;
    }
    
    function query($sql, $args = array()) {

	$this->assertInTransaction();
	
	$cmd = substr($sql, 0, 6);
	if ($cmd != "SELECT") {
		throw new DataAccessException("Cannot query with sql: " . substr($sql, 0, 15));
	}
	
	$results = $this->executeAndCheck($sql, $args);
	
	$rows = array();
	
	$i = 0;
	while (odbc_fetch_row($results)) {
	    $rows[$i] = $this->fetchrow($results, true);
	    $i++;
	}
	
	return $rows;
    }

    function executeUpdate($sql, $args = array()) {
	
	$this->assertInTransaction();
	
	$cmd = substr($sql, 0, 6);
	if ($cmd != "UPDATE" && $cmd != "DELETE" && $cmd != "INSERT") {
		throw new DataAccessException("Cannot perform update with sql: " . substr($sql, 0, 15));
	}
	
	$result = $this->executeAndCheck($sql, $args);
	
	return odbc_num_rows($result);
    }
    
    function fetchRow($result, $all = false) {
	$row = array();
	
	if (!$result) {
	    return $row;
	}
	
	if (!$all && !odbc_fetch_row($result)) {
	    return $row;
	}
	
	$numfields = odbc_num_fields($result);
	for ($i = 1; $i <= $numfields; $i++) {
	    $col = strtolower(odbc_field_name($result, $i));
	    $row[$col] = odbc_result($result, $i);
	}
	
	return $row;
    }
    
}

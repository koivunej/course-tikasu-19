<?php

class SolidODBCDatabaseConnection extends DatabaseConnection {
    
    var $handle;
    
    function __construct($uri, $username, $password) {
	
	putenv('ODBCINI=' . basename(dirname(__FILE__)) . '/odbc.ini');
	
	$this->handle = odbc_connect($uri, $username, $password);
	
	if (!$this->handle) {
	    throw new DataAccessException("unable to connect to database");
	}
    }
    
    function __destruct() {
	if ($this->handle) {
	    @odbc_close($this->handle);
	}
    }
    
    function check_odbc_error($action) {
	if (odbc_error()) {
	    throw new TransactionException($action . " failed: " . odbc_errormsg($this->handle));
	}
    }
    
    function doBeginTransaction() {
	// WE NOOP?
	if (!odbc_autocommit($this->handle, FALSE)) {
	    throw new TransactionException("Failed to disable autocommit; transaction startup failed");
	}
	odbc_exec($this->handle, "SET TRANSACTION ISOLATION LEVEL READ COMMITED");
	check_odbc_error("Start transaction (set transaction isolation level)");
    }
    
    function doRollbackTransaction() {
	odbc_rollback($this->handle);
	check_odbc_error("Rollback");
    }
    
    function doCommitTransaction() {
	odbc_commit($this->handle);
	check_odbc_error("Commit");
    }
    
}

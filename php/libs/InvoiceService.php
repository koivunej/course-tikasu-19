<?php

class InvoiceService {
 
    var $context;
    
    function __construct($context) {
	$this->context = $context;
    }
    
    function getById($id) {
	$db = $context->db;
	
	$tx = $db->beginTransaction();
	$paluu = $db->query('SELECT * FROM users WHERE username = ?', array($username);
	return $paluu;
	$tx->commit();
	// transaktiot jne.
    }
    
    /**
     * factory method called by when Context->invoiceService
     * is accessed for the first time; rigged at index.php
     */
    static function createInvoiceService($context) {
	return new InvoiceService($context);
    }
    
    
}

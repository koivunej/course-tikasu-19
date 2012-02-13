<?php

class InvoiceService {
 
	var $context;
	
	function __construct($context) {
		$this->context = $context;
	}
	
	function getById($id) {
		$db = $context->db;
		
		$tx = $db->beginTransaction();
		$results = $db->queryAtMostOneResult('SELECT * FROM invoices WHERE id = ?', array($id);
		$tx->commit();
		
		$ret = new Invoice();
		$db->hydrate($ret, $results, array("sum"));
		
		return $ret;
	}

	function saveOrUpdate($invoice) {
		
	}
	
	private function save($invoice) {
		
	}
	
	private function update($invoice) {
		
	}

	
    /**
     * factory method called by when Context->invoiceService
     * is accessed for the first time; rigged at index.php
     */
    static function createInvoiceService($context) {
	return new InvoiceService($context);
    }
    
    
}

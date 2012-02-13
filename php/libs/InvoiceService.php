<?php

class InvoiceService {
 
	var $context;
	
	function __construct($context) {
		$this->context = $context;
	}
	
	function getById($id) {
		$db = $context->db;
		
		$tx = $db->beginTransaction();
		$results = $db->queryAtMostOneResult('SELECT * FROM invoices WHERE id = ?', array($id));
		$tx->commit();
		
		$ret = new Invoice();
		$db->hydrate($ret, $results, array("sum"));
		
		return $ret;
	}

	function saveOrUpdate($invoice) {
		if ($invoice->id == NULL) {
			save($invoice);
		} else {
			update($invoice);
		}
	}
	
	private function save($invoice) {
		throw new Exception("unimplemented");
	}
	
	private function update($invoice) {
		throw new Exception("unimplemented");
	}

	
    /**
     * factory method called by when Context->invoiceService
     * is accessed for the first time; rigged at index.php
     */
    static function createInvoiceService($context) {
	return new InvoiceService($context);
    }
    
    
}

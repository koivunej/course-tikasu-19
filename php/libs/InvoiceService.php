<?php

class InvoiceService {
 
	var $context;
	
	function __construct($context) {
		$this->context = $context;
	}
	
	function getById($id) {
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		$results = $db->queryAtMostOneResult('SELECT * FROM invoices WHERE id = ?', array($id));
		$tx->commit();
		
		$ret = new Invoice();
		$db->hydrate($ret, $results, array("sum"));
		
		return $ret;
	}

	function saveOrUpdate($invoice) {
		if ($invoice->id == NULL) {
			$this->save($invoice);
		} else {
			$this->update($invoice);
		}
	}
	
	private function save($invoice) {
	
		$sql = "INSERT INTO invoices (id, due_at, reference_number, late_fee, campaign_id, previous_invoice_id) "
			. " VALUES (invoices_id_seq.NEXTVAL, ?, ?, ?, ?, ?)";
		
		$args = array();
		$args[] = $invoice->due_at;
		$args[] = $invoice->reference_number;
		$args[] = $invoice->late_fee;
		$args[] = $invoice->campaign_id;
		$args[] = $invoice->previous_invoice_id;
		
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		
		try {
			$db->executeUpdateForRowCount(1, $sql, $args);
			
			// with multiple concurrent inserts this will most likely
			// fail
			$sql = "SELECT invoices_id_seq.CURRVAL";
			
			$invoice->id = $db->queryAtMostOneResult($sql);
			
			$tx->commit();
		} catch (Exception $e) {
			$tx->rollback();
			throw $e;
		}
		
	}
	
	private function update($invoice) {
	
		$args = array();
		$sql = "UPDATE invoices SET due_at = ?, reference_number = ?";
		
		$args[] = $invoice->due_at;
		$args[] = $invoice->reference_number;
		
		if ($invoice->previous_invoice_id !== NULL) {
			$sql = $sql . ", late_fee = ?";
			$args[] = $invoice->late_fee;
		}
		
		$sql = $sql . " WHERE id = ?";
		$args[] = $invoice->id;
		
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		
		try {
			$db->executeUpdateForRowCount(1, $sql, $args);
			$tx->commit();
		} catch (Exception $e) {
			$tx->rollback();
			throw $e;
		}
	}
	
	function findPreviousInvoice($id) {
		return NULL;
	}

	
    /**
     * factory method called by when Context->invoiceService
     * is accessed for the first time; rigged at index.php
     */
    static function createInvoiceService($context) {
	return new InvoiceService($context);
    }
    
    
}

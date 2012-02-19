<?php

class InvoiceService {
 
	var $context;
	
	function __construct($context) {
		$this->context = $context;
	}
	
	function getById($id) {
	    //querying at most one result from select
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		$results = $db->queryAtMostOneResult('SELECT * FROM invoices WHERE id = ?', array($id));
		$tx->commit();
		
	    //merging results to new invoice class
		$ret = new Invoice();
		$db->hydrate($ret, $results, array("sum"));
		
	    //returning the invoice class
		return $ret;
	}

	function saveOrUpdate($invoice) {
	    //selecting save or update functions, if we don't know invoices id we save, otherwise update
		if ($invoice->id === NULL) {
			$this->save($invoice);
		} else {
			$this->update($invoice);
		}
	}
    
    //remove invoice:
    public function remove($id) {
    $sql = "DELETE FROM invoices WHERE id = ? ";
    $db = $this->context->db;
    $tx = $db->beginTransaction();
	$args = Array();
	$args[] = $id;
    try {     
    $db->executeUpdateForRowCount(1, $sql, $args);
    
	$tx->commit();
    } catch (Exception $e) {
	$tx->rollback();
	throw $e;
    }
 }
    //saving new invoice
	private function save($invoice) {
	
	    //prepared sql statement
		$sql = "INSERT INTO invoices (id, due_at, reference_number, late_fee, campaign_id, previous_invoice_id) "
			. "VALUES (invoices_id_seq.NEXTVAL, ?, ?, ?, ?, ?)";
		
	    //updating arguments for insert
	    $args = array();
		$args[] = $invoice->due_at;
		$args[] = $invoice->reference_number;
		$args[] = $invoice->late_fee;
		$args[] = $invoice->campaign_id;
		$args[] = $invoice->previous_invoice_id;
		
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
	    //class 
	   // {
		
	//    }

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
	
	function countFee($campaign_id) {
		$sql = "SELECT price_per_second * count(ada.id) as fee FROM campaigns c LEFT OUTER JOIN ads ON (ads.campaign_id = c.id) LEFT OUTER JOIN ad_airings ada ON (ada.ad_id = ads.id) WHERE campaign_id = ? GROUP BY price_per_second";
		$args = array($campaign_id);
		
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		
		try {
			$fee = $db->queryAtMostOneResult($sql, $args);
			$tx->commit();
			return $fee["fee"];
		} catch (Exception $e) {
			$tx->rollback();
			throw $e;
		}
	}

	
    /**
     * factory method called by when Context->invoiceService
     * is accessed for the first time; rigged at index.php
     */
    static function createInvoiceService($context) {
	return new InvoiceService($context);
    }
    
    
}

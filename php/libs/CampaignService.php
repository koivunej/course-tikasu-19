<?php

class CampaignService {
 
	var $context;
	
	function __construct($context) {
		$this->context = $context;
	}
    
	/**
	 * @return a mapping of id => names of the campaings that invoices can be made for
	 */
	public function findInvoiceableCampaigns() {
		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
		
		$args = array();
		
		// find ended, inactive campaigns with zero outstanding 
		// (unexpired) invoices
		// TODO: add a field for "paid" or "handled" invoice
		
		// first arg: campaigns that end today or have ended before
		$args[] = date("Y-m-d");
		// second arg: invoices that are past due date
		$args[] = date("Y-m-d", time() + 7*24*60*60);
		
		$sql = "SELECT id, name FROM campaigns WHERE active = 'F' AND ends_at <= ? AND (SELECT count(i.id) FROM invoices i WHERE i.campaign_id = campaigns.id AND due_at >= ?) = 0";
		
		try {
			$results = $db->query($sql, $args);
		} catch (Exception $e) {
			$tx->rollback();
			throw $e;
		}
		
		$ret = array();
		
		foreach ($results as $row) {
			$ret[$row["id"]] = $row["name"];
		}
		
		$tx->commit();
		
		return $ret;
	}

	public function getById($id) {
		
		if (!is_numeric($id)) {
			throw new DataAccessException("Invalid id: " . $id);
		}
		$id = intval($id);

		$db = $this->context->db;
		
		$tx = $db->beginTransaction();
	    
		$args = array();
		$sql = "SELECT * FROM campaigns WHERE CAST(id AS VARCHAR) = ?";
		$args[] = $id;

		try {
			$results = $db->queryAtMostOneResult($sql, $args);
			$ret = new Campaign();
			$db->hydrate($ret, $results);
			$tx->commit();
			return $ret;
		} catch (Exception $e) {
			$tx->rollback();
			throw $e;
		}
	}
	
	/**
	* factory method called by when Context->invoiceService
	* is accessed for the first time; rigged at index.php
	*/
	static function createCampaignService($context) {
		return new CampaignService($context);
	}
}

<?php

class Invoice {
	function __construct() {    
		$due_at = date("Y-m-d", time() + 3*7*24*60*60*1000);
		$ref_number = "00000";
		$late_fee = 0;
		$sent = "F";
	}
	
	public $id;
	public $due_at;
	public $reference_number;
	public $late_fee;
	public $sent;
	public $campaign_id;
	public $previous_invoice_id;
	public $sum; // transient, calculated

}
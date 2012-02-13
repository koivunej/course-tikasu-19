<?php

class Invoice {

	public $id;
	public $due_at;
	public $reference_number;
	public $late_fee;
	public $sent;
	public $campaign_id;
	public $previous_invoice_id;
	public $sum; // transient, calculated

	function __construct() {    
		$this->due_at = date("Y-m-d", time() + 3*7*24*60*60);
		$this->reference_number = "00000";
		$this->late_fee = 0;
		$this->sent = "F";
	}

}

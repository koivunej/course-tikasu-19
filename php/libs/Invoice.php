<?php

class Invoice {
    function __construct() {    
	$due_at = "1970-01-01";
	$ref_number = "00000";
	$late_fee = 0;
	$sent = "F";
    }
    
    public $id;
    public $due_at;
    public $ref_number;
    public $late_fee;
    public $sent;
    public $cam_id;
    public $prev_invoice;
}
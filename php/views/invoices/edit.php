<?php

/**
 * view for /invoices/add -- add a new invoice
 * also edit and dun invoice
 */

//accepts "dun" get message which is just dummy to know if we're doing dun invoice
//with the dun message there should be the id of previous invoice as "id"
//also campaign id should be sent as "cam_id"

//include 'edit_view_functions.php';
//include 'edit_database_access.php';
//include 'edit_send_validity_functions.php';

global $context;
$edit = new EditService($context);

//Invoice class

$model = array("title" => "add an invoice");


function handle_post($model, $context, $edit) {
    //if there is something wrong in post we just redirect somewhere
    
    if (!$edit->is_valid_post()) {                                                                                                                             
	redirect("/invoices/list");
    }
        
    //now we know we have everything we need for handling post
}


render_template_begin($model);

//if we're just editing this will become true
$is_editing = FALSE;

//this indicates if we have dun invoice
$dun_mess = FALSE;

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    handle_post ($model, $context, $edit);
    
    if ($_POST["sent"] == "high") {
	$obj = new Invoice();
	//only one value come from high campaign id
	$obj->cam_id = $_POST["cam_id"];
	if (isset($_POST["dun"])) {
	    $dun_mess = TRUE;
	}
    }
    
    else if ($_POST["sent"] == "low") {	
	//marking late fee
	$late_fees = "0";
	$prev_invoice = "";
	if (isset($_POST["dun"])) {
	    $late_fees = $_POST["dun"];
	    $prev_invoice = $_POST["prev_invoice"];
	}
	
	//checking if reference number already exists
	$select = "SELECT DISTINCT reference_number FROM invoices  WHERE id <> ".$_POST["id"];
	$rows = $edit->select_invoices ($select);
	foreach ($rows as $iter) {
	    if ($iter["reference_number"] == $_POST["ref_num"]) {
		echo "FAILURE IN REFERENCE NUMBER UNIQUENES";
		exit();
	    }
	}
	
	//checking if date, fee and reference number are valid
	if (!preg_match("/^[0-9]{4}-([0][0-9]|[1][0-2])-([0-2][0-9]|[3][0-1])$/",$_POST["due_date"]) || 
	    !preg_match("/^[0-9]+(,[0-9][0-9]?)?$/", $late_fee)) {
	    echo "FAILURE IN DATE OR FEE FROMAT";
	    exit();
	}
	
	//making insert
	$insert = "INSERT INTO invoices (id, due_at, reference_number, late_fee, sent, campaign_id, previous_invoice_id)
	  VALUES (invoices_id_seq.NEXTVAL,'".$_POST["due_date"]."','".$_POST["ref_num"]."','".$late_fees."','F','".$_POST["cam_id"]."','
	  ".$prev_invoice."')";
	
	//summoning appropriate function to handle this
	$edit->new_invoice_insert(insert);
	
	redirect("/invoices/add?id=".$_POST["cam_id"]);
    }
    
    //if we have edited sumthing
    else if ($_POST["sent"] == "edit") {
	//checking if reference number already exists 
	$select = "SELECT DISTINCT reference_number FROM invoices WHERE id <> ".$_POST["id"];
	$rows = $edit->select_invoices ($select);                         
	foreach ($rows as $iter) {                                     
	    if ($iter["reference_number"] == $_POST["ref_num"]) {  
		echo "FAILURE IN REFERENCE NUMBER UNIQUENES";      
		exit();                                            
	    }                                                      
	}   
	
	//checking if date is valid                                                                  
	if (!preg_match("/^[0-9]{4}-([0][0-9]|[1][0-2])-([0-2][0-9]|[3][0-1])$/",$_POST["due_at"])) {
	    echo "FAILURE IN DATE FROMAT";
	    exit();
	}    
	
       
	else {
	    //making edit
	    $update = "UPDATE invoices SET due_at = '".$_POST["due_at"]."', reference_number = '".$_POST["ref_num"]."' WHERE ".$_POST["id"]." = id";
	
	    //updating invoice
	    $edit->update_invoice ($update);
	    
	    redirect("/invoices/edit?id=".$_POST["id"]."");
	}
    }
    
    //seems like for some reason dun need this (though would be better to use this ^^)
    //$model = handle_post($model, $context);
}

if ( $_SERVER["REQUEST_METHOD"] == "GET") {
    if ($edit->is_valid_get ()) {
	$is_editing = TRUE;
	$obj = new Invoice ();
	$obj->id = $_GET["id"];
       
	if (isset($_GET["dun"])) {
	    $dun_mess = TRUE;
	    $is_editing = FALSE;
	    //making stuff before hand so we can keep track of previous invoices
	    $obj->prev_invoice = $_GET["id"];
	    $obj->cam_id = $_GET["cam_id"];
	    //$obj->cam_id = $_GET["id"];
	}
    }
}

//if there doesn't exist a model of anything we create one

if (!isset($obj)) {
    //creating new base invoice
    if (!$dun_mess) {
	$obj = new Invoice();
	// we are creating new if the id is NULL, otherwise we are editing an old
	$obj->cam_id = NULL;
    }
}

if (!$is_editing) {

    if (!$dun_mess && $obj->cam_id == NULL) {

//	$query = "SELECT DISTINCT campaigns.id,name, starts_at, ends_at FROM campaigns, invoices WHERE campaigns.id NOT IN 
//		   (SELECT campaigns.id FROM invoices, campaigns WHERE (campaigns.id = campaign_id)
//		    OR campaigns.active = 'T')";
	$query = "SELECT DISTINCT ca.id, ca.name, ca.starts_at, ca.ends_at FROM campaigns ca WHERE ca.id = 
		   (SELECT c.id FROM campaigns c JOIN invoices i ON (i.campaign_id = c.id) 
	  WHERE c.active = 'F' GROUP BY c.id HAVING count(i.id)='0')";
       
	//getting necessary rows                                                                                                                                      
	$rows = $edit->select_invoices($query);
	
	//printing results
	$edit->new_invoice_select($rows,$dun_mess);
    }
    
    //where we know which campaign to edit we just print all the information and make it possible to edit what is needed to be edited
    else {
	$edit->new_invoice_modify($obj,$dun_mess);
    }	
}

//we are editing now
else {
    $query = "SELECT DISTINCT id,due_at,reference_number,late_fee,sent,campaign_id,previous_invoice_id FROM invoices WHERE id = ".$obj->id;
    
    $row = $edit->select_invoices($query);
    $edit->edit_invoice ($row);
}

render_template_end($model);

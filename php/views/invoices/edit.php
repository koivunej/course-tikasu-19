<?php

/**
 * view for /invoices/add -- add a new invoice
 */

//Invoice class

class Invoice {
    function __Invoice () {    
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

function handle_post($model, $context) {
    
    // service discovery through $context
    // service call
    // redirection to view page -- redirect_and_exit("/invoices/view?id=" . $inserted_id)
    // exception handling (in case of invalid data): 
    
}

global $context;

$model = array("title" => "add an invoice");

// it's good idea to concentrate everything around editing this single object
// filling the form values each time will be automatic in this case

$is_editing = FALSE; // 

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    
    
    if (!isset($_POST["sent"])) {
	echo "SUM ERROR";
    }
    
    else if ($_POST["sent"] == "high") {
	$model["obj"] = new Invoice();
	//only one value come from high campaign id
	$model["obj"]->cam_id = $_POST["cam_id"];
    }
    
    else if ($_POST["sent"] == "low") {
	//now we can open up db conncetion
	$conn_id = $context->db;
	
	//making insert
	$insert = "INSERT INTO invoices (id, due_at, reference_number, late_fee, sent, campaign_id)
	  VALUES (invoices_id_seq.NEXTVAL,'".$_POST["due_date"]."','".$_POST["ref_num"]."','0','F','".$_POST["cam_id"]."')";
	
	$conn_id->beginTransaction ();
	$conn_id->query($insert);
	
	redirect("/invoices/list");
    }
    
    //if we have edited sumthing
    else if ($_POST["sent"] == "edit") {
	//opening database connection
	$conn_id = $context->db;
	
	//making edit
	$update = "UPDATE invoices SET due_at = '".$_POST["due_at"]."', reference_number = '".$_POST["ref_num"]."' WHERE ".$_POST["id"]." = id";
	
	$conn_id->beginTransaction ();
	$conn_id->query($update);
	
	redirect("/invoices/view?id=".$_POST["id"]."");
    }
    
    //seems like for some reason dun need this (though would be better to use this ^^)
    //$model = handle_post($model, $context);
}

if ( $_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET) {
	$is_editing = TRUE;
	$model["obj"]->id = $_GET["id"];
    }
}

//if there doesn't exist a model of anything we create one

render_template_begin($model);
if (!isset($model["obj"])) {
    //creating new base invoice
    $model["obj"] = new Invoice();
    // we are creating new if the id is NULL, otherwise we are editing an old
    $model["obj"]->cam_id = NULL;
}

if (!$is_editing) {

    if ($model["obj"]->cam_id == NULL) {
	//initializing the form when it's not yet know which campaign we're editing
	echo "<form method=\"post\" action=\"edit\">";
	  echo "<table border = \"1\">";
	  echo "<thead>";
	    echo "<tr>";
	      echo "<td> Name </td>";
	      echo "<td> Start date </td>";
	      echo "<td> End date </td>";
	    echo "</tr>";
	  echo "</thead>";

	//opening connection to database                                                                                                                                                                                                                                                                            
    
	$conn_id = $context->db;

	$query = "SELECT DISTINCT campaigns.id,name, starts_at, ends_at FROM campaigns, invoices WHERE campaigns.id NOT IN 
		   (SELECT campaigns.id FROM invoices, campaigns WHERE (campaigns.id = campaign_id)
		    OR campaigns.active = 'T')";
	
	$conn_id->beginTransaction();
	
	//getting necessary rows                                                                                                                                      
	$rows = $conn_id->query ($query);

	//printing some information about the campaings (not really known what we need to print here)
	echo "<tbody>";
	foreach ($rows as $iter) {
	      echo "<tr>";
	        echo "<td>".$iter["name"]."</td>";
	        echo "<td>".$iter["starts_at"]."</td>";
	        echo "<td>".$iter["ends_at"]."</td>";     
	        echo "<td><input type=\"submit\" value=\"Create\"></td>";
	      echo "</tr>";
	}	
	echo "</tbody>";
	echo "</table>";
	//sending which campaign to edit
	if (isset ($iter)) {
	    echo "<input type=\"hidden\" value=\"".$iter["id"]."\" id=\"cam_id\" name=\"cam_id\">";
	}
	//fake send for post function                                                                                                                 
	echo "<input type=\"hidden\" value=\"high\" id=\"sent\" name=\"sent\">";  
	echo "</form>";
    }
    
    //where we know which campaign to edit we just print all the information and make it possible to edit what is needed to be edited
    else {	
	echo "<form method=\"post\" action=\"edit\">";
	echo "Due date (yyyy-mm-dd): <input type=\"text\" value=\"".$model["obj"]->due_at."\" id=\"due_date\" name=\"due_date\"><br>";
	echo "Reference number (at least 5 letters): <input type=\"text\" value=\"".$model["obj"]->ref_number."\" id=\"ref_num\" name=\"ref_num\"><br>";
	echo "Late fee: ".(string)$model["obj"]->late_fee."<br>";
	echo "Sent: F<br>";
	//lets send fake value because there is stuff to consider ^^
	echo "<input type=\"hidden\" value=\"low\" id=\"sent\" name=\"sent\">";
	echo "Campaign number: ".$model["obj"]->cam_id."<br>";
	echo "<input type=\"hidden\" value=\"".$model["obj"]->cam_id."\" id=\"cam_id\" name=\"cam_id\">";
	echo "<input type=\"submit\" value=\"Save\">";
	echo "</form>";
    }	
}

else {
    //connection to database
    $conn_id = $context->db;
    
    $query = "SELECT DISTINCT id,due_at,reference_number,late_fee,sent,campaign_id,previous_invoice_id FROM invoices WHERE campaign_id = ".$model["obj"]->id;
    
    $conn_id->beginTransaction();
    
    $row = $conn_id->query ($query);
   
    //$uri = "~/".$_SERVER['PATH_INFO'];
    
    //editing invoice
    echo "<form method = \"post\" action=\"edit\">";
    echo "<table>";
    
    foreach ($row as $iter) {
	echo "<tr><td> Id: </td>";
	echo "<td>".$iter["id"]."</td></tr>";
	echo "<tr><td> Due date: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["due_at"]."\" id=\"due_at\" name=\"due_at\"></td></tr>";
	echo "<tr><td> Reference number: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["reference_number"]."\" id=\"ref_num\" name=\"ref_num\"></td></tr>";
	echo "<tr><td> Late fee: </td>";
	echo "<td>".$iter["late_fee"]."</td></tr>";
	echo "<tr><td> Sent: </td>";
	echo "<td>".$iter["sent"]."</td></tr>";
	echo "<tr><td> Campaign number: </td>";
	echo "<td>".$iter["campaign_id"]."</td></tr>";
	echo "<tr><td> Previous invoice: </td>";
	echo "<td>".$iter["previous_invoice_id"]."</td></tr>";
	//making fake value send edit to post handle
	echo  "<input type=\"hidden\" value=\"edit\" id=\"sent\" name=\"sent\">";
	//also sending necesary id so we can easily edit in the database
	echo "<input type=\"hidden\" value=\"".$iter["id"]."\" id=\"id\" name=\"id\">";
    }
    
    echo "</table>";
    echo "<input type=\"submit\" value=\"Save\">";
    echo "</form>";
}

render_template_end($model);

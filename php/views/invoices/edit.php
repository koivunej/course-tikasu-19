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

$model["obj"] = new Invoice();

// we are creating new if the id is NULL, otherwise we are editing an old
$is_editing = FALSE; // $model["obj"]->id == NULL;

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    $model = handle_post($model, $context);
}

if ( $_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET) {
	$is_editing = TRUE;
	$model["obj"]->id = $_GET["id"];
    }
}

render_template_begin($model);

if (!$is_editing) {

    echo "<form method=\"post\">";
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

    $query = "SELECT DISTINCT name, starts_at, ends_at FROM campaigns, invoices WHERE campaigns.id NOT IN 
	   (SELECT campaigns.id FROM invoices, campaigns WHERE (campaigns.id = campaign_id)
	    OR campaigns.active = 'T')";
	
    $conn_id->beginTransaction();

    //gerring necessary rows                                                                                                                                      
    $rows = $conn_id->query ($query);

    foreach ($rows as $iter) {
	echo "<tbody>";
	echo "<tr>";
	echo "<td>".$iter["name"]."</td>";
	echo "<td>".$iter["starts_at"]."</td>";
	echo "<td>".$iter["ends_at"]."</td>";
	echo "<td><input type=\"submit\" value=\"Create\"></td>";
	echo "</tr>";
    }	
   echo "</form>";	
}

else {
    //connection to database
    $conn_id = $context->db;
    
    $query = "SELECT DISTINCT id,due_at,reference_number,late_fee,sent,campaign_id,previous_invoice_id FROM invoices WHERE campaign_id = ".$model["obj"]->id;
    
    $conn_id->beginTransaction();
    
    $row = $conn_id->query ($query);
    
    echo "<form method = \"post\">";
    foreach ($row as $iter) {
	echo "<tr><td> Id: </td>";
	echo "<td>".$iter["id"]."</td></tr><br>";
	echo "<tr><td> Due date: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["due_at"]."\"</td></tr><br>";
	echo "<tr><td> Reference number: </td>";
	echo "<td>".$iter["reference_number"]."</td></tr><br>";
	echo "<tr><td> Late fee: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["late_fee"]."\"</td></tr><br>";
	echo "<tr><td> Sent: </td>";
	echo "<td>".$iter["sent"]."</td></tr><br>";
	echo "<tr><td> Campaign number: </td>";
	echo "<td>".$iter["campaign_id"]."</td></tr><br>";
	echo "<tr><td> Previous invoice: </td>";
	echo "<td>".$iter["previous_invoice_id"]."</td></tr><br>";
    }
    echo "</form>";
}

?>
      
	
<?php
render_template_end($model);

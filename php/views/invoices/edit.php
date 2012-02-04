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
    $model["obj"] = new Invoice();
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
$is_editing = FALSE; // 
$model["obj"]->cam_id = NULL;

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    $model = handle_post($model, $context);
    if ($_POST["sent"] == "high") {
	//only one value come from high campaign id
	$model["obj"]->cam_id = $_POST["cam_id"];
    }
    
    else if ($_POST["sent"] == "low") {
	//now we can open up db conncetion
	$conn_id = $context->db;
	
	//making insert
	$insert = "INSERT INTO invoices (id, due_at, reference_number, late_fee, sent, campaign_id)
	  VALUES (invoices_id_seq.NEXTVAL,".$_POST["due_at"].",".$_POST["ref_number"].",'0','F',".$_POST["cam_id"].")";
	
	$conn_id->beginTransaction ();
	$conn_id->query(insert);
    }
}

if ( $_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET) {
	$is_editing = TRUE;
	$model["obj"]->id = $_GET["id"];
    }
}

render_template_begin($model);

if (!$is_editing) {

    if ($model["obj"]->cam_id == NULL) {
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

	$query = "SELECT DISTINCT campaigns.id,name, starts_at, ends_at FROM campaigns, invoices WHERE campaigns.id NOT IN 
		   (SELECT campaigns.id FROM invoices, campaigns WHERE (campaigns.id = campaign_id)
		    OR campaigns.active = 'T')";
	
	$conn_id->beginTransaction();
	
	//gerring necessary rows                                                                                                                                      
	$rows = $conn_id->query ($query);

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
	echo "<input type=\"hidden\" value=\"".$iter["id"]."\" name=\"cam_id\">";                                                                     
	//fake send for post function                                                                                                                 
	echo "<input type=\"hidden\" value=\"high\" name=\"sent\">";  
	echo "</form>";
    }
    
    else {
	echo "<form method=\"post\">";
	echo "Due date (yyyy-mm-dd): <input type=\"text\" value=\"".$model["obj"]->due_at."\" method=\"post\" name=\"due_date\" ><br>";
	echo "Reference number (at least 5 letters): <input type=\"text\" value=\"".$model["obj"]->ref_number."\" method=\"post\" name=\"ref_number\"><br>";
	echo "Late fee: ".$model["obj"]->late_fee."<br>";
	echo "Sent: F<br>";
	//lets send fake value because there is stuff to consider ^^
	echo "<input type=\"hidden\" value=\"low\" name=\"sent\">";
	echo "Campaign number: ".$model["obj"]->cam_id."<br>";
	echo "<input type=\"hidden\" value=\"".$model["obj"]->cam_id."\" name=\"cam_id\">";
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
    
    echo "<form method = \"post\">";
    foreach ($row as $iter) {
	echo "<table>";
	echo "<tr><td> Id: </td>";
	echo "<td>".$iter["id"]."</td></tr><br>";
	echo "<tr><td> Due date: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["due_at"]."\"</td></tr><br>";
	echo "<tr><td> Reference number: </td>";
	echo "<td><input type=\"text\" value = \"".$iter["reference_number"]."\"</td></tr><br>";
	echo "<tr><td> Late fee: </td>";
	echo "<td>".$iter["late_fee"]."</td></tr><br>";
	echo "<tr><td> Sent: </td>";
	echo "<td>".$iter["sent"]."</td></tr><br>";
	echo "<tr><td> Campaign number: </td>";
	echo "<td>".$iter["campaign_id"]."</td></tr><br>";
	echo "<tr><td> Previous invoice: </td>";
	echo "<td>".$iter["previous_invoice_id"]."</td></tr><br>";
	echo "</table>";
	echo "<input type=\"submit\" value=\"Save\">";
    }
    echo "</form>";
}

?>
      
	
<?php
render_template_end($model);

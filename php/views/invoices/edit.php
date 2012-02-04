<?php

/**
 * view for /invoices/add -- add a new invoice
 */

//Invoice class

class Invoice {
    
}

function handle_post($model, $context) {
    $is_editing = TRUE;
    // service discovery through $context
    // service call
    // redirection to view page -- redirect_and_exit("/invoices/view?id=" . $inserted_id)
    // exception handling (in case of invalid data): 
    
}

global $context;

$model = array("title" => "add an invoice");

// it's good idea to concentrate everything around editing this single object
// filling the form values each time will be automatic in this case

//$model["obj"] = new Invoice();

// we are creating new if the id is NULL, otherwise we are editing an old
$is_editing = FALSE; // $model["obj"]->id == NULL;

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    $model = handle_post($model, $context);
}

render_template_begin($model);
	
if (!is_editing) {

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
    
    $query = "SELECT DISTINCT * FROM campaigns WHERE id = ".model[obj]->id;
    
    $conn_id->beginTransaction();
    
    $row = $conn_id->query ($query);
    
    echo "<form method = \"post\">";
    echo "<tr><td> Id: </td>";
    echo "<td>".$row[id]."<td></tr>";
    echo "</form>";
}

?>
      
	
<?php
render_template_end($model);

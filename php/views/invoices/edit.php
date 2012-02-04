<?php

/**
 * view for /invoices/add -- add a new invoice
 */

function handle_post($model, $context) {
    
    // service discovery through $context
    // service call
    // redirection to view page -- redirect_and_exit("/invoices/view?id=" . $inserted_id)
    // exception handling (in case of invalid data): 
    
}

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
?>
	
<form method="post">
<table border = "1">
	<thead>
		<tr>
			<td> Name </td>
			<td> Start date </td>
			<td> End date </td>
		</tr>
	</thead>
<?php

//opening connection to database                                                                                                                              
global $context;                                                                                                                                              
                                                                                                                                                              
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
?>
</form>
      
	
<?php
render_template_end($model);

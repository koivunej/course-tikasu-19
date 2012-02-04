<?php

/**
 * view for both 
 *  - /invoices
 *  - /invoices/list (redirects to /invoices)
 */

$model = array("title" => "invoices listing");

render_template_begin($model);
?>

<table border="1">
	<thead>
		<tr>
			<td>Invoice number</td>
			<td>Advertiser name</td>
			<td>Is active</td>
			<td colspan="2">actions</td>
		</tr>
    	</thead>
<?php

//opening connection to database
global $context;

$conn_id = $context->db;

//making the query
$query = "SELECT reference_number, advertisers.name FROM invoices, advertisers, campaigns WHERE invoices.campaign_id = campaigns.id
  AND campaigns.adv_vat = advertisers.VAT";

$conn_id->beginTransaction();

//gerring necessary rows
$rows = $conn_id->query ($query);

foreach ($rows as $iter) {
    echo "<tbody>";
      echo "<tr>";
        echo "<td>".$iter["reference_number"]."</td>";
        echo "<td>".$iter["name"]."</td>";
        echo "<td><input type=\"checkbox\" name=\"".$iter["name"]."\" value=\"active\"></td>";
        echo "<td>"; 
        echo_link('/invoices/view?id='.$iter["reference_number"], 'View');
        echo "</td>";
        echo "<td>";
        echo_link('/invoices/edit?id='.$iter["reference_number"], 'Edit');
        echo "</td>";
      echo "</tr>";
    echo "</tbody>";
}
	
echo "</table>";  

render_template_end($model);

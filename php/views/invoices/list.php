<?php

/**
 * view for both 
 *  - /invoices
 *  - /invoices/list (redirects to /invoices)
 */
//checking if user is good                                                                                                                                    
UserDetailsContext::assertRoles(array("ROLE_ACCOUNTING")); 

$model = array("title" => "invoices listing");

render_template_begin($model);
?>

<table border="1">
	<thead>
		<tr>
			<td>Invoice number</td>
			<td>Advertiser name</td>
			<td>Due at</td>
			<td>Sent</td>
			<td colspan="2">Actions</td>
		</tr>
    	</thead>
<?php

//opening connection to database
global $context;

$conn_id = $context->db;

//making the query
$query = "SELECT invoices.id, invoices.due_at, invoices.sent, advertisers.name "
	 . "FROM invoices JOIN campaigns ON (invoices.campaign_id = campaigns.id) "
		       . "JOIN advertisers ON (advertisers.VAT = campaigns.adv_vat) "
     . "ORDER BY invoices.id DESC";

$tx = $conn_id->beginTransaction();
$rows = $conn_id->query ($query);
$tx->commit();

foreach ($rows as $iter) {
    echo "<tbody>";
      echo "<tr>";
        echo "<td>".$iter["id"]."</td>";
        echo "<td>".$iter["name"]."</td>";
	echo '<td>'.$iter["due_at"].'</td>';
	echo '<td>'.$iter["sent"].'</td>';
        echo "<td>"; 
        echo_link('/invoices/view?id='.$iter["id"], 'View');
        echo "</td>";
        echo "<td>";
        echo_link('/invoices/edit?id='.$iter["id"], 'Edit');
        echo "</td>";
      echo "</tr>";
    echo "</tbody>";
}
	
echo "</table>"; 



render_template_end($model);

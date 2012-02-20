<?php
//checking if user is good                                                                                                                                    
UserDetailsContext::assertRoles(array("ROLE_ACCOUNTING"));


$model["title"] = "invoice details";
render_template_begin($model);
?>

<table border="0">
<?php
global $context;

function sendinvoice() {
    $to = "";
    $subject = "lasku";
    $contents = "";
    mail($to, $subject, $contents, "tikasu");
}

function printti($left, $right) {
    echo "<tbody>";
    echo "<tr>";
    echo "<td>".$left."</td>";
    echo "<td>".$right."</td>";
    echo "</tr>";
    echo "</tbody>";
}

function addinfo($row) {
    
    foreach ($rows as $iter) {
	echo "<tr>";
	echo "<td>ad name: ".$iter["name"]."</td>";
	echo "<td>duration: ".$iter["duration"]."</td>";
	$airq = "SELECT aired_at FROM ad_airings WHERE ad_id = ";
	$airq .= $iter["id"];
	printti($airq, "aired_at");
    }
}

function typer($totype) {
    echo "<tr>";
        echo "<td>".$totype."</td>";
    echo "<tr>";
}
?>

<h2> Report </h2>
<?php
//handling get
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    //checking if correct get key exists
    if (array_key_exists("id", $_GET)) {
	//once we're fairly sure it exists we can get necessary information
	//getting invoice by id
	$tx = $context->db->beginTransaction();
	$model["invoices"] = $context->invoiceService->getById($_GET["id"]);
	$model["campaigns"] = $context->campaignService->getById($model["invoices"]->campaign_id);
	$model["advertisers"] = $context->advertiserService->getByVAT($model["campaigns"]->adv_vat);
	$model["addresses"] = $context->addressService->getById($model["advertisers"]->address_id);
	$model["contactpersons"] = $context->contactpersonService->getById($model["campaigns"]->contactperson_id);
	$model["cities"] = $context->cityService->getById($model["addresses"]->city_id);
	$model["ads"] = $context->adService->getByCamId($model["campaigns"]->id);
	$tx->commit();
    }
	
    //id there is no id we just redirect back to invoice list
    else {
	redirect("/invoices/list");
    }
}


//printing customer information
typer("Customer Information:");
typer("------------------------------------");
typer("<br>");

//name
printti("First name: ", $model["contactpersons"]->firstname);
printti("Last name: ", $model["contactpersons"]->lastname);
typer("<br>");

//company
printti("Advertiser name: ", $model["advertisers"]->name);

//and address
printti("Address", $model["addresses"]->address);
//and city:
printti("City", $model["cities"]->name);
typer("<br>");

//Information about the campaign:
typer("Campaign information:");
typer("------------------------------------");
printti("Name: ", $model["campaigns"]->name);
printti("Begin date: ", $model["campaigns"]->starts_at);
printti("End date: ", $model["campaigns"]->ends_at);
printti("Price per second: ", $model["campaigns"]->price_per_second);
printti("Budget", $model["campaigns"]->budget);
typer("<br>");

//ads can be found by campaign_id:
typer("Aired Ads:");
typer("------------------------------------");
//total amount
$total = 0;
foreach ($model["ads"] as $iter) {
    $total += count($model)*$model["campaigns"]->price_per_second*$iter->duration;
    printti ("Name: ", $iter->name);
    printti("Duration: ", $iter->duration);
    //now we have to summon special model :P
    $tx = $context->db->beginTransaction();
    $model["ad_airings"] = $context->adAiringService->getByAdId($iter->id);
    $tx->commit();
    printti ("Ad Airing count: ", count($model));
    printti ("Price: ", count($model)*$model["campaigns"]->price_per_second*$iter->duration);
    typer("<br>");
    
}

//billing information
typer("Billing information");
typer("------------------------------------"); 
//bankaccount:
typer("Bank account: XXXX-XXXX");
//duedate:
printti("Due date: ", $model["invoices"]->due_at);
printti("Reference number: ", $model["invoices"]->reference_number);
if ($model["invoices"]->previous_invoice_id !== NULL) {
	printti("Late fee: ", $model["invoices"]->late_fee);
}
printti("Total sum: ", $total + $model["invoices"]->late_fee);
//amount

sendinvoice();
?>
</table>

<h2>actions</h2>

<ul>
<li><?php echo_link('/invoices/edit?id='.$model["invoices"]->id, 'Edit invoice');?></li>
<li><?php echo_link( ($model["invoices"]->sent == 'T' ? NULL : '/invoices/send?id='.$model["invoices"]->id) , 'Send invoice');?></li>
<li><?php echo_link('/invoices/delete?id='.$model["invoices"]->id, 'Delete invoice');?></li>
</ul>

<?php
render_template_end($model);

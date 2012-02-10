<?php

$model["title"] = "invoice details";
render_template_begin($model);
?>

<table border="0">
<?php

function sendinvoice() {
    $to = "";
    $subject = "lasku";
    $contents = "";
    mail($to, $subject, $contents, "tikasu");
}

function printti($query, $printthis) {
global $context;

$conn_id = $context->db;

$conn_id->beginTransaction();

$rows = $conn_id->query($query);

foreach ($rows as $iter) {
    echo "<tbody>";
      echo "<tr>";
             echo "<td>".$printthis."</td>";
	     echo "<td>".$iter[$printthis]."</td>";
      echo "</tr>";
    echo "</tbody>";
  }
}

function addinfo($query3) {
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction();
    $rows = $conn_id->query($query3);
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

function getkey($query2, $cid) {
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction();
    $k = $conn_id->query($query2);
    foreach ($k as $i){
	//return $i["campaign_id"];
	return $i[$cid];
    }
}
?>

<h2> Report </h2>
<?php
//queryes:
//get corresponding id:s for invoice-printing:
//get campaings id:
$q = "SELECT campaign_id FROM invoices WHERE reference_number = "; // WHERE reference_number = ";
$q .= $_GET['id'];
$campaign_id = getkey($q, "campaign_id");
//get contactpersons id:
$q = "SELECT contactperson_id FROM campaigns WHERE id = ";
$q .= $campaign_id;
$contactperson_id = getkey($q, "contactperson_id");
//qet advertisers VAT:
$q = "SELECT adv_vat FROM campaigns WHERE id = ";
$q .= $campaign_id;
$adv_vat = getkey($q, "adv_vat");
//advertisers information:
$q = "SELECT address_id FROM advertisers WHERE vat = '";
$q .= $adv_vat;
$q .= "'";
$address_id = getkey($q, "address_id");
$q = "SELECT city_id FROM addresses WHERE id = ";
$q .= $address_id;
$city_id = getkey($q, "city_id");

typer("Customer Information:");
//print name:
$q = "SELECT firstname, lastname FROM contactpersons WHERE id = ";
$q .= $contactperson_id;
printti($q, "firstname");
printti($q, "lastname");
//company:
$q = "SELECT name FROM advertisers WHERE vat = '";
$q .= $adv_vat;
$q .= "'";
printti($q, "name");
//and address
$q = "SELECT address FROM addresses WHERE id = ";
$q .= $address_id;
printti($q, "address");
//and city:
$q = "SELECT name FROM cities WHERE id = ";
$q .= $city_id;
printti($q, "name");

//Information about the campaign:
typer("Campaign to invoice:");
$q = "SELECT name, starts_at, ends_at, budget, price_per_second FROM campaigns WHERE id = ";
$q .= $campaign_id;
printti($q, "name");
printti($q, "starts_at");
printti($q, "ends_at");
printti($q, "price_per_second");
printti($q, "budget");

//ads can be found by campaign_id:
typer("Aired Ads:");
$q = "SELECT id, name, duration FROM ads WHERE campaign_id = ";
$q .= $campaign_id;
addinfo($q);

typer("Billing information");
//bankaccount:
typer("Bank account: XXXX-XXXX");
//duedate:
$q = "SELECT due_at, reference_number FROM invoices WHERE reference_number = ";
$q .= $_GET['id'];
printti($q, "due_at");
//reference
$q = "SELECT reference_number FROM invoices WHERE reference_number = ";
$q .= $_GET['id'];
printti($q, "reference_number");
//amount
typer("Amount:");
typer("tocount");
sendinvoice();
?>
</table>

<!-- for simple key: value data use dl -->

<h2>actions</h2>

<ul>
<li><?php echo_link('/invoices/edit?id=123', 'Edit invoice');?></li>
<li><?php echo_link('/invoices/send?id=123', 'Send invoice');?></li>
<li><?php echo_link('/invoices/delete?id=123', 'Delete invoice');?></li>
</ul>

<?php
render_template_end($model);

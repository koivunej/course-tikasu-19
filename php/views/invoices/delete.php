<?php

$model["title"] = "invoice deleted";
render_template_begin($model);
?>

<?php

function removefromdb($query2) {
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction();
    $conn_id->query($query2);
}
?>

<h2> Invoice deleted </h2>
<?php
//query for delete:
$q = "DELETE FROM invoices WHERE reference_number = ";
$q .= $_GET['id'];
removefromdb($);
//get contactpersons id:
<!-- for simple key: value data use dl -->

<h2>actions</h2>

<ul>
</ul>

<?php
render_template_end($model);

<?php
if(isset($_POST['submit'])) {
removefromdb();
}

$model["title"] = "invoice deleted";
render_template_begin($model);
?>

<?php
function removefromdb() {
    global $context;
    $conn_id = $context->db;
    $conn_id->beginTransaction();
	$query2 = "DELETE FROM invoices WHERE id = ";
	$query2 .= $_GET['id'];
    $conn_id->executeUpdateForRowCount(1, $query2, $args);
}
?>

<form action="<?=$_SERVER['PHP_SELF'];?>" method="post">
<input type="button" name="submit" value="Remove">
</form>

<h3>Press button and invoice is removed</h3>

<ul>
</ul>

<?php
render_template_end($model);

<?php
global $context;

if(isset($_POST['submit'])) {
    removefromdb();
}

$model["title"] = "invoice deleted";
render_template_begin($model);
?>

<?php
function removefromdb() {
 $context->InvoiceService->remove($_GET['id']);
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

<?php
global $context;

if(isset($_POST['submit'])) {
    $context->invoiceService->remove($_GET['id']);
    redirect("/invoices/list");
    exit(0);
}

$model["title"] = "delete invoice";
render_template_begin($model);
?>

<form method="post">
<input type="submit" name="submit" value="Delete invoice"/>
</form>

<?php
render_template_end($model);

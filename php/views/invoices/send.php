<?php
//checking if user is good                                                                                                                                  
UserDetailsContext::assertRoles(array("ROLE_ACCOUNTING"));

       

global $context;

if(isset($_POST['submit'])) {
    $context->invoiceService->send($_GET['id']);
    redirect("/invoices/view?id=" . $_GET["id"]);
    exit(0);
}

$model["title"] = "send invoice";
render_template_begin($model);
?>

<form method="post">
<input type="submit" name="submit" value="Send the invoice"/>
</form>

<?php
render_template_end($model);

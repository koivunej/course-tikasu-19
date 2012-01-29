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
	
	<input type="submit" value="<?php if ($is_editing) { echo "Save"; } else { echo "Create"; } ?>"/>
</form>
	
<?php
render_template_end($model);

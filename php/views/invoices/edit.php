<?php

/**
 * view for /invoices/add -- add a new invoice
 * also edit and dun invoice
 */

//accepts "dun" get message which is just dummy to know if we're doing dun invoice
//with the dun message there should be the id of previous invoice as "id"
//also campaign id should be sent as "campaign_id"


global $context;

$model = array("title" => "add an invoice");


function bind_post_vars($instance, $optional = array()) {

	$not_found = array();

	foreach (get_class_vars($instance) as $key => $unused_value) {
		if (!array_key_exists($key, $_POST) && !array_value_exists($key, $optional)) {
			$not_found[] = $key;
		} else {
			$val = $_POST[$key];
			if ($val == "NULL") {
				$val = NULL;
			}
			$instance->$key = $val;
		}
	}
	
	if (count($not_found) > 0) {
		throw new Exception("Not all required values were bound: " . implode(", ", $not_found) . "; all post keys: " . implode(", ", array_keys($_POST)));
	}
}

$tx = $context->db->beginTransaction();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// bind values
	$model["obj"] = new Invoice();
	
	if (array_key_exists("campaign_id", $_POST)) {
		// TODO: check that there are no other values
		$model["obj"]->campaign_id = intval($_POST["campaign_id"]);
		$model["obj"]->previous_invoice_id = $context->invoiceService->findPreviousInvoice($model["obj"]->campaign_id);
	} else {
		// if campaign_id is not found here, it'll be an error
		bind_post_vars($model["obj"], array("id", "sum", "late_fee", "previous_invoice_id"));
		
		try {
			$context->invoiceService->saveOrUpdate($model["obj"]);
			$tx->commit();
			redirect("/invoices/view?id=" . $model["obj"]->id);
		} catch (DataAccessException $e) {
			$tx->rollback();
			$model["errors"][] = $e->getMessage();
		}
	}
	
} else {
	$model["obj"] = new Invoice();
}

render_template_begin($model);

if ($model["obj"]->campaign_id !== NULL) {
	$campaign = $context->campaignService->getById($model["obj"]->campaign_id);

	if ($campaign == NULL) {
		// someone must have just deleted the campaign?
		$model["errors"][] = "Sorry, campaign was just deleted, pick another one";
		$model["obj"]->campaign_id = NULL;
	} else {
		$model["campaign_name"] = $campaign->name;
	}
}

// DOUBLE CHECKING THE campaign_id ON PURPOSE; it might had been deleted between requests!
if ($model["obj"]->campaign_id == NULL) {
	$model["campaigns"] = $context->campaignService->findInvoiceableCampaigns();
	if (count($model["campaigns"]) == 0) {
		?>
		<p>Sorry, no campaign needs invoices at the moment!</p>
		<?php
		render_template_end();
		$tx->commit();
		exit();
	}
}

if ($model["obj"]->previous_invoice_id !== NULL) {
	$prev_invoice = $context->invoiceService->getById($model["obj"]->previous_invoice_id);
	if ($prev_invoice == NULL) {
		$tx->commit();
		die("Previous invoice was deleted or you spoofed it :(");
	}
	$model["previousInvoiceDue"] = $prev_invoice->due_at;
}

?>

<form method="post">
	<label for="campaign_id_selection">Campaign:</label>
        <?php if ($model["obj"]->campaign_id == NULL) {?>
	<select id="campaign_id_selection" name="campaign_id">
		<?php foreach ($model["campaigns"] as $id => $name) { ?>
		<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
		<?php } ?>
		
		<input type="submit" value="Continue" />
	</select>
	<?php } else { ?>
		<input type="text" value="<?php echo $model["campaign_name"]; ?>" disabled="disabled" />
		<input type="hidden" name="campaign_id" value="<?php echo $model["obj"]->campaign_id;?>" />
		
		<label for="reference_number_text">Reference number:</label>
		<input type="text" name="reference_number" id="reference_number_text" value="<?php echo $model["obj"]->reference_number; ?>" />

		<label for="due_date_text">Due date:<?php if ($model["obj"]->previous_invoice_id !== NULL) { echo " (Previous was: " . $model["previousInvoiceDue"] . ")"; } ?></label>
		<input type="text" name="due_date" id="due_date_text" value="<?php echo $model["obj"]->due_date; ?>" />
		
		<label for="sum_text">Sum:</label>
		<input type="text" name="sum" id="sum_text" value="<?php echo $model["obj"]->sum; ?>" disabled="disabled" />
		
		<?php if ($model["obj"]->previous_invoice_id !== NULL) { ?>
			<input type="hidden" name="previous_invoice_id" value="<?php echo $model["obj"]->previous_invoice_id; ?>" />
			<label for="late_fee_text">Late fee:</label>
			<input type="text" name="late_fee" id="late_fee_text" value="<?php echo $model["obj"]->late_fee; ?>" />
		<?php } else { ?>
			<input type="hidden" name="previous_invoice_id" value="NULL" />
		<?php } ?>
		
		<input type="hidden" name="sent" value="<?php echo $model["obj"]->sent; ?>" />
		
		<?php
		$save_button_text = $model["obj"]->id == NULL ? "Save" : "Update";
		?>
		<input type="submit" value="<?php echo $save_button_text; ?>" />
	<?php } ?>
</form>


<?php
render_template_end($model);
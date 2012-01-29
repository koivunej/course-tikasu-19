<?php

$model["title"] = "invoice details";

render_template_begin($model);
?>

<dl>
	<dt>contact person</dt>
	<dd>contact person details</dd>
	<dt>customer</dt>
	<dd>customer details</dd>
	<dd>customer address</dd>
	
	<dt>invoice number</dt>
	<dd>1234567</dd>
	
	<!-- other data -->
</dl>
	
<h2>report</h2>
	
<!-- for simple key: value data use dl -->

<h2>actions</h2>
	
<ul>
<li><?php echo_link('/invoices/edit?id=123', 'Edit invoice');?></li>
<li><?php echo_link('/invoices/send?id=123', 'Send invoice');?></li>
<li><?php echo_link('/invoices/delete?id=123', 'Delete invoice');?></li>
</ul>

<?php
render_template_end($model);

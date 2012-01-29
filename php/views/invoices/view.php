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
		
<?php
render_template_end($model);

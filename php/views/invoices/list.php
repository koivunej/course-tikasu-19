<?php

/**
 * view for both 
 *  - /invoices
 *  - /invoices/list (redirects to /invoices)
 */

$model = array("title" => "invoices listing");

render_template_begin($model);
?>

<table border="1">
	<thead>
		<tr>
			<td>col 1</td>
			<td>col 2</td>
			<td>col n</td>
			<td colspan="2">actions</td>
		</tr>
    	</thead>
	<tbody>
		<tr>
			<td>value 1</td>
			<td>value 2</td>
			<td>value n</td>
			<td><?php echo_link('/invoices/view?id=123', "View"); ?></td>
			<td><?php echo_link('/invoices/edit?id=123', 'Edit'); ?></td>
		</tr>
	</tbody>
</table>
	
<?php
render_template_end($model);

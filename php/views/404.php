<?php

global $model;

if (!isset($model)) {
    $model = array();
}

$model['title'] = '404 not found';

render_template_begin($model);

if (array_key_exists('requested_resource', $model)) {
?>
	<p>sorry, we do not have anything for <em><?php echo $model['requested_resource']; ?></em>.</p>
<?php
}
	
render_template_end($model);

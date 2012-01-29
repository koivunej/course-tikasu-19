<?php

// the internal error page

if (!isset($model)) {
    $model = array();
}

$model["title"] = "internal error";

render_template_begin($model);

?>
	<pre><?php echo $model["exception"]; ?></pre>
<?php

render_template_end($model);

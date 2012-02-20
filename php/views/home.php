<?php

$model = array('title' => 'home');

render_template_begin($model);



$username = UserDetailsContext::getUsernameOrNull();
if ($username !== NULL) {
?>

<p>Welcome <?php echo $username; ?>

<?php
} else {
?>

<p>Welcome! Please <?php echo_link('/login', 'log in');?>!</p>

<?php
}

render_template_end($model);

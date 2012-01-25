<?php

$model = array('title' => 'home');

render_template_begin($model);

?>
	
<dl>
	<dt>authenticated</dt>
	<dd><?php $auth = UserDetailsContext::isAuthenticated(); echo ($auth == TRUE ? "yes" : "no"); ?></dd>
	<dt>username</dt>
	<dd><?php $username = UserDetailsContext::getUsernameOrNull(); echo ($username == NULL ? "not authenticated" : $username); ?></dd>
</dl>

	
<h2>session</h2>
<pre><?echo var_dump($_SESSION); ?></pre>

<ul>
	<li>
		<a <?php if (!UserDetailsContext::isAuthenticated()) { echo 'href="' . link_to_url('/login') . '"'; } ?>>login</a>
	</li>
	<li>
		<a href="<?php echo link_to_url('/logout'); ?>">logout</a>
	</li>
</ul>
	
<?php

render_template_end($model);

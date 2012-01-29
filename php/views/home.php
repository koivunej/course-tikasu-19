<?php

$model = array('title' => 'home');

render_template_begin($model);

?>

<h2>authentication state</h2>	
<dl>
	<dt>authenticated</dt>
	<dd><?php $auth = UserDetailsContext::isAuthenticated(); echo ($auth == TRUE ? "yes" : "no"); ?></dd>
	<dt>username</dt>
	<dd><?php $username = UserDetailsContext::getUsernameOrNull(); echo ($username == NULL ? "not authenticated" : $username); ?></dd>
</dl>

	
<h2>session</h2>
<pre><?echo var_dump($_SESSION); ?></pre>

<?php

render_template_end($model);

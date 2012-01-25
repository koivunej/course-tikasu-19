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

<pre><?echo var_dump($_SESSION); ?></pre>

<?php

render_template_end($model);

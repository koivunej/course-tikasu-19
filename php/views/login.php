<?php

/* simple login controller + view */

function handle_post($model, $context) {

    $users = $context->getUserDetailsService();
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
	$userdetails = $users->authenticate($username, $password);
	UserDetailsContext::initialize($userdetails);
	redirect('/'); // will exit
    } catch (BadCredentialsException $e) {
	$model['errors'] = 'Wrong username or password';
	return;
    } catch (DataAccessException $e) {
	$model['errors'] = 'Temporary database error, please try again later';
	return;
    }
    
    die('Internal error'); // shouldn't get here
}

$model = array();
$model['title'] = 'login';
$model['username'] = '';

if ( $_METHOD = 'POST' ) {
    handle_post($model, $context);
}

include 'site_header.php';
?>

<h1>login</h1>

<?php
if (isset($model['errors']) && count($model['errors']) > 0) {
    echo '<ul class="errors">\n';
    
    foreach ($model['errors'] as $msg) {
	echo '<li>' . $msg . '</li>\n';
    }

    echo '</ul>\n';
}
?>

<form method="post">
	<input type="text" name="username" value="<?php echo $model['username']; ?>" />
	<input type="password" name="password" />
	<input type="submit" value="login" />
</form>

<?php
include 'site_footer.php';

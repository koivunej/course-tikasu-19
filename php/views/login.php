<?php

/* simple login controller + view */

function handle_post($model, $context) {
    
    $users = $context->userDetailsService;
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $model['username'] = $username;
    
    try {
	$userdetails = $users->authenticate($username, $password);
	UserDetailsContext::initialize($userdetails);
	redirect_and_exit('/'); // will exit
    } catch (BadCredentialsException $e) {
	$model['errors'] = 'Wrong username or password';
	return $model;
    } catch (UnauthorizedUserException $e) {
	$model["errors"] = "You don't have any rights granted on this system, contact someone.";
	return $model;
    } catch (DataAccessException $e) {
	$model['errors'] = 'Temporary database error, please try again later';
	return $model;
    }
    
    die('Internal error'); // shouldn't get here
}

global $context;

$model = array();
$model['title'] = 'authenticate';
$model['username'] = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $model = handle_post($model, $context);
}

render_template_begin($model);
?>

<?php
// TODO: this ought to be refactored to more general place
if (isset($model['errors']) && count($model['errors']) > 0) {
    echo '<ul class="errors">';
    
    $errors = $model['errors'];
    
    if (!is_array($errors)) {
	$errors = array($errors);
    }
    
    foreach ($errors as $msg) {
	echo '<li>' . $msg . '</li>';
    }

    echo '</ul>';
}
?>

<form method="post">
	<input type="text" name="username" value="<?php echo $model['username']; ?>" />
	<input type="password" name="password" />
	<input type="submit" value="login" />
</form>

<?php
render_template_end($model);

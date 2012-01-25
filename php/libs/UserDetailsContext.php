<?php

class UserDetailsContext {

    static function attach() {
	session_start();
	if (!array_key_exists('valid', $_SESSION)) {
	    $_SESSION['valid'] = FALSE;
	}
    }
    
    static function detach() {
	if (!array_key_exists('valid', $_SESSION) || $_SESSION['valid'] !== TRUE) {
	    session_destroy();
	}
    }
    
    static function initialize($userDetails) {
	$_SESSION['udc.userdetails'] = $userDetails;
	$_SESSION['valid'] = TRUE;
    }
    
    static function getRequiredUser() {
	if (session_id() == "") {
	    die("No current session");
	}
	
	if (!array_key_exists('udc.userdetails', $_SESSION)) {
	    die("No userdetails");
	}
	
	return UserDetailsContext::getUserOrNull();
    }
    
    static function getUserOrNull() {
	if (isset($_SESSION) && array_key_exists('udc.userdetails', $_SESSION)) {
	    return $_SESSION['udc.userdetails'];
	}
	return NULL;
    }
    
    static function getUsernameOrNull() {
	$user = UserDetailsContext::getUserOrNull();
	if ($user !== NULL) {
	    return $user->username;
	}
	return NULL;
    }
    
    static function destroy() {
	session_destroy();
    }
    
    static function isAuthenticated() {
	return (session_id() !== "") && (array_key_exists('udc.userdetails', $_SESSION));
    }
    
    static function assertAuthenticated() {
	if (!isAuthenticated()) {
	    redirect_to_unauthorized();
	}
    }
    
    static function assertRoles($roleNames) {
	if (!isAuthenticated() || count(array_intersect($roleNames, getRequiredUser()->roles)) != count($roleNames)) {
	    redirect_to_unauthorized();
	}
    }
    
}


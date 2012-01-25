<?php

class UserDetailsService {
    
    var $context;
    var $systemSalt;
    
    function __construct($context) {
	$this->context = $context;
    }
    
    function authenticate($username, $password) {
	$db = $this->context->getDatabaseConnection();
	
	$tx = $db->beginTransaction();
	
	try {
	    $results = $db->query('SELECT * FROM users WHERE username = ? AND password = ?',
				  array($username, hash($password, salt($username))));

	    if (count($results) == 0) {
		throw new BadCredentialsException();
	    }
	    
	    $result = new UserDetails();
	    
	    $db->hydrate($results[0], $result, array('roles'));
	    
	    $roles = $db->query('SELECT name FROM roles r JOIN users_roles ur ON (ur.role_id = r.id) WHERE ur.user_id = ?',
				array($result->id));
	    
	    $result->roles = $roles;
	    
	    return $result;
	} finally {
	    $tx->commit();
	}
    }

    function salt($input) {
	$salt = '{' . $input . '}';
	if (isset($this->systemSalt)) {
	    $salt = $salt . $this->systemSalt;
	}
	return $salt;
    }
    
    function hash($password, $salt = '') {
	return hash('sha512', $password . $salt);
    }
	
}

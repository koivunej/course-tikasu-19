<?php

class UserDetailsService {
    
    var $context;
    var $systemSalt;
    
    function __construct($context) {
	$this->context = $context;
    }
    
    function authenticate($username, $password) {
	$db = $this->context->db;
	
	$tx = $db->beginTransaction();
	
	try {
	    $results = $db->queryAtMostOneResult('SELECT * FROM users WHERE username = ? AND password = ?',
				  array($username, $this->hash($password, $this->salt($username))));

	    if ($results == NULL) {
		throw new BadCredentialsException();
	    }
	    
	    $result = new UserDetails();
	    
	    $db->hydrate($result, $results, array('roles'));
	    
	    $roles = $db->query('SELECT r.name as name FROM roles r JOIN users_roles ur ON (ur.role_id = r.id) WHERE ur.user_id = ?',
				array($result->id));
	    
	    if (count($roles) == 0) {
		throw new UnauthorizedUserException();
	    }

	    $result->roles = array();

	    foreach ($roles as $row) {
		$result->roles[] = $row['name'];
	    }
	    
	    return $result;
	} catch (Exception $e) {
	    $tx->commit();
	    throw $e;
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
    
    static function createUserDetailsService($context) {
	return new UserDetailsService($context);	
    }
	
}

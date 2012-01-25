<?php

// entity class for table Users
// properties must match columns

class UserDetails {
    public $id;
    public $username;
    public $password;
    
    public $roles;
    
    function __construct() {
	$this->roles = array();
    }
}

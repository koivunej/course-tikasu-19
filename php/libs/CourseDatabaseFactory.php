<?php

/**
 * course database factory; sets up the solid the way it should be set up.
 * 
 * invoked normally through first Context->db property access, once having
 * been hooked up through the index.php configuration.
 */

class CourseDatabaseFactory {
    // this needs to be static so that we can use it as a callback
    static function createDatabaseConnection() {
    
	$db_conf_include = dirname(dirname(__FILE__)) . '/database-settings.php';
    
	if (!file_exists($db_conf_include)) {
	    throw new DataAccessException("No database configuration found!");
	}
    
	include $db_conf_include;
    
	$proto = $settings['proto'];
	$host = $settings['host'];
	$port = $settings['port'];
	$username = $settings['username'];
	$password = $settings['password'];
    
	return new SolidODBCDatabaseConnection($proto . ' ' . $host . ' ' . $port, $username, $password);	
    }
}

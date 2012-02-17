<?php

class AddressService {
    var $context;
    
    function __construct ($context) {
	$this->context = $context;
    }
    
    public function getById ($id) {
	
	$db = $this->context->db;
	
	$tx = $db->beginTransaction();                                                                                                                
	                                
	$args = array($id);             
	$sql = "SELECT * FROM addresses WHERE id = ?";
	                                              
	try {                                         
	    $results = $db->queryAtMostOneResult($sql, $args);
	} catch (Exception $e) {                                                                                                                      
	    $tx->commit();                                    
	    throw $e;                                                                                                                             
	}                                                                                                                                             
	                                                      
	$tx->commit();                                        
	                                                      
	$ret = new Address();                                                                                                                        
	
	$db->hydrate($ret, $results);
	
	return $ret; 
    }
    
    static function createAddressService ($context) {
	return new AddressService ($context);
    }
}

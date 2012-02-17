<?php

class ContactpersonService {
    var $context;
    
    function __construct ($context) {
	$this->context = $context;
    }
    
    function getById ($id) {                 
	$db = $this->context->db;                                                                                                                     
	                 
	$tx = $db->beginTransaction();                                                                                                                
	 
	$args = array($id);                                                                                                                           
	$sql = "SELECT * FROM contactpersons WHERE id = ?";                                                                                                
	                    
	try {                                                                                                                                         
	    $results = $db->queryAtMostOneResult($sql, $args);                                                                                    
	} catch (Exception $e) {                                                                                                                      
	    $tx->commit();                                                                                                                        
	    throw $e;                                                                                                                             
	}                                                                                                                                             
	
	$tx->commit();                                                                                                                                
	
	$ret = new Contactperson ();                                                                                                                        
	
	$db->hydrate($ret, $results);                                                                                                                 
	
	return $ret;  
    }
    
    static function createContactpersonService ($context) {
	return new ContactpersonService ($context);
    }
}

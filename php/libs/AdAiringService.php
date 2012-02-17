<?php

class AdAiringService {
    var $context;
    
    function __construct ($context) {
	$this->context = $context;
    }
    
    function getByAdId ($id) {
	$db = $this->context->db;
	
	$tx = $db->beginTransaction();
	
	$args = array($id);            
	$sql = "SELECT * FROM ad_airings WHERE ad_id = ?";                                                                                                     
	
	try {                                                                                                                                                 
	    $results = $db->query($sql, $args);                                                                                                               
	}                                                                                                                                                     
	
	catch (Exception $e) {                                                                                                                                
	    $tx->commit();                                                                                                                                    
	    throw $e;                                                                                                                                         
	}                                                                                                                                                     
	
	$tx->commit();                                                                                                                                        
	
	$rest = array();
	$ret = new AdAiring();                                                                                                                                      
	
	foreach ($results as $iter) {                                                                                                                         
	    $db->hydrate($ret, $iter);                                                                                                                        
	    $rest[] = $ret;                                                                                                                                   
	}                                                                                                                                                     
	                                                                                                                                                              
	return $rest; 
    }
    
    static function createAdAiringService ($context) {
	return new AdAiringService ($context);
    }
}

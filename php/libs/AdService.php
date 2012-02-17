<?php

class AdService {
    var $context;
    
    function __construct ($context) {
	$this->context = $context;
    }
    
    function getByCamId ($campaign_id) {
	$db = $this->context->db;                                                                                                                     
	
	$tx = $db->beginTransaction();                                                                                                                
	
	$args = array($campaign_id);                                                                                                                           
	$sql = "SELECT * FROM ads WHERE campaign_id = ?";                                                                                                
	
	try {                                                                                                                                         
	    $results = $db->query($sql, $args);                                                                                    
	} 
	
	catch (Exception $e) {                                                                                                                      
	    $tx->commit();                                                                                                                        
	    throw $e;                                                                                                                             
	}                                                                                                                                             
	
	$tx->commit();                                                                                                                                
	$rest = array();
	$ret = new Ad(); 
	
	foreach ($results as $iter) {
	    $db->hydrate($ret, $iter);
	    $rest[] = $ret;
	}
	
	return $rest;  
    }
    
    static function createAdService ($context) {
	return new AdService ($context);
    }
}

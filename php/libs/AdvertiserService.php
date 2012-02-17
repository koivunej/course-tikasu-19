<?php

class AdvertiserService {
    
    var $context;
    
    function __construct ($context) {
	$this->context = $context;
    }
    
    function getByVAT ($VAT) {
	//querying at most one result from select                                                                                                         
	$db = $this->context->db;                                                                                                                     
	
	$tx = $db->beginTransaction();
	
	try {
	    $results = $db->queryAtMostOneResult('SELECT * FROM advertisers WHERE VAT = ?', array($VAT));
	}
	
	catch (Exception $e) {
	    $tx->commit();
	    throw $e;
	}
	
	$tx->commit();
	//merging results to new advertiser class                                                                                                            
	$ret = new Advertiser();                                                                                                                         
	$db->hydrate($ret, $results);                                                                                                   
	
	//returning the advertiser class                                                                                                                     
	return $ret; 
    }
    
    static function createAdvertiserService ($context) {
	return new AdvertiserService ($context);
    }
}

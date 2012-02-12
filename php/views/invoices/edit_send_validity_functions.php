<?php
//here we check that post has specific indcators for certain posts
function is_valid_post () {                    
    //first checking sent (this applies to all)
    if (array_key_exists("sent",$_POST)) {       
	//this is for selection
	if (array_key_exists("cam_id",$_POST)) {                                                                                                                        
	    //we can safely return true because all necessary stuff exists                                                                                    
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
	
	else if (array_key_exists("due_date", $_POST) && array_key_exists("ref_num", $_POST) && array_key_exists("cam_id", $_POST)) {                                                          
	    //if dun is there then there must be prvious invoice too                                                                                          
	    if (array_key_exists("dun",$_POST) && !array_key_exists("prev_invoice",$_POST)) {                                                                                     
		return FALSE;                                                                                                                                 
	    }                                                                                                                                                 
	    
	    //otherwise we can return TRUE                                                                                                                    
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
	
	//and when we're editing                                                                                                                              
	else if (array_key_exists("due_at", $_POST) && array_key_exists("ref_num", $_POST) && array_key_exists("id", $_POST)) {                                                              
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
    }                                                                                                                                                         
    
    //there is some unknown combination                                                                                                                       
    return FALSE;                                                                                                                                             
} 

//checking if everything is ok with get 
function is_valid_get() {               
    //id must exist                     
    if (array_key_exists("id", $_GET)) {           
	//also checking stuff when dun is available 
	if (array_key_exists("dun",$_GET) && (!array_key_exists("id", $_GET) || !array_key_exists("cam_id", $_GET))) {  
	    return FALSE;                                                               
	}                                                                               
	return TRUE;                                                                    
    }                                                                                   
    return FALSE;                                                                       
} 

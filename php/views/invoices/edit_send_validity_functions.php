<?php
//here we check that post has specific indcators for certain posts
function is_valid_post () {                    
    //first checking sent (this applies to all)
    if (isset($_POST["sent"])) {       
	//this is for selection
	if (isset($_POST["cam_id"])) {                                                                                                                        
	    //we can safely return true because all necessary stuff exists                                                                                    
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
	
	else if (isset($_POST["due_date"]) && isset($_POST["ref_num"]) && isset($_POST["cam_id"])) {                                                          
	    //if dun is there then there must be prvious invoice too                                                                                          
	    if (isset($_POST["dun"]) && !isset($_POST["prev_invoice"])) {                                                                                     
		return FALSE;                                                                                                                                 
	    }                                                                                                                                                 
	    
	    //otherwise we can return TRUE                                                                                                                    
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
	
	//and when we're editing                                                                                                                              
	else if (isset($_POST["due_date"]) && isset($_POST["ref_num"]) && isset($_POST["id"])) {                                                              
	    return TRUE;                                                                                                                                      
	}                                                                                                                                                     
    }                                                                                                                                                         
    
    //there is some unknown combination                                                                                                                       
    return FALSE;                                                                                                                                             
} 

//checking if everything is ok with get 
function is_valid_get() {               
    //id must exist                     
    if (isset($_GET["id"])) {           
	//also checking stuff when dun is available 
	if (isset($_GET["dun"]) && (!isset($_GET["id"]) || !isset($_GET["cam_id"]))) {  
	    return FALSE;                                                               
	}                                                                               
	return TRUE;                                                                    
    }                                                                                   
    return FALSE;                                                                       
} 

<?php
class EditService {	
    private $context;		
    
    function __construct($context) {
	$this->context = $context;
    }
	
    //database functions
    //*****  **  *****//
    
    function new_invoice_insert ($insert) {                                               
	$conn_id = $this->context->db;                                                      
	$conn_id->beginTransaction ();                                                
	//hopefully this will change                                                  
	$conn_id->query($insert);                                                 
    }                                                                                     
                                                                                          
    function update_invoice ($update) {                                                                                                             
	$conn_id = $this->context->db;                                                      
	$conn_id->beginTransaction ();                                                
	$conn_id->query($update);                                                     
    }                                                                                     
                                                                                          
    function select_invoices ($select) {                                                  
	$conn_id = $this->context->db;                                                      
	$conn_id->beginTransaction();                                                 
	return $conn_id->query ($select);                                             
    } 
    
    //*****  **  *****//
    
    //view functions//
    //***** ** *****//
    
    function new_invoice_select ($rows, $dun_mess) {                                      
	//initializing the form when it's not yet know which campaign we're editing   
	echo "<form method=\"post\" action=\"edit\">";                            
	echo "<table border = \"1\">";                                                
	echo "<thead>";                                                               
	echo "<tr>";                                                                  
	echo "<td> Name </td>";                                                       
	echo "<td> Start date </td>";                                                 
	echo "<td> End date </td>";                                                   
	echo "</tr>";                                                                 
	echo "</thead>";                                                            
	
	//printing some information about the campaings (not really known what we need to print here)
	echo "<tbody>";                                                                          
	foreach ($rows as $iter) {                                                                   
	    echo "<tr>";                                                                   
	    echo "<td>".$iter["name"]."</td>";                                             
	    echo "<td>".$iter["starts_at"]."</td>";                                        
	    echo "<td>".$iter["ends_at"]."</td>";                                          
	    echo "<td><input type=\"submit\" value=\"Create\"></td>";                      
	    echo "</tr>";                                                                  
	}                                                                                            
	echo "</tbody>";                                                                             
	echo "</table>";                                                                             
	//sending which campaign to edit                                                             
	if (isset ($iter)) {                                                                     
	    echo "<input type=\"hidden\" value=\"".$iter["id"]."\" id=\"cam_id\" name=\"cam_id\">";
	}                                                                                                  
	
	//if we have dun invoice we send extra                                                                   
	if ($dun_mess) {                                                                                     
	    echo "<input type=\"hidden\" value=\"dun\" name=\"dun\">";                               
	}                                                                                                    
	
	//fake send for post function                                                                        
	echo "<input type=\"hidden\" value=\"high\" id=\"sent\" name=\"sent\">";                         
	echo "</form>";                                                                                          
    }
    
    //prints the view for modifying the invoice                   
    //accepts $obj parameter which is the invoice we're modifying 
    //accepts $dun_mess which is boolean indicator for late fee or dun invoice
    function new_invoice_modify ($obj, $dun_mess) {                           
	echo "<form method=\"post\" action=\"edit\">";                    
	echo "Due date (yyyy-mm-dd): <input type=\"text\" value=\"".$obj->due_at."\" id=\"due_date\" name=\"due_date\"><br>"; 
	echo "Reference number (at least 5 letters): <input type=\"text\" value=\"".$obj->ref_number."\" id=\"ref_num\" name=\"ref_num\"><br>";
	if ($dun_mess) {                                                                                                                       
	    echo "Late fee: <input type=\"text\" value=\"".$obj->late_fee."\" name=\"dun\"><br>";                                      
	}                                                                                                                                      
	
	else {                                                                                                                                 
	    echo "Late fee: ".$obj->late_fee."<br>";                                                                                   
	}                                                                                                                                      
	echo "Sent: F<br>";                                                                                                                    
	//lets send fake value because there is stuff to consider ^^                                                                           
	echo "<input type=\"hidden\" value=\"low\" id=\"sent\" name=\"sent\">";                                                            
	echo "Campaign number: ".$obj->cam_id."<br>";                                                                                          
	//if we're doing dun invoice we add previnvoice information                                                                            
	if ($dun_mess) {                                                                                                                 
	    echo "Previous invoice: ".$obj->prev_invoice."<br>";                                                               
	    echo "<input type=\"hidden\" value=\"".$obj->prev_invoice."\" name=\"prev_invoice\">";                             
	}                                                                                                                                
	echo "<input type=\"hidden\" value=\"".$obj->cam_id."\" id=\"cam_id\" name=\"cam_id\">";                                               
	echo "<input type=\"submit\" value=\"Save\">";                                                                                         
	echo "</form>";                                                                                                                        
    }   
    
    function edit_invoice ($row) {                                
	//editing invoice                                     
	echo "<form method = \"post\" action=\"edit\">";  
	echo "<table>";                                       
	
	foreach ($row as $iter) {                             
	    echo "<tr><td> Id: </td>";                
	    echo "<td>".$iter["id"]."</td></tr>";     
	    echo "<tr><td> Due date: </td>";          
	    echo "<td><input type=\"text\" value = \"".$iter["due_at"]."\" id=\"due_at\" name=\"due_at\"></td></tr>";  
	    echo "<tr><td> Reference number: </td>";                                                                   
	    echo "<td><input type=\"text\" value = \"".$iter["reference_number"]."\" id=\"ref_num\" name=\"ref_num\"></td></tr>";
	    echo "<tr><td> Late fee: </td>";                                                                                     
	    echo "<td>".$iter["late_fee"]."</td></tr>";                                                                          
	    echo "<tr><td> Sent: </td>";                                                                                         
	    echo "<td>".$iter["sent"]."</td></tr>";                                                                              
	    echo "<tr><td> Campaign number: </td>";                                                                              
	    echo "<td>".$iter["campaign_id"]."</td></tr>";                                                                       
	    echo "<tr><td> Previous invoice: </td>";                                                                             
	    echo "<td>".$iter["previous_invoice_id"]."</td></tr>";                                                               
	    //making fake value send edit to post handle                                                                         
	    echo  "<input type=\"hidden\" value=\"edit\" id=\"sent\" name=\"sent\">";                                    
	    //also sending necesary id so we can easily edit in the database                                                     
	    echo "<input type=\"hidden\" value=\"".$iter["id"]."\" id=\"id\" name=\"id\">";                              
	}                                                                                                                                
	echo "</table>";                                                                                                                 
	echo "<input type=\"submit\" value=\"Save\">";                                                                                   
	echo "</form>";                                                                                                                  
    }
    
    //*****  **  ******//
    
    //validity functions
    //***** ** ******//
    
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
    //***** ** *****//
}

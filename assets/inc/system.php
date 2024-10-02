<?php

################################################  ADMIN BEREICH #####################################################

function output( $in_content )
{
	## laden der index.html die die Grundstruktur unserer Seite darstellt
	$out = file_get_contents( "assets/html/index.html" ); 
	
	## den CONTENT Platzhalter mit dem aktuellen Inhalt ersetzen
	$out = str_replace( "###CONTENT###" , $in_content , $out );

	$sign = "<a href='index.php?act=signup_page'>Sign Up</a>";
	$user_txt = "";
	
	## default den LOGIN Link zeigen
	$text = "<a href='index.php?act=login_page'>Login</a>";
	
	## wenn jemand angemeldet ist .. dann >>
	if( isset( $_SESSION['user_id'] ) && isset($_SESSION['role_id']) && $_SESSION['role_id'] > 2)
	{	
		## den User aus Datenbank laden
		$user = new User( $_SESSION['user_id'] );
		
		## den login-text mit einem logout text überschreiben
		$user_txt  = "| <span> Sie sind angemeldet als: ". $user->get_username() ." </span> ";		
		$text = " <a href='index.php?act=logout'>Logout</a>";
		$sign = "<a href='index.php?act=list_user'>Benutzerliste</a>";
	}
	
	## den LOGOUT Platzhalter mit dem vorher erzeugten Text ersetzten
	$out = str_replace( "###LOGOUT###" , $text , $out );
	$out = str_replace("###USER###", $user_txt, $out);
	$out = str_replace("###REGISTER###", $sign, $out);
		
    ## den HTML code ausgeben und das PHP beenden
	die( $out ); ## ENDE DES PHP !!!! 
}



function act_admin($in_msg = "Willkommen im Admin Panel")
{
    output($in_msg);
}

################################################  ADMIN BEREICH #####################################################

function g( $assoc_index )
{
	if( isset( $_REQUEST[$assoc_index] ) == false )
	{
		return null;
	}
	
	return $_REQUEST[$assoc_index];
}

function gen_html_options( $in_data_array , $in_selected_id , $in_add_empty  )
{	
	$out_opt = "";
	
	if( $in_add_empty == true )
	  {
		$out_opt .= '<option value=0  >  -- KEINE --  </option>';
	  }	
	
	foreach( $in_data_array as $key => $val )	
	{		
		$sel = "";
		
		if( $key  == $in_selected_id )
			$sel = "selected";
		
		$out_opt    .= '<option value="'. $key .'"  '. $sel .' > '. $val .' </option>';
	}	
	
	return $out_opt ;
}

################################################  USER BEREICH  #####################################################  

function output_fe( $in_content )
{
	## laden der index.html die die Grundstruktur unserer Seite darstellt
	$html   = file_get_contents("assets/html/frontend/index.html"); 
	$logout = "";
	$out    = $in_content;	

	if(isset($_SESSION['user_id']))
	{
		$logout = file_get_contents("assets/html/frontend/logout.html");
		$user = new User($_SESSION['user_id']);
		$logout = str_replace("###USERNAME###", $user->get_username(), $logout);
	}

	$logout = str_replace("###LOGOUT###"  , $logout , $html);
    $out 	= str_replace("###CONTENT###" , $out 	, $logout);
		
    ## den HTML code ausgeben und das PHP beenden
	die( $out ); ## ENDE DES PHP !!!! 
}


function home()
{
    act_start();
	//act_admin();
}

function act_start()
{
    $html = file_get_contents("assets/html/frontend/home.html");
	if(isset($_SESSION['user_id']))
	{
		$html = file_get_contents("assets/html/frontend/goto_chat.html");
	}
    
    output_fe($html);
}
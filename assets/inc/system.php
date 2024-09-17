<?php

function output( $in_content )
{
	## laden der index.html die die Grundstruktur unserer Seite darstellt
	$out = file_get_contents( "assets/html/index.html" ); 
	
	## den CONTENT Platzhalter mit dem aktuellen Inhalt ersetzen
	$out = str_replace( "###CONTENT###" , $in_content , $out );
	
	## default den LOGIN Link zeigen
	$text = "<a href='index.php?act=login'>Login</a>";
	
	## wenn jemand angemeldet ist .. dann >>
	if( isset( $_SESSION['user_id'] ) == true  && isset($_SESSION['user_status']) != 'umschüler')
	{	
		## den User aus Datenbank laden
		//$user = new User( $_SESSION['user_id'] );
		
		## den login-text mit einem logout text überschreiben
		//$text  = " Sie sind angemeldet als: ". $user->username ." <br> ";		
		$text .= " <a href='index.php?act=logout'>Logout</a>";
	}
	
	## den LOGOUT Platzhalter mit dem vorher erzeugten Text ersetzten
	$out = str_replace( "###LOGOUT###" , $text , $out );
		
    ## den HTML code ausgeben und das PHP beenden
	die( $out ); ## ENDE DES PHP !!!! 
}

################################################  ADMIN BEREICH #####################################################

function act_admin()
{
    output('Hallo');
}

################################################  ADMIN BEREICH #####################################################

function home()
{
    //act_start();
}

function g( $assoc_index )
{
	if( isset( $_REQUEST[$assoc_index] ) == false )
	{
		return null;
	}
	
	return $_REQUEST[$assoc_index];
}
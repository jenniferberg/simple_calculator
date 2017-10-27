<?php

class Session{
	function __construct(){
		ob_start();
		
		session_start();
	}
	
	public function end_session(){
		unset($_SESSION['result']);
		unset($_SESSION['i']);
		unset($_SESSION['all_values']);
	}
	
}

$session = new Session();



?>
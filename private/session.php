<?php

class Session{
	function __construct(){
		ob_start();
		session_start();
	}

	public function set_default_session(){
		$_SESSION['submit'] = 'false';
		$_SESSION['i'] = 0;
		$_SESSION['all_values'] = [];
	}

	public function set_session($submit, $counter, $all_values){
		$_SESSION['submit'] = $submit;
		$_SESSION['i'] = $counter;
		$_SESSION['all_values'] = $all_values;
	}

	public function end_session(){
		unset($_SESSION['submit']);
		unset($_SESSION['i']);
		unset($_SESSION['all_values']);
	}

}

$session = new Session();



?>

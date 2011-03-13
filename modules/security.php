<?php

class security 
{
	var $attempt = array();
	var $sessionName = "SECURITY";
	
	function __construct($threshold=true) {
		//$this->escape();
		if ($threshold)
			$this->session();
	}
	function escape() {
		if (!function_exists("get_magic_quotes_gpc") || !get_magic_quotes_gpc()) {
			foreach($_GET as $k => $v) $_GET[$k] = addslashes($v);
            foreach($_POST as $k => $v) $_POST[$k] = addslashes($v);
            foreach($_COOKIE as $k => $v) $_COOKIE[$k] = addslashes($v);
		}
	}
	function session($threshold=false) {
		
		// Start session
		if (!isset($_SESSION)) session_start();
		
		// Check if first time
		if (isset($_SESSION[$this->sessionName]))
		{
			// IP
			if ($_SERVER['REMOTE_ADDR'] !== $_SESSION[$this->sessionName.'_REMOTEADDR']) 
				session_destroy(); 
			
			// Useragent
			if ($_SERVER['HTTP_USER_AGENT'] !== $_SESSION[$this->sessionName.'_USERAGENT'])
				session_destroy();
		}
		
		session_regenerate_id($threshold); // generate a new session identifier
		
		// set value
		$_SESSION[$this->sessionName.'_USERAGENT'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION[$this->sessionName.'_REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];






	}
}


?>
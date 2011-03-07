<?php

defined('ERROR_USERNAME_INVALID') or define('ERROR_USERNAME_INVALID', 1);

class SU_User {
	public static $error_code = 10;

	public static $username;
	public static $password;
	
	public function __construct() {
		
	}
	
	// $store = save extra info to session if successfully logged in.
	public function login($username, $password, $store) {
		$validate_func = c::get('user.login.validate');
		if ($user_id = $validate_func($username, $password)) {
			s::set('user.id', $user_id);
			return true;
		} else {
			s::set('error.login', ERROR_USERNAME_INVALID);
			return false;
		}
	}
	
	public function logout($all=true) {
		if ($all) {
			s::destroy();
		} else {
			s::remove('user_id');
		}
	}
	
	public function get_error_code() {
		return self::$error_code;
	}
}

?>
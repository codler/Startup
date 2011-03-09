<?php

defined('ERROR_USERNAME_INVALID') or define('ERROR_USERNAME_INVALID', 1);

defined('USER_IDENTITY') or define('USER_IDENTITY', '_su_identity');
defined('USER_PASSWORD') or define('USER_PASSWORD', '_su_password');

class SU_User {
	public static $error_code = 10;

	public $identity;
	public $password;
	
	public function __construct() {
		$this->identity = c::get('user.form.identity', USER_IDENTITY);
		$this->password = c::get('user.form.password', USER_PASSWORD);
	}
	
	// $store = save extra info to session if successfully logged in.
	public function login($username=null, $password=null, $store=null) {
		if (is_null($username)) {
			$username = r::get(SU::User()->identity);
		}
		if (is_null($password)) {
			$password = r::get(SU::User()->password);
		}
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
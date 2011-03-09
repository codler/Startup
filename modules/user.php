<?php

defined('ERROR_USERNAME_INVALID') or define('ERROR_USERNAME_INVALID', 1);

defined('USER_IDENTITY') or define('USER_IDENTITY', '_su_identity');
defined('USER_PASSWORD') or define('USER_PASSWORD', '_su_password');

defined('SU_USER_REGISTER_IDENTITY') or define('SU_USER_REGISTER_IDENTITY', 1);
defined('SU_USER_REGISTER_PASSWORD') or define('SU_USER_REGISTER_PASSWORD', 2);
defined('SU_USER_LOGIN_IDENTITY') or define('SU_USER_LOGIN_IDENTITY', 3);
defined('SU_USER_LOGIN_PASSWORD') or define('SU_USER_LOGIN_PASSWORD', 4);


if (!function_exists('get_facebook_cookie')) {
	function get_facebook_cookie($app_id, $app_secret) {
	  $args = array();
	  parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
	  ksort($args);
	  $payload = '';
	  foreach ($args as $key => $value) {
		if ($key != 'sig') {
		  $payload .= $key . '=' . $value;
		}
	  }
	  if (md5($payload . $app_secret) != $args['sig']) {
		return null;
	  }
	  return $args;
	}
}

class SU_User {
	public static $error_code = 10;

	public $identity;
	public $password;
	
	public function __construct() {
		$this->identity = c::get('user.form.identity', USER_IDENTITY);
		$this->password = c::get('user.form.password', USER_PASSWORD);
	}
	
	public function register($identity=null, $password=null) {
		if (is_null($identity)) {
			$identity = r::get($this->identity);
		}
		if (is_null($password)) {
			$password = r::get($this->password);
		}
		$validate_func = c::get('user.register.validate');
		if (is_callable($validate_func) && $validate_func($identity, $password)) {
			return true;
		} elseif ($validate_fb_func = c::get('user.register.validate.fb') && 
			$cookie = get_facebook_cookie(c::get('fb.id'), c::get('fb.secret'))) {
			return is_callable($validate_fb_func) && $validate_fb_func($cookie['uid']);
		} else {
			return false;
		}
	}
	
	// $store = save extra info to session if successfully logged in.
	public function login($identity=null, $password=null, $store=null) {
		if ($validate_fb_func = c::get('user.login.validate.fb') && 
			$cookie = get_facebook_cookie(c::get('fb.id'), c::get('fb.secret'))) {
			if (is_callable($validate_fb_func) && $user_id = $validate_fb_func($cookie['uid'])) {
				s::set('user.id', $user_id);
				return true;
			}
		}
		
		if (is_null($identity)) {
			$identity = r::get($this->identity);
		}
		if (is_null($password)) {
			$password = r::get($this->password);
		}
		$validate_func = c::get('user.login.validate');
		if (is_callable($validate_func) && $user_id = $validate_func($identity, $password)) {
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
<?php

// Application root directory
defined('BASE_DIR') or define('BASE_DIR', dirname(__file__) . '/');
// Debug mode (true or false)
defined('DEBUG') or define('DEBUG', true);

// Load Startup
require_once(BASE_DIR . 'bootstrap.php');

/*
// == Database settings ==
c::set('db.host', 'localhost');
c::set('db.user', '');
c::set('db.password', '');
c::set('db.name', ''); // DB name
c::set('db.prefix', ''); // Table prefix
c::set('db.debugging', DEBUG); // DB query debug
*/

/*
// == Facebook settings ==
c::set('fb.id', '');
c::set('fb.secret', '');
*/

// == Route settings ==
c::set('route.base.host', 'ply.se'); // Hostname
//c::set('route.base.path', '/index.php/'); // URL Application path

// == Authentication settings ==
c::set('user.register.validate', function($u,$p) {
	if (!v::email($u)) {
		SU::Form()->set_message(SU_USER_REGISTER_IDENTITY, 'Ingen giltig epostadress', 'error');
	} elseif(db::count('user', array('email' => $u))) {
		SU::Form()->set_message(SU_USER_REGISTER_IDENTITY, 'Epostadressen existerar redan', 'error');
	} elseif(!v::password($p)) {
		SU::Form()->set_message(SU_USER_REGISTER_PASSWORD, 'Lösenordet är förkort', 'error');
	} elseif(!v::passwords($p, r::get('password3'))) {
		SU::Form()->set_message(SU_USER_REGISTER_PASSWORD, 'Lösenordet är stämmer inte överäns', 'error');
	} else {
		db::insert('user', array('email' => $u, 'password' => $p));
		SU::User()->login();
		return true;
	}
	return false;
	
}); // Validate user register - return true or false
c::set('user.login.validate', function($u,$p) {
	$r = db::field('user', 'id', array('email' => $u, 'password' => $p));
	if (!$r) {
		// Set error login message
		SU::Form()->set_message(SU_USER_LOGIN_IDENTITY, 'Login failed', 'error');
	}
	return $r;
}); // Validate user logins - return id or false
/*
c::set('user.register.validate.fb', function($uid) {
	return; // check if uid does not already exist, then login.
}); // Validate fb register - require facebook settings
c::set('user.login.validate.fb', function($uid) {
	return; // find user_id from uid, alt. register if not exist
}); // Validate fb logins - require facebook settings

c::set('user.form.identity', USER_IDENTITY); // Name of the inputfield
c::set('user.form.password', USER_PASSWORD); // Name of the inputfield
*/

?>
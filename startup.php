<?php

// Application root directory
defined('BASE_DIR') or define('BASE_DIR', dirname(__file__) . '/');
// Debug mode (true or false)
defined('DEBUG') or define('DEBUG', true);

// Load Startup
require_once(BASE_DIR . 'bootstrap.php');

/*
// Database settings
c::set('db.host', 'localhost');
c::set('db.user', '');
c::set('db.password', '');
c::set('db.name', ''); // DB name
c::set('db.prefix', ''); // Table prefix
c::set('db.debugging', DEBUG); // DB query debug
*/

// Route settings
c::set('route.base.host', 'ply.se'); // Hostname
//c::set('route.base.path', '/index.php/'); // URL Application path

// Authentication settings
c::set('user.login.validate', function($u,$p) {
	return db::field('user', 'id', array('email' => $u, 'password' => $p));
}); // Validate user logins - return id or false




c::set('user.login.onSuccess', false);
c::set('user.login.onError', false);

?>
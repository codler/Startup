<?php
require_once(dirname(__file__) . '/bootstrap.php');

defined('BASE_DIR') or define('BASE_DIR', dirname(__file__) . '/');
c::set('user.login.onSuccess', false);
c::set('user.login.onError', false);
c::set('user.login.validate', function($u,$p) {
	return db::field('user', 'id', array('username' => $u, 'password' => $p));
});

c::set('route.base.host', 'ply.se');
//c::set('route.base.path', '/index.php/');
?>
<?php
require_once(dirname(__file__) . '/bootstrap.php');

c::set('user.login.onSuccess', false);
c::set('user.login.onError', false);
c::set('user.login.validate', function($u,$p) {
	return db::field('user', 'id', array('username' => $u, 'password' => $p));
});

c::set('route.base.host', 'ply.se');
//c::set('route.base.path', '/index.php/');
?>
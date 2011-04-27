<?php 
require_once(dirname(__file__) . '/startup.php');

// Add an external resourse such as js or css file.
#SU::Ui()->add_external('hej.js');

SU::Route(array(SU_URL_HOST, ''), function($meta, $symbols) {
	if ($id = s::get('user.id')) {
		$data .= 'Logged in as '. $id;
		$data .= '<a href="http://' . $meta['host'] . c::get('route.base.path') . 'logout">Logout</a>';
	} else {
		$data .= '<a href="http://' . $meta['host'] . c::get('route.base.path') . 'login">Login</a>';
		$data .= '<a href="http://' . $meta['host'] . c::get('route.base.path') . 'register">Register</a>';
	}
	
	// test form
	$form = SU::Form();
	$form->open('test');
	$form->label('Meddelande', 'message');
	$form->textarea('message', array('id'=>'message', 'placeholder'=>'Meddelande', 'required'));
	$form->label('Radio1', 'radio1');
	$form->radio('radio', array('id'=>'radio1'));
	$form->label('Radio2', 'radio2');
	$form->radio('radio', array('id'=>'radio2'));
	$form->label('Checkbox1', 'checkbox1');
	$form->checkbox('checkbox', array('id'=>'checkbox1'));
	$form->label('Checkbox2', 'checkbox2');
	$form->checkbox('checkbox', array('id'=>'checkbox2'));
	$form->submit('submit', 'Knapp');
	$form->close();
	
	$data .= $form->render();
	$page = array('page' => 'main.php', 'data'=> $data);
	SU::view('tpl.php', $page);
});


// Example: Register
SU::Route(array(SU_URL_HOST, 'register'), function($meta, $symbols) {
	if (SU::Form()->verify('register')) {
		if (SU::User()->register()) {
			SU::Form()->set_message(SU_USER_REGISTER_IDENTITY, 'Register success', 'success');
		}
	}

	// register form
	$form = SU::Form();
	$form->open('register');
	$form->message(SU_USER_REGISTER_IDENTITY);
	$form->label('Epost', 'email2');
	$form->email(SU::User()->identity, array('id'=>'email2', 'placeholder'=>'E-postadress', 'required'));
	$form->message(SU_USER_REGISTER_PASSWORD);
	$form->label('Lösenord', 'password2');
	$form->password(SU::User()->password, array('id'=>'password2', 'placeholder'=>'Lösenord'));
	$form->label('igen', 'password3');
	$form->password('password3', array('id'=>'password3', 'placeholder'=>'Lösenord igen'));
	$form->submit('submit', 'Registrera');
	$form->close();
	
	$data = $form->render();
	$page = array('page' => 'main.php', 'data'=> $data);
	SU::view('tpl.php', $page);
});

// Example: Login
SU::Route('login', function($meta, $symbols) {
	// Verify form
	if (SU::Form()->verify('login')) {
		// Try login
		if (SU::User()->login()) {
			// Set success login message
			SU::Form()->set_message(SU_USER_LOGIN_IDENTITY, 'Login success', 'success');
			
			$base_host = c::get('route.base.host');
			$redirect = r::get('redirect');
			if (v::url($redirect) &&
				// Compare end string
				substr_compare(parse_url($redirect, PHP_URL_HOST), $base_host, -strlen($base_host), strlen($base_host)) === 0) {
				go($redirect);
			} else {
				// Go to root
				go(c::get('route.base.path'));
			}
		}
	}

	/* // login form
	$form = SU::Form();
	$form->open('login');
	if (v::url(r::get('next'))) {
		$form->hidden('redirect', r::get('next'));
	}
	$form->message(SU_USER_LOGIN_IDENTITY);
	$form->label('Epost', 'email');
	$form->email(SU::User()->identity, array('id'=>'email', 'placeholder'=>'E-postadress', 'required'));
	$form->label('Lösenord', 'password');
	$form->password(SU::User()->password, array('id'=>'password', 'placeholder'=>'Lösenord'));
	$form->submit('submit', 'Logga in');
	$form->close();
	
	$data = $form->render(); */
	$data = SU::view('login.php', null, true);
	$page = array('page' => 'main.php', 'data'=> $data);
	SU::view('tpl.php', $page);
});

// Example: Login continue
SU::Route('login/facebook', function($meta, $symbols) {
	SU::User()->sso_login('facebook', 'login/facebook/auth');
});
SU::Route('login/facebook/auth', function($meta, $symbols) {
	if (SU::User()->sso_login_auth('facebook', $meta['path'])) {
		// Set success login message
		SU::Form()->set_message(SU_USER_LOGIN_IDENTITY, 'Login success', 'success');
	}
	// Go to root
	go(c::get('route.base.path'));
});
// Example: Login continue
SU::Route('login/google', function($meta, $symbols) {
	SU::User()->sso_login('google', 'login/google/auth');
});
SU::Route('login/google/auth', function($meta, $symbols) {
	if (SU::User()->sso_login_auth('google', $meta['path'])) {
		// Set success login message
		SU::Form()->set_message(SU_USER_LOGIN_IDENTITY, 'Login success', 'success');
	}
	// Go to root
	go(c::get('route.base.path'));
});

// Example: Logout
SU::Route(array(SU_URL_HOST, 'logout'), function($meta, $symbols) {
	s::destroy();
	// Go to root
	go(c::get('route.base.path'));
});

// Main domain
SU::Route(array(SU_URL_HOST, 'maindomain'), function($meta, $symbols) {
	echo "Maindomain - " . $meta['host'] . '/' . $meta['path'];
});

// All subdomain
SU::Route(array(SU_URL_SUB_HOST, 'subdomain'), function($meta, $symbols) {
	echo "Subdomain - " . $meta['host'] . '/' . $meta['path'];
}); 

// "Wildcard route"
SU::Route(array('unique', 'id/:id'), function($meta, $symbols) {
	echo print_r($meta, true) . print_r($symbols, true);
/*
Array
(
    [host] => ply.se
    [path] => id/30
)
Array
(
    [0] => id/30
    [id] => 30
    [1] => 30
)
*/
});

// Both are same page
SU::Route(array('*', 'all'), function() {}); 
SU::Route('all', function() {}); 

// Example: Protected page
function access() {
	if (!s::get('user.id')) {
		$page = array('html' => 'Login required');
		SU::view('tpl.php', $page);
		return false;
	}
}
SU::Route('protected-page', access, function($meta, $symbols) {
	echo "Viewing a protected-page";
});

// Example: Protected page 2 with redirect back on success login
function access2($meta, $symbols) {
	if (!s::get('user.id')) {
		go('http://' . c::get('route.base.host') . '/login?next=' . urlencode(url::current()));
		return false;
	}
}
SU::Route('protected-page', access2, function($meta, $symbols) {
	echo "Viewing a protected-page";
});

// Custom 404 page
SU::Route('404!', function($meta, $symbols) {
	echo "Page not found";
});


?>
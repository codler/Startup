<?php 
require_once(dirname(__file__) . '/startup.php');

#SU::Ui()->add_external('hej.js', 'js');

SU::Route(array(SU_URL_HOST, ''), function($host, $path) {
	
	#if (isset($_POST)) {
		if (SU::Form()->verify('login')) {
			if (SU::User()->login()) {
				SU::Form()->set_message(SU::User()->identity, 'Login success', 'success');
			} else {
				SU::Form()->set_message(SU::User()->identity, 'Login failed', 'error');
			}
		}
		
		if (SU::Form()->verify('register')) {
			if (SU::User()->register()) {
				SU::Form()->set_message(SU::User()->identity, 'Register success', 'success');
			} else {
				#SU::Form()->set_message(SU::User()->identity, 'Register failed', 'error');
			}
		}
	#}
	
	$data = '';
	
	if ($id = s::get('user.id')) {
		$data .= 'Logged in as '. $id;
		$data .= '<a href="http://' . $host . '/logout">Logout</a>';
	}
	
	// login form
	$form = SU::Form();
	$form->open('login');
	$form->message(SU::User()->identity);
	$form->label('Epost', 'email');
	$form->email(SU::User()->identity, array('id'=>'email', 'placeholder'=>'E-postadress', 'required'));
	$form->label('Lösenord', 'password');
	$form->password(SU::User()->password, array('id'=>'password', 'placeholder'=>'Lösenord'));
	$form->submit('submit', 'Logga in');
	$form->close();
		
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
	
	// test form
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

SU::Route(array(SU_URL_HOST, 'logout'), function($host, $path) {
	s::destroy();
	go('http://' . $host);
});

SU::Route(array(SU_URL_HOST, 'login'), function($host, $path) {
	echo "main" . $host . $path;
});

SU::Route(array(SU_URL_SUB_HOST, 'login'), function($host, $path) {
	echo "sub" . $host . $path;
}); 


SU::Route(array('*', 'all'), function($host, $path) {
	echo "all" . $host . $path;
}); 

SU::Route(array('spe', 'qw:id'), function($host, $path) {
	echo "qw" . $host . $path; 
}); 



	/* if ($login) {
		SU::User()->login($u, $p);
	}
 */


/* 
class User_auth {
	public $id;
	public $additional_info = array();
	public function authenticate($username, $password) {
		return true;
	}
}
//new SU();
//SU::Core()->User->login('','');

echo SU::User()->get_error_code();

$identity = new User_auth();
if ($identity->authenticate('','')) {
	//SU::User()->login($identity);
} */
?>
<?php 
require_once(dirname(__file__) . '/startup.php');

#SU::Ui()->add_external('hej.js', 'js');

SU::Route(array(SU_URL_HOST, ''), function($host, $path) {
	#if (isset($_POST)) {
		if (SU::Form()->verify('login')) {
			if (SU::User()->login()) {
				echo "Login success";
			} else {
				SU::Form()->set_message(SU::User()->identity, 'Login failed', 'error');
			}
		}
	#}
	
	$data = '';
	
	if ($id = s::get('user.id', false)) {
		$data .= 'Logged in as '. $id;
	}
	$form = SU::Form();
	$form->open('login');
	$form->message(SU::User()->identity);
	$form->label('Epost', 'email');
	$form->email(SU::User()->identity, array('id'=>'email', 'placeholder'=>'E-postadress', 'required'));
	$form->label('Lösenord', 'password');
	$form->password(SU::User()->password, array('id'=>'password', 'placeholder'=>'Lösenord'));
	$form->submit('submit', 'Logga in');
	$form->close();
	
	$form->open('login');
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
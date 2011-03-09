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
	
	$data .= SU::Form()->open('login');
	$data .= SU::Form()->message(SU::User()->identity);
	$data .= SU::Form()->label('Epost', 'email');
	$data .= SU::Form()->email(SU::User()->identity, array('id'=>'email', 'placeholder'=>'E-postadress', 'required'));
	$data .= SU::Form()->label('Lösenord', 'password');
	$data .= SU::Form()->password(SU::User()->password, array('id'=>'password', 'placeholder'=>'Lösenord'));
	$data .= SU::Form()->submit('submit', 'Logga in');
	$data .= SU::Form()->close();
	
	$data .= SU::Form()->open('login');
	$data .= SU::Form()->label('Meddelande', 'message');
	$data .= SU::Form()->textarea('message', array('id'=>'message', 'placeholder'=>'Meddelande', 'required'));
	$data .= SU::Form()->label('Radio1', 'radio1');
	$data .= SU::Form()->radio('radio', array('id'=>'radio1'));
	$data .= SU::Form()->label('Radio2', 'radio2');
	$data .= SU::Form()->radio('radio', array('id'=>'radio2'));
	$data .= SU::Form()->label('Checkbox1', 'checkbox1');
	$data .= SU::Form()->checkbox('checkbox', array('id'=>'checkbox1'));
	$data .= SU::Form()->label('Checkbox2', 'checkbox2');
	$data .= SU::Form()->checkbox('checkbox', array('id'=>'checkbox2'));
	$data .= SU::Form()->submit('submit', 'Knapp');
	$data .= SU::Form()->close();
	
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
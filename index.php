<?php 
require_once(dirname(__file__) . '/startup.php');

SU::Route(array(SU_URL_HOST, ''), function($host, $path) {
	echo "startsida" . $host . $path;
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
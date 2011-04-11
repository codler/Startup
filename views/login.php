<?php
// login form
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

echo $form->render();
?>
<a href="/login/facebook">Logga in med facebook</a>
<a href="/login/google">Logga in med google</a>
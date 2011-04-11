<?php
/**
 * @author Han Lin Yap < http://zencodez.net/ >
 * @copyright 2011 zencodez.net
 * @license http://creativecommons.org/licenses/by-sa/3.0/
 * @package Single-Sign-On
 * @version 1.0 - 2011-04-10
 */
require_once(dirname(__file__) . '/libs/lightopenid.php');

class Single_sign_on {
	
	public $identity;
	public $fetch_info = array('contact/email', 'namePerson/first', 'namePerson/last');
	public $providers = array('facebook' => 'https://www.facebook.com/dialog/oauth'/*'https://graph.facebook.com/oauth/authorize'*/, 'google' => 'https://www.google.com/accounts/o8/id');
	public $provider;
	public $domain;
	public $fb_app_id;
	public $fb_app_secret;
	public $redirect_url;
	
	function __construct($provider, $domain = null, $redirect_url = null, $fb_app_id = null, $fb_app_secret) {
		$this->identity = $this->providers[$provider];
		$this->provider = $provider;
		$this->domain = $domain;
		$this->fb_app_id = $fb_app_id;
		$this->fb_app_secret = $fb_app_secret;
		$this->redirect_url = $redirect_url;
	}
	
	function login() {
		if ($this->provider == 'facebook') {
			$auth_url = $this->identity . '?client_id=' . $this->fb_app_id . '&' . 'redirect_uri=' . urlencode($this->redirect_url) . '&scope=email';
		} else {
			$openid = new LightOpenID;
			$openid->identity = $this->identity;
			$openid->realm = $this->domain;
			$openid->required = $this->fetch_info;
			if ($this->redirect_url) $openid->returnUrl = $this->redirect_url;
			$auth_url = $openid->authUrl();
		}
		@header('Location: ' . $auth_url);
		die();
	}
	
	function return_page() {
		$data = $_POST + $_GET;
		$openid = new LightOpenID;
		if ($data['openid_mode'] && $openid->mode == 'id_res' && $openid->validate()) {
			return array('identity' => $openid->identity) + $openid->getAttributes();
		} elseif ($code = $_REQUEST['code']) {
			$access_token = $this->get_access_token($code);
			$graph_url = "https://graph.facebook.com/me?" . $access_token;
			$user = json_decode(file_get_contents($graph_url), true);
			return array(
				'identity' => $user['id'],
				'contact/email' => $user['email'],
				'namePerson/first' => $user['first_name'],
				'namePerson/last' => $user['last_name']			
			);
		}
		return false;
	}
	
	function get_access_token($code) {
		$token_url = 'https://graph.facebook.com/oauth/access_token?client_id='
        . $this->fb_app_id . "&redirect_uri=" . urlencode($this->redirect_url) . "&client_secret="
        . $this->fb_app_secret . "&code=" . $code;
		return file_get_contents($token_url);
	}
}
?>
<?php
defined('SU_BASE_DIR') or define('SU_BASE_DIR', dirname(__FILE__) . "/");

// Class files
function SU_autoload($name) {
	if (0 === strpos($name, 'SU_')) {
		$name = str_replace('SU_', '', $name);
	}
	
	// Modules folder
	if (file_exists(SU_BASE_DIR . 'modules/' . strtolower($name) . '.php')) {
		require_once(SU_BASE_DIR . 'modules/' . strtolower($name) . '.php');
	}
	
	// Kirby plugins
	if (file_exists(SU_BASE_DIR . 'libs/kirby/plugins/' . $name . '.php')) {
		require_once(SU_BASE_DIR . 'libs/kirby/plugins/' . $name . '.php');
	}
}

// Kirby
if (file_exists(SU_BASE_DIR . 'libs/kirby/kirby.php')) {
	require_once(SU_BASE_DIR . 'libs/kirby/kirby.php');
}

// Preload class
$preload = array('route');
array_map(SU_autoload, $preload);

// Nonce class
if (file_exists(SU_BASE_DIR . 'libs/nonce.class.php')) {
	require_once(SU_BASE_DIR . 'libs/nonce.class.php');
}

spl_autoload_register('SU_autoload');
?>
<?php 

defined('SU_URL_HOST') or define('SU_URL_HOST', 1);
defined('SU_URL_SUB_HOST') or define('SU_URL_SUB_HOST', 2);

defined('SU_ROUTE_HOST') or define('SU_ROUTE_HOST', (stripos(server::get('HTTP_HOST'), 'www.') === 0) ? substr(server::get('HTTP_HOST'), 4) : server::get('HTTP_HOST'));
defined('SU_ROUTE_SUB_HOST') or define('SU_ROUTE_SUB_HOST', a::first(explode('.', SU_ROUTE_HOST)));

defined('SU_ROUTE_REQUEST_PATH') or define('SU_ROUTE_REQUEST_PATH', a::first(explode('?', server::get('request_uri'))));

c::set('route.base.path', c::get('route.base.path', '/'));

class SU_Route {
	public static $calls = 0;

	// $route, $callback1[, $callback2...]
	function __construct($route, $callback) {
		$args = func_get_args();
	
		$base_host = c::get('route.base.host');
		#$base_path = str_replace(c::get('route.base.path', '/'), '', SU_ROUTE_REQUEST_PATH);
		$base_path = substr(strstr(SU_ROUTE_REQUEST_PATH, c::get('route.base.path', '/')), strlen(c::get('route.base.path', '/')));
		if (is_array($route)) {
			$base = $route[0];
			$path = $route[1];
		} else {
			$base = '*';
			$path = $route;
		}
		
		if ($path == '404!') {
			register_shutdown_function(array('SU_Route', 'call_404'), $args, ($base === SU_URL_HOST || $base == '*') ? SU_ROUTE_HOST : SU_ROUTE_SUB_HOST, $base_path);
		}
		
		// replace for regex
		$path = str_replace(array('/', ':id'), array('\/', '\d+'), $path);
		$path = '/^'.$path.'$/i';
		
		// Main
		if ($base === SU_URL_HOST) {
			if (SU_ROUTE_HOST == $base_host &&
				preg_match($path, $base_path)) {
				$this->_callback($args, SU_ROUTE_HOST, $base_path);
			}
		// All subdomain
		} elseif ($base === SU_URL_SUB_HOST) {
			if (SU_ROUTE_SUB_HOST.'.'.$base_host == SU_ROUTE_HOST &&
				preg_match($path, $base_path)) {
				$this->_callback($args, SU_ROUTE_SUB_HOST, $base_path);
			}
		// Main and subdomain
		} elseif ($base == '*') {
			if (preg_match($path, $base_path)) {
				$this->_callback($args, SU_ROUTE_HOST, $base_path);
			}
		// Specific subdomain
		} elseif ($base == SU_ROUTE_SUB_HOST) {
			if (preg_match($path, $base_path)) {
				$this->_callback($args, SU_ROUTE_SUB_HOST, $base_path);
			}
		}
	}
	
	function call_404($args, $route, $path) {
		if (self::$calls == 0) {
			@header("HTTP/1.1 404 Not Found");
			self::_callback($args, $route, $path);
		}
	}
	
	function _callback($args, $route, $path) {
		for($i = 1; $i < count($args); $i++) {
			self::$calls++;
			$callback = $args[$i];
			$return = $callback($route, $path);
			if ($return === false) {
				break;
			}
		}
	}
}
?>
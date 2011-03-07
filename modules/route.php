<?php 

defined('SU_URL_HOST') or define('SU_URL_HOST', 2);
defined('SU_URL_SUB_HOST') or define('SU_URL_SUB_HOST', 3);

defined('SU_ROUTE_HOST') or define('SU_ROUTE_HOST', (stripos(server::get('HTTP_HOST'), 'www.') === 0) ? substr(server::get('HTTP_HOST'), 4) : server::get('HTTP_HOST'));
defined('SU_ROUTE_SUB_HOST') or define('SU_ROUTE_SUB_HOST', a::first(explode('.', SU_ROUTE_HOST)));

defined('SU_ROUTE_REQUEST_PATH') or define('SU_ROUTE_REQUEST_PATH', a::first(explode('?', server::get('request_uri'))));

class SU_Route {
	function __construct($route, $callback) {
		echo "construct";
		$base_host = c::get('route.base.host');
		$base_path = str_replace(c::get('route.base.path', '/'), '', SU_ROUTE_REQUEST_PATH);
		
		$base = $route[0];
		$path = $route[1];
		
		// replace for regex
		$path = str_replace(array('/', ':id'), array('\/', '\d'), $path);
		$path = '/^'.$path.'$/i';
		
		// Main
		if ($base === SU_URL_HOST) {
			if (SU_ROUTE_HOST == $base_host &&
				preg_match($path, $base_path)) {
				$callback(SU_ROUTE_HOST, $base_path);
			}
		// All subdomain
		} elseif ($base === SU_URL_SUB_HOST) {
			if (SU_ROUTE_SUB_HOST.'.'.$base_host == SU_ROUTE_HOST &&
				preg_match($path, $base_path)) {
				$callback(SU_ROUTE_SUB_HOST, $base_path);
			}
		// Main and subdomain
		} elseif ($base == '*') {
			if (preg_match($path, $base_path)) {
				$callback(SU_ROUTE_HOST, $base_path);
			}
		// Specific subdomain
		} elseif ($base == SU_ROUTE_SUB_HOST) {
			if (preg_match($path, $base_path)) {
				$callback(SU_ROUTE_SUB_HOST, $base_path);
			}
		}
	}
}
?>
<?php 
class SU_Core {
	public $version = '0.1';

	public function __get($name) {
		echo $name;
		$reflection = new ReflectionClass('SU_' . $name);
		return $reflection->newInstanceArgs();
	}

	public static function __callStatic($name, $arguments) {
		//$name = 'SU_' . $name;
		//return new $name($arguments);
		$reflection = new ReflectionClass('SU_' . $name);
		return $reflection->newInstanceArgs($arguments);
    }
	
	public static function view($file, $data=null, $output=true) {
		if (is_array($data)) {
			extract($data);
		}
		if (file_exists(BASE_DIR . 'views/' . $file)) {
			ob_start();
			require(BASE_DIR . 'views/' . $file);
			return ($output) ? ob_get_flush() : ob_get_clean();
		}
	}
}
?>
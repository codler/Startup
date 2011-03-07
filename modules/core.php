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
}
?>
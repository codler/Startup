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
	
	public static function view($file, $data=null, $return=false) {

		$file .= c::get('view.extension','');

		if (is_array($data)) {
			extract($data);
		}
		if (file_exists(BASE_DIR . c::get('view.path','views') . '/' . $file) &&
			is_file(BASE_DIR . c::get('view.path','views') . '/' . $file)) {
			content::start();
			require(BASE_DIR . c::get('view.path','views') . '/' . $file);
			return content::end($return);
		}
	}
	
	/**
	 * Max uploadable file to server
	 *
	 * @require Ui class
	 * @return int Number of bytes
	 */
	function max_upload() {
		return min(SU::Ui()->to_bytes(ini_get('post_max_size')),
			SU::Ui()->to_bytes(ini_get('upload_max_filesize')));
	}
	
	
}
?>
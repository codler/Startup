<?php 
class SU_Core {
	public $version = '0.1';

	public function __get($name) {
		echo $name;
		$reflection = new ReflectionClass('SU_' . $name);
		return $reflection->newInstanceArgs();
	}
	
	/**
	 * Instantiate SU_X classes
	 */
	public static function __callStatic($name, $arguments) {
		$reflection = new ReflectionClass('SU_' . $name);
		
		/* Bugfix - this if-state is required or you will get this in some server
		// Fatal error: Uncaught exception 'ReflectionException' with message 'Class SU_X does not have a constructor, so you cannot pass any constructor arguments'
		 */
		if ($arguments) {
			return $reflection->newInstanceArgs($arguments);
		} else {
			return $reflection->newInstanceArgs();
		}
    }
	
	/**
	 * Simple View for MVC
	 * 
	 * @param string $filename Filename, extension is optional if config view.extension is set
	 * @param array $data key/value => variablename/variablevalue
	 * @param boolean $return return or buffer out content
	 * @return mixed content of file or null
	 */
	public static function view($filename, $data=null, $return=false) {
		$filename .= c::get('view.extension');
		$f = BASE_DIR . c::get('view.path') . '/' . $filename;
		
		if (file_exists($f) && is_file($f)) {
			// Avoid variable conflict
			$SU_f = $f;
			$SU_return = $return;
			
			// Set variable in the view file.
			if (is_array($data)) extract($data);
			
			content::start();
			require($SU_f);
			return content::end($SU_return);
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
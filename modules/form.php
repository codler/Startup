<?php 

class SU_Form {
	// HTML buffer
	public static $html = '';
	
	// PARAMETER
	// field [,value [, attr]]
	// field [, attr]
	function __call($name, $arguments) {
		if (is_array($arguments[1])) {
			$attr = $arguments[1];
		} elseif(isset($arguments[1])) {
			$attr = $arguments[2];
			$attr['value'] = $arguments[1];
		}
		$attr['type'] = $name;
		$attr['name'] = $arguments[0];
		if ($attr['type'] == 'checkbox') {
			$attr['name'] .= '[]';
		}
		return $this->input($attr);
	}
	
	public $default_attr=array();
	function __construct($default_attr=array()) {
		$this->default_attr = $default_attr;
	}
	
	// @param string $name Unique form identifier, used for method verify
	// Recommend using this instead of making own "<form>"-tag. This provide safety mechanism that will prevent CSRF.
	function open($name, $location='', $method='post', $attr=array()) {
		$nonce = (isset($attr['nonce'])) ? Nonce::create($name, $attr['nonce']) : Nonce::create($name);
		unset($attr['nonce']);
		
		$attr['action'] = $location;
		$attr['method'] = $method;
		$attr = array_merge($this->default_attr, $attr);
		self::$html .= $html = "<form" . $this->parse_attr($attr) . ">\r\n";
		$html .= $this->hidden('_su_type', $name);
		$html .= $this->hidden('_su_nonce', $nonce);
		return $html;
	}
	
	function verify($name, $uid=false) {
		$nonce = r::get('_su_nonce');
		return Nonce::verify($nonce, $name, $uid);
	}
	
	function close() {
		self::$html .= $html = "</form>\r\n";
		return $html;
	}
	
	function label($value, $id=null, $attr=array()) {
		$attr = array_merge($this->default_attr, $attr);
		if ($id) {
			$attr['for']=$id;
		}
		self::$html .= $html = "<label" . $this->parse_attr($attr) . ">" . $value . "</label>\r\n";
		return $html;
	}
	
	function textarea($field, $value=null, $attr=array()) {
		if (is_array($value)) {
			$attr = $value;
			$value = null;
		}
		$attr = array_merge($this->default_attr, $attr);
		$attr['name'] = $field;
		self::$html .= $html = "<textarea" . $this->parse_attr($attr) . ">" . $value . "</textarea>\r\n";
		return $html;
	}
	
	function input($attr) {
		$attr = array_merge($this->default_attr, $attr);
		self::$html .= $html = "<input" . $this->parse_attr($attr) . "/>\r\n";
		return $html;
	}
	
	function set_message($id, $message, $type) {
		c::set('form.message.' . $id, compact('type', 'message'));
	}
	
	function message($id) {
		if ($msg = c::get('form.message.' . $id, false)) {
			self::$html .= $html = "<span class=\"" . $msg['type'] . "\">" . $msg['message'] . "</span>\r\n";
			return $html;
		}
	}
	
	function render() {
		$html = self::$html;
		self::$html = '';
		return $html;
	}
	
	// TODO move to UI class
	public $only_attr = array('autofocus', 'checked', 'disabled', 'multiple', 'novalidate', 'required', 'selected');
	function parse_attr($array) {
		$html = ' ';
		foreach($array AS $attr => $value) {
			if (is_numeric($attr) && in_array($value, $this->only_attr)) {
				$html .= $value . ' ';
			} else {
				// htmlspecialchars for anti-XSS
				$html .= $attr . '="' . htmlspecialchars($value) . '" ';
			}
		}
		return $html;
	}
	
	/**
	 * Upload file
	 *
	 * @param array $options 
	 * @return array
	 */
	function upload($options) {
		/* 	
		$options['field'] // (required) source string
		$options['path'] // (required) source string
		*/
		$options['image'] = (isset($options['image'])) ? $options['image'] : true; // default true
		$options['max_size'] = (isset($options['max_size'])) ? min($options['max_size'],SU::Core()->max_upload()) : SU::Core()->max_upload(); // default server max upload in bytes

		if (empty($options['field']) || empty($options['path'])) {
			return array('error' => 'Option field and path is required');
		}
		
		if (!isset($_FILES[$options['field']])) {
			return array('error' => 'No file was selected');
		}
		
		// validate path
		$upload_path = $options['path'];
		$upload_path = rtrim($upload_path, '/').'/';
		if (@realpath($upload_path) !== false) {
			$upload_path = str_replace("\\", "/", realpath($upload_path));
		}
		if(!file_exists($upload_path)) {
			if (!@mkdir($upload_path, 0777)) {
				return array('error' => 'Directory isnt writable');
			}
			chmod($upload_path, 0777);
		}
		if (!@is_dir($upload_path) || !is_writable($upload_path)) {
			return array('error' => 'Directory isnt writable');
		}
		$upload_path = preg_replace("/(.+?)\/*$/", "\\1/",  $upload_path); // ?
		
		
		// Remapping for loop
		if (!is_array($_FILES[$options['field']]['tmp_name'])) {
			$_FILES[$options['field']] = array_map(function ($item) {
				return array($item);
			}, $_FILES[$options['field']]);
		}
		
		$success = array();
		foreach($_FILES[$options['field']]['tmp_name'] AS $key => $value) {
			// Get upload info
			$error = $_FILES[$options['field']]['error'][$key];
			$name = $_FILES[$options['field']]['name'][$key];
			$tmp_name = $_FILES[$options['field']]['tmp_name'][$key];
			$size = $_FILES[$options['field']]['size'][$key];
			$type = $_FILES[$options['field']]['type'][$key];
			
			if (!is_uploaded_file($tmp_name) || $error != UPLOAD_ERR_OK) {
				continue;
			}
			
			$type = preg_replace("/^(.+?);.*$/", "\\1", $type); // ?
			$type = strtolower(trim(stripslashes($type), '"'));
			$ext = f::extension($name);
			$name = f::safe_name(f::name($name));
			$name = substr($name, 0, 100);
			
			// Check allowed file type
			$image_types = array('gif', 'jpg', 'jpeg', 'png', 'jpe');
			if ($options['image']) {
				if (!in_array($ext, ((is_array($options['image'])) ? $options['image'] : $image_types)) || !SU::File()->is_image($type) || getimagesize($tmp_name) === false) {
					continue;
				}
			}
			
			// Check file size
			if ($options['max_size'] < $size) {
				continue;
			}
			
			// Unique filename
			if (file_exists($upload_path.$name.".".$ext)) {
				$number = 1;
				while (file_exists($upload_path.$name.$number.".".$ext)){
					$number++;
				}
				$name = $name . $number; 
			}
			
			// save
			if (!@move_uploaded_file($tmp_name, $upload_path.$name.".".$ext)) {
				continue;
			}
			
			// TODO xss clean
			
			
			
			$success[] = array(
				'extension' => $ext,
				'filename' => $name.".".$ext,
				'original_filename' => $_FILES[$options['field']]['name'][$key],
				'name' => $name,
				'size' => $size,
				'nice_size' => f::nice_size($size),
				'md5' => md5(file_get_contents($upload_path.$name.".".$ext))
			);
		}
		return array(
			'failed' => count($_FILES[$options['field']]['tmp_name']) - count($success),
			'success' => $success
		);
	}
}
?>
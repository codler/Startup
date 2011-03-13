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
}
?>
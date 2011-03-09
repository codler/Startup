<?php 

class SU_Form {
	
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
		$html = "<form" . $this->parse_attr($attr) . ">\r\n";
		$html .= $this->hidden('_su_type', $name);
		$html .= $this->hidden('_su_nonce', $nonce);
		return $html;
	}
	
	function verify($name, $uid=false) {
		$nonce = r::get('_su_nonce');
		return Nonce::verify($nonce, $name, $uid);
	}
	
	function close() {
		return "</form>\r\n";
	}
	
	function label($value, $id=null, $attr=array()) {
		$attr = array_merge($this->default_attr, $attr);
		if ($id) {
			$attr['for']=$id;
		}
		return "<label" . $this->parse_attr($attr) . ">" . $value . "</label>\r\n";
	}
	
	function textarea($field, $value=null, $attr=array()) {
		if (is_array($value)) {
			$attr = $value;
			$value = null;
		}
		$attr = array_merge($this->default_attr, $attr);
		$attr['name'] = $field;
		return "<textarea" . $this->parse_attr($attr) . ">" . $value . "</textarea>\r\n";
	}
	
	function input($attr) {
		$attr = array_merge($this->default_attr, $attr);
		return "<input" . $this->parse_attr($attr) . "/>\r\n";
	}
	
	function set_message($id, $message, $type) {
		c::set('form.message.' . $id, compact('type', 'message'));
	}
	
	function message($id) {
		if ($msg = c::get('form.message.' . $id, false)) {
			return "<span class=\"" . $msg['type'] . "\">" . $msg['message'] . "</span>\r\n";
		}
	}
	
	// TODO move to UI class
	public $only_attr = array('autofocus', 'checked', 'disabled', 'multiple', 'novalidate', 'required', 'selected');
	function parse_attr($array) {
		$html = ' ';
		foreach($array AS $attr => $value) {
			if (is_numeric($attr) && in_array($value, $this->only_attr)) {
				$html .= $value . ' ';
			} else {
				$html .= $attr . '="' . $value . '" ';
			}
		}
		return $html;
	}
}
?>
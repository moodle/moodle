<?php

class com_wiris_quizzes_impl_Result extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function onSerializeInner($s) {
		$this->errors = $s->serializeArray($this->errors, com_wiris_quizzes_impl_ResultError::$tagName);
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_Result();
	}
	public function onSerialize($s) {
	}
	public $errors;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	function __toString() { return 'com.wiris.quizzes.impl.Result'; }
}

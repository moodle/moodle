<?php

class com_wiris_quizzes_impl_ResultError extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ResultError::$tagName);
		$this->type = $s->attributeString("type", $this->type, null);
		$this->id = $s->attributeString("id", $this->id, null);
		$this->location = $s->serializeChild($this->location);
		$this->detail = $s->childString("detail", $this->detail, null);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ResultError();
	}
	public $id;
	public $type;
	public $detail;
	public $location;
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
	static $tagName = "error";
	static $TYPE_MATHSYNTAX = "mathSyntax";
	static $TYPE_PARAMVALUE = "paramValue";
	function __toString() { return 'com.wiris.quizzes.impl.ResultError'; }
}

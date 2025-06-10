<?php

class com_wiris_quizzes_impl_Property extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_Property();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_Property::$tagName);
		$this->name = $s->attributeString("name", $this->name, null);
		$this->value = $s->textContent($this->value);
		$s->endTag();
	}
	public $value;
	public $name;
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
	static $tagName = "property";
	function __toString() { return 'com.wiris.quizzes.impl.Property'; }
}

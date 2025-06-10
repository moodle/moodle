<?php

class com_wiris_quizzes_impl_Variable extends com_wiris_quizzes_impl_MathContent {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_Variable::$tagName);
		$this->name = $s->attributeString("name", $this->name, null);
		$this->onSerializeInner($s);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_Variable();
	}
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
	static $tagName = "variable";
	function __toString() { return 'com.wiris.quizzes.impl.Variable'; }
}

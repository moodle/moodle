<?php

class com_wiris_quizzes_impl_ResultGetCheckAssertions extends com_wiris_quizzes_impl_Result {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ResultGetCheckAssertions();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ResultGetCheckAssertions::$tagName);
		$this->onSerializeInner($s);
		$this->checks = $s->serializeArray($this->checks, com_wiris_quizzes_impl_AssertionCheckImpl::$tagName);
		$s->endTag();
	}
	public $checks;
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
	static $tagName = "getCheckAssertionsResult";
	function __toString() { return 'com.wiris.quizzes.impl.ResultGetCheckAssertions'; }
}

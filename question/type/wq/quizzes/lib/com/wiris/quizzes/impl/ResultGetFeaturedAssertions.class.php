<?php

class com_wiris_quizzes_impl_ResultGetFeaturedAssertions extends com_wiris_quizzes_impl_Result {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ResultGetFeaturedAssertions();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ResultGetFeaturedAssertions::$tagName);
		$this->onSerializeInner($s);
		$this->assertions = $s->serializeArray($this->assertions, com_wiris_quizzes_impl_Assertion::$tagName);
		$s->endTag();
	}
	public $assertions;
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
	static $tagName = "getFeaturedAssertionsResult";
	function __toString() { return 'com.wiris.quizzes.impl.ResultGetFeaturedAssertions'; }
}

<?php

class com_wiris_quizzes_impl_ProcessGetTranslation extends com_wiris_quizzes_impl_Process {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ProcessGetTranslation();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ProcessGetTranslation::$tagName);
		$this->lang = $s->attributeString("lang", $this->lang, null);
		$s->endTag();
	}
	public $lang;
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
	static $tagName = "getTranslation";
	function __toString() { return 'com.wiris.quizzes.impl.ProcessGetTranslation'; }
}

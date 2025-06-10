<?php

class com_wiris_quizzes_impl_ProcessGetVariables extends com_wiris_quizzes_impl_Process {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ProcessGetVariables();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ProcessGetVariables::$TAGNAME);
		$this->names = $s->attributeString("names", $this->names, null);
		$this->type = $s->attributeString("type", $this->type, "mathml");
		$s->endTag();
	}
	public $type;
	public $names;
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
	static $TAGNAME = "getVariables";
	function __toString() { return 'com.wiris.quizzes.impl.ProcessGetVariables'; }
}

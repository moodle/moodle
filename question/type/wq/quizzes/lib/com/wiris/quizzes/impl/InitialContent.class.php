<?php

class com_wiris_quizzes_impl_InitialContent extends com_wiris_quizzes_impl_MathContent {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_InitialContent::$TAGNAME);
		$this->onSerializeInner($s);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_InitialContent();
	}
	static $TAGNAME = "initialContent";
	function __toString() { return 'com.wiris.quizzes.impl.InitialContent'; }
}

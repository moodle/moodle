<?php

class com_wiris_quizzes_impl_ProcessGetCheckAssertions extends com_wiris_quizzes_impl_Process {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ProcessGetCheckAssertions();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ProcessGetCheckAssertions::$tagName);
		$s->endTag();
	}
	static $tagName = "getCheckAssertions";
	function __toString() { return 'com.wiris.quizzes.impl.ProcessGetCheckAssertions'; }
}

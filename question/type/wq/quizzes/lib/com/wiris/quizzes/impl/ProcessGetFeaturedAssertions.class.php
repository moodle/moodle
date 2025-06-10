<?php

class com_wiris_quizzes_impl_ProcessGetFeaturedAssertions extends com_wiris_quizzes_impl_Process {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ProcessGetFeaturedAssertions();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ProcessGetFeaturedAssertions::$TAGNAME);
		$s->endTag();
	}
	static $TAGNAME = "getFeaturedAssertions";
	function __toString() { return 'com.wiris.quizzes.impl.ProcessGetFeaturedAssertions'; }
}

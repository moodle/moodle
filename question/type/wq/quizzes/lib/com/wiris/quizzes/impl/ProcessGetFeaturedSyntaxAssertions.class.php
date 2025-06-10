<?php

class com_wiris_quizzes_impl_ProcessGetFeaturedSyntaxAssertions extends com_wiris_quizzes_impl_Process {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ProcessGetFeaturedSyntaxAssertions();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ProcessGetFeaturedSyntaxAssertions::$TAGNAME);
		$s->endTag();
	}
	static $TAGNAME = "getFeaturedSyntaxAssertions";
	function __toString() { return 'com.wiris.quizzes.impl.ProcessGetFeaturedSyntaxAssertions'; }
}

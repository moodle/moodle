<?php

class com_wiris_quizzes_impl_Process extends com_wiris_util_xml_SerializableImpl {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_Process();
	}
	public function onSerialize($s) {
	}
	function __toString() { return 'com.wiris.quizzes.impl.Process'; }
}

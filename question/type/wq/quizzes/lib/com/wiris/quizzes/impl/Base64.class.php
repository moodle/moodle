<?php

class com_wiris_quizzes_impl_Base64 extends haxe_BaseCode {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct(haxe_io_Bytes::ofString("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"));
	}}
	public function decodeBytes($bytes) {
		return haxe_io_Bytes::ofString(base64_decode($bytes->b));
	}
	function __toString() { return 'com.wiris.quizzes.impl.Base64'; }
}

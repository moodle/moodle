<?php

class com_wiris_system_Md5Tools {
	public function __construct(){}
	static function encodeString($content) {
		return haxe_Md5::encode($content);
	}
	static function encodeBytes($content) {
		return haxe_Md5::encode($content->toString());
	}
	function __toString() { return 'com.wiris.system.Md5Tools'; }
}

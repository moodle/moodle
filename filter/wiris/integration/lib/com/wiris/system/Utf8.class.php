<?php

class com_wiris_system_Utf8 {
	public function __construct() { 
	}
	static function getLength($s) {
		return haxe_Utf8::length($s);
	}
	static function charCodeAt($s, $i) {
		return haxe_Utf8::charCodeAt($s, $i);
	}
	static function charAt($s, $i) {
		return com_wiris_system_Utf8_0($i, $s);
	}
	static function uchr($i) {
		$s = new haxe_Utf8(null);
		$s->addChar($i);
		return $s->toString();
	}
	static function sub($s, $pos, $len) {
		return haxe_Utf8::sub($s, $pos, $len);
	}
	static function toBytes($s) {
		return haxe_io_Bytes::ofString($s)->b;
	}
	static function fromBytes($s) {
		$bs = haxe_io_Bytes::ofData($s);
		return $bs->toString();
	}
	static function getIterator($s) {
		return new com_wiris_system__Utf8_StringIterator($s);
	}
	function __toString() { return 'com.wiris.system.Utf8'; }
}
function com_wiris_system_Utf8_0(&$i, &$s) {
	{
		$s1 = new haxe_Utf8(null);
		$s1->addChar(haxe_Utf8::charCodeAt($s, $i));
		return $s1->toString();
	}
}

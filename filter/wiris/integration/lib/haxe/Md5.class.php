<?php

class haxe_Md5 {
	public function __construct(){}
	static function encode($s) {
		return md5($s);
	}
	function __toString() { return 'haxe.Md5'; }
}

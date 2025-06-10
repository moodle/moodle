<?php

class haxe_Timer {
	public function __construct(){}
	static function measure($f, $pos = null) {
		$t0 = haxe_Timer::stamp();
		$r = call_user_func($f);
		haxe_Log::trace(_hx_string_rec(haxe_Timer::stamp() - $t0, "") . "s", $pos);
		return $r;
	}
	static function stamp() {
		return Sys::time();
	}
	function __toString() { return 'haxe.Timer'; }
}

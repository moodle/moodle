<?php

class com_wiris_util_type_IntegerTools {
	public function __construct(){}
	static function sign($value) {
		return (($value >= 0) ? 1 : -1);
	}
	static function signBool($value) {
		return (($value) ? 1 : -1);
	}
	static function max($x, $y) {
		return (($x > $y) ? $x : $y);
	}
	static function min($x, $y) {
		return (($x < $y) ? $x : $y);
	}
	static function clamp($x, $a, $b) {
		return com_wiris_util_type_IntegerTools::min(com_wiris_util_type_IntegerTools::max($a, $x), $b);
	}
	static function inRange($x, $start, $end) {
		return $x >= $start && $x < $end;
	}
	static function isInt($x) {
		return _hx_deref(new EReg("^[\\+\\-]?\\d+\$", ""))->match($x);
	}
	function __toString() { return 'com.wiris.util.type.IntegerTools'; }
}

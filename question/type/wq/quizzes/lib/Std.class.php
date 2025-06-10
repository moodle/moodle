<?php

class Std {
	public function __construct(){}
	static function is($v, $t) {
		return _hx_instanceof($v, $t);
	}
	static function string($s) {
		return _hx_string_rec($s, "");
	}
	static function int($x) {
		return intval($x);
	}
	static function parseInt($x) {
		$x = ltrim($x);
		$firstCharIndex = ((_hx_char_at($x, 0) === "-") ? 1 : 0);
		$firstCharCode = _hx_char_code_at($x, $firstCharIndex);
		if(!($firstCharCode !== null && $firstCharCode >= 48 && $firstCharCode <= 57)) {
			return null;
		}
		$secondChar = _hx_char_at($x, $firstCharIndex + 1);
		if($secondChar === "x" || $secondChar === "X") {
			return intval($x, 0);
		} else {
			return intval($x, 10);
		}
	}
	static function parseFloat($x) {
		$result = floatval($x);
		if($result != 0) {
			return $result;
		}
		$x = ltrim($x);
		$firstCharIndex = ((_hx_char_at($x, 0) === "-") ? 1 : 0);
		$charCode = _hx_char_code_at($x, $firstCharIndex);
		if($charCode === 46) {
			$charCode = _hx_char_code_at($x, $firstCharIndex + 1);
		}
		if($charCode !== null && $charCode >= 48 && $charCode <= 57) {
			return 0.0;
		} else {
			return Math::$NaN;
		}
	}
	static function random($x) {
		return mt_rand(0, $x - 1);
	}
	static function isDigitCode($charCode) {
		return $charCode !== null && $charCode >= 48 && $charCode <= 57;
	}
	function __toString() { return 'Std'; }
}

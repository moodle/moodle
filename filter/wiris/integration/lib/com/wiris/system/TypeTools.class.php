<?php

class com_wiris_system_TypeTools {
	public function __construct(){}
	static function floatToString($value) {
		return "" . _hx_string_rec($value, "");
	}
	static function isFloating($str) {
		$pattern = new EReg("^[+-]?\\d*\\.?\\d+([eE][+-]?\\d+)?\$", "");
		return $pattern->match($str);
	}
	static function isInteger($str) {
		$pattern = new EReg("^[+-]?\\d+\$", "");
		return $pattern->match($str);
	}
	static function isIdentifierPart($c) {
		$letterPattern = new EReg("[a-z]", "i");
		$numberPattern = new EReg("[0-9]", "");
		$str = chr($c);
		return $letterPattern->match($str) || $numberPattern->match($str) || $str === "_";
	}
	static function isIdentifierStart($c) {
		$letterPattern = new EReg("[a-z]", "i");
		$str = chr($c);
		return $letterPattern->match($str) || $str === "_";
	}
	static function isArray($o) {
		return Std::is($o, _hx_qtype("Array"));
	}
	static function isHash($o) {
		return Std::is($o, _hx_qtype("Hash"));
	}
	static function string2ByteData_iso8859_1($str) {
		return haxe_io_Bytes::ofString($str);
	}
	static function hashParamsToObjectParams($params) {
		if($params === null) {
			return null;
		}
		$paramObject = _hx_anonymous(array());
		$i = $params->keys();
		while($i->hasNext()) {
			$key = $i->next();
			$value = $params->get($key);
			$paramObject->{$key} = $value;
			unset($value,$key);
		}
		return $paramObject;
	}
	static function longBitsToDouble($notused, $numberhigh, $numberlow) {
		$numberint64 = new haxe_Int64($numberhigh, $numberlow);
		$high7ff = 0 | 0;
		$low7ff = 2047 | 0;
		$int647ff = new haxe_Int64($high7ff, $low7ff);
		$highfff = 1048575 | 0;
		$lowfff = -1 | 0;
		$int64fff = new haxe_Int64($highfff, $lowfff);
		$high100 = 1048576 | 0;
		$low100 = 0 | 0;
		$int64100 = new haxe_Int64($high100, $low100);
		$shiftright63 = new haxe_Int64($numberint64->high >> 31, $numberint64->high >> 31);
		$s = ((($shiftright63->high | $shiftright63->low) === 0) ? 1 : -1);
		$shiftright52 = new haxe_Int64($numberint64->high >> 31, $numberint64->high >> 20);
		$and7ff = new haxe_Int64($shiftright52->high & $int647ff->high, $shiftright52->low & $int647ff->low);
		$eint64 = $and7ff;
		$andfff = new haxe_Int64($numberint64->high & $int64fff->high, $numberint64->low & $int64fff->low);
		$shift1 = new haxe_Int64($andfff->high << 1 | _hx_shift_right($andfff->low, 31), $andfff->low << 1);
		$or100 = new haxe_Int64($andfff->high | $int64100->high, $andfff->low | $int64100->low);
		$mint64 = ((($eint64->high | $eint64->low) === 0) ? $shift1 : $or100);
		$estring = $eint64->toString();
		$e = Std::parseFloat($estring);
		$mstring = $mint64->toString();
		$m = Std::parseFloat($mstring);
		$finalResult = $s * Math::pow(2, $e - 1075) * $m;
		return $finalResult;
	}
	static function intBitsToFloat($bits) {
		$s = (($bits >> 31 === 0) ? 1 : -1);
		$e = $bits >> 23 & 255;
		$m = com_wiris_system_TypeTools_0($bits, $e, $s);
		return $s * $m * Math::pow(2, $e - 150);
	}
	function __toString() { return 'com.wiris.system.TypeTools'; }
}
function com_wiris_system_TypeTools_0(&$bits, &$e, &$s) {
	if($e === 0) {
		return ($bits & 8388607) << 1;
	} else {
		return $bits & 8388607 | 8388608;
	}
}

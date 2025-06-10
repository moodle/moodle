<?php

class com_wiris_system_UUIDUtils {
	public function __construct(){}
	static $rndSeed;
	static $state0;
	static $state1;
	static function splitmix64_seed($num) {
		$index = new haxe_Int64($num >> 31 | 0, $num | 0);
		$n1 = -1640531527 | 0;
		$n2 = 2135587861 | 0;
		$n3 = -1084733587 | 0;
		$n4 = 484763065 | 0;
		$n5 = -1798288965 | 0;
		$n6 = 321982955 | 0;
		$result = haxe_Int64::add($index, new haxe_Int64($n1, $n2));
		$result = haxe_Int64::mul(com_wiris_system_UUIDUtils_0($index, $n1, $n2, $n3, $n4, $n5, $n6, $num, $result), new haxe_Int64($n3, $n4));
		$result = haxe_Int64::mul(com_wiris_system_UUIDUtils_1($index, $n1, $n2, $n3, $n4, $n5, $n6, $num, $result), new haxe_Int64($n5, $n6));
		return com_wiris_system_UUIDUtils_2($index, $n1, $n2, $n3, $n4, $n5, $n6, $num, $result);
	}
	static function randomFromRange($min, $max) {
		$n1 = new haxe_Int64(0 | 0, 1 | 0);
		$s1 = com_wiris_system_UUIDUtils::$state0;
		$s0 = com_wiris_system_UUIDUtils::$state1;
		com_wiris_system_UUIDUtils::$state0 = $s0;
		$s1 = com_wiris_system_UUIDUtils_3($max, $min, $n1, $s0, $s1);
		com_wiris_system_UUIDUtils::$state1 = com_wiris_system_UUIDUtils_4($max, $min, $n1, $s0, $s1);
		$result32 = haxe_Int64::getLow(com_wiris_system_UUIDUtils_5($max, $min, $n1, $s0, $s1));
		$result = com_wiris_system_UUIDUtils_6($max, $min, $n1, $result32, $s0, $s1);
		$result = (($result < 0) ? -$result : $result);
		return $result + haxe_Int64::toInt($min);
	}
	static function randomByte() {
		return com_wiris_system_UUIDUtils::randomFromRange(new haxe_Int64(0 | 0, 0 | 0), new haxe_Int64(0 | 0, 255 | 0));
	}
	static function unparse($data) {
		$hex = $data->toHex();
		$uuid = _hx_substr($hex, 0, 8) . "-" . _hx_substr($hex, 8, 4) . "-" . _hx_substr($hex, 12, 4) . "-" . _hx_substr($hex, 16, 4) . "-" . _hx_substr($hex, 20, 12);
		return $uuid;
	}
	static function generateV4($randBytes = null, $randomFunc = null) {
		if($randomFunc === null) {
			$randomFunc = (isset(com_wiris_system_UUIDUtils::$randomByte) ? com_wiris_system_UUIDUtils::$randomByte: array("com_wiris_system_UUIDUtils", "randomByte"));
		}
		$buffer = $randBytes;
		if($buffer === null) {
			$buffer = haxe_io_Bytes::alloc(16);
			{
				$_g = 0;
				while($_g < 16) {
					$i = $_g++;
					$buffer->b[$i] = chr(call_user_func($randomFunc));
					unset($i);
				}
			}
		} else {
			if($buffer->length < 16) {
				throw new HException("Random bytes should be at least 16 bytes");
			}
		}
		$buffer->b[6] = chr(ord($buffer->b[6]) & 15 | 64);
		$buffer->b[8] = chr(ord($buffer->b[8]) & 63 | 128);
		$uuid = com_wiris_system_UUIDUtils::unparse($buffer);
		return $uuid;
	}
	function __toString() { return 'com.wiris.system.UUIDUtils'; }
}
com_wiris_system_UUIDUtils::$rndSeed = intval(haxe_Timer::stamp() * 1000);
com_wiris_system_UUIDUtils::$state0 = com_wiris_system_UUIDUtils::splitmix64_seed(com_wiris_system_UUIDUtils::$rndSeed);
com_wiris_system_UUIDUtils::$state1 = com_wiris_system_UUIDUtils::splitmix64_seed(com_wiris_system_UUIDUtils::$rndSeed + 1);
function com_wiris_system_UUIDUtils_0(&$index, &$n1, &$n2, &$n3, &$n4, &$n5, &$n6, &$num, &$result) {
	{
		$b = new haxe_Int64($result->high >> 30, _hx_shift_right($result->low, 30) | $result->high << 2);
		return new haxe_Int64($result->high ^ $b->high, $result->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_1(&$index, &$n1, &$n2, &$n3, &$n4, &$n5, &$n6, &$num, &$result) {
	{
		$b = new haxe_Int64($result->high >> 27, _hx_shift_right($result->low, 27) | $result->high << 5);
		return new haxe_Int64($result->high ^ $b->high, $result->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_2(&$index, &$n1, &$n2, &$n3, &$n4, &$n5, &$n6, &$num, &$result) {
	{
		$b = new haxe_Int64($result->high >> 31, _hx_shift_right($result->low, 31) | $result->high << 1);
		return new haxe_Int64($result->high ^ $b->high, $result->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_3(&$max, &$min, &$n1, &$s0, &$s1) {
	{
		$b = new haxe_Int64($s1->high >> 23, _hx_shift_right($s1->low, 23) | $s1->high << 9);
		return new haxe_Int64($s1->high ^ $b->high, $s1->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_4(&$max, &$min, &$n1, &$s0, &$s1) {
	{
		$a = com_wiris_system_UUIDUtils_7($max, $min, $n1, $s0, $s1); $b = new haxe_Int64(_hx_shift_right($s0->high, 5), _hx_shift_right($s0->low, 5) | $s0->high << 27);
		return new haxe_Int64($a->high ^ $b->high, $a->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_5(&$max, &$min, &$n1, &$s0, &$s1) {
	{
		$a = haxe_Int64::add(com_wiris_system_UUIDUtils::$state1, $s0); $b = haxe_Int64::sub($max, haxe_Int64::add($min, $n1));
		$sign = ($a->high | $b->high) < 0;
		if($a->high < 0) {
			$a = com_wiris_system_UUIDUtils_8($a, $b, $max, $min, $n1, $s0, $s1, $sign);
		}
		if($b->high < 0) {
			$b = com_wiris_system_UUIDUtils_9($a, $b, $max, $min, $n1, $s0, $s1, $sign);
		}
		$m = haxe_Int64::divMod($a, $b)->modulus;
		if($sign) {
			$high = ~$m->high;
			$low = -$m->low;
			if($low === 0) {
				$high = $high + (1 | 0) | 0;
			}
			return new haxe_Int64($high, $low);
		} else {
			return $m;
		}
		unset($sign,$m,$b,$a);
	}
}
function com_wiris_system_UUIDUtils_6(&$max, &$min, &$n1, &$result32, &$s0, &$s1) {
	{
		if(($result32 >> 30 & 1) !== _hx_shift_right($result32, 31)) {
			throw new HException("Overflow " . Std::string($result32));
		}
		return $result32 & -1;
	}
}
function com_wiris_system_UUIDUtils_7(&$max, &$min, &$n1, &$s0, &$s1) {
	{
		$a = new haxe_Int64($s1->high ^ $s0->high, $s1->low ^ $s0->low); $b = new haxe_Int64(_hx_shift_right($s1->high, 18), _hx_shift_right($s1->low, 18) | $s1->high << 14);
		return new haxe_Int64($a->high ^ $b->high, $a->low ^ $b->low);
	}
}
function com_wiris_system_UUIDUtils_8(&$a, &$b, &$max, &$min, &$n1, &$s0, &$s1, &$sign) {
	{
		$high = ~$a->high;
		$low = -$a->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function com_wiris_system_UUIDUtils_9(&$a, &$b, &$max, &$min, &$n1, &$s0, &$s1, &$sign) {
	{
		$high = ~$b->high;
		$low = -$b->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}

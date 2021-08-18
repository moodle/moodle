<?php

class haxe_Int64 {
	public function __construct($high, $low) {
		if(!php_Boot::$skip_constructor) {
		$this->high = $high;
		$this->low = $low;
	}}
	public function toString() {
		if($this->high === 0 && $this->low === 0) {
			return "0";
		}
		$str = "";
		$neg = false;
		$i = $this;
		if($i->high < 0) {
			$neg = true;
			$i = haxe_Int64_0($this, $i, $neg, $str);
		}
		$ten = new haxe_Int64(0 | 0, 10 | 0);
		while(!(($i->high | $i->low) === 0)) {
			$r = haxe_Int64::divMod($i, $ten);
			$str = _hx_string_rec(haxe_Int64_1($this, $i, $neg, $r, $str, $ten), "") . $str;
			$i = $r->quotient;
			unset($r);
		}
		if($neg) {
			$str = "-" . $str;
		}
		return $str;
	}
	public $low;
	public $high;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function make($high, $low) {
		return new haxe_Int64($high, $low);
	}
	static function ofInt($x) {
		return new haxe_Int64($x >> 31 | 0, $x | 0);
	}
	static function ofInt32($x) {
		return new haxe_Int64($x >> 31, $x);
	}
	static function toInt($x) {
		if(haxe_Int64_2($x) !== 0) {
			if($x->high < 0) {
				return -haxe_Int64::toInt(haxe_Int64_3($x));
			}
			throw new HException("Overflow");
		}
		return haxe_Int64_4($x);
	}
	static function getLow($x) {
		return $x->low;
	}
	static function getHigh($x) {
		return $x->high;
	}
	static function add($a, $b) {
		$high = $a->high + $b->high | 0;
		$low = $a->low + $b->low | 0;
		if(haxe_Int32::ucompare($low, $a->low) < 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
	static function sub($a, $b) {
		$high = $a->high - $b->high | 0;
		$low = $a->low - $b->low | 0;
		if(haxe_Int32::ucompare($a->low, $b->low) < 0) {
			$high = $high - (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
	static function mul($a, $b) {
		$mask = 65535 | 0;
		$al = $a->low & $mask; $ah = _hx_shift_right($a->low, 16);
		$bl = $b->low & $mask; $bh = _hx_shift_right($b->low, 16);
		$p00 = $al * ($bl & 65535) + ($al * (_hx_shift_right($bl, 16)) << 16 | 0) | 0;
		$p10 = $ah * ($bl & 65535) + ($ah * (_hx_shift_right($bl, 16)) << 16 | 0) | 0;
		$p01 = $al * ($bh & 65535) + ($al * (_hx_shift_right($bh, 16)) << 16 | 0) | 0;
		$p11 = $ah * ($bh & 65535) + ($ah * (_hx_shift_right($bh, 16)) << 16 | 0) | 0;
		$low = $p00;
		$high = ($p11 + (_hx_shift_right($p01, 16)) | 0) + (_hx_shift_right($p10, 16)) | 0;
		$p01 = $p01 << 16;
		$low = $low + $p01 | 0;
		if(haxe_Int32::ucompare($low, $p01) < 0) {
			$high = $high + (1 | 0) | 0;
		}
		$p10 = $p10 << 16;
		$low = $low + $p10 | 0;
		if(haxe_Int32::ucompare($low, $p10) < 0) {
			$high = $high + (1 | 0) | 0;
		}
		$high = $high + haxe_Int64_5($a, $ah, $al, $b, $bh, $bl, $high, $low, $mask, $p00, $p01, $p10, $p11) | 0;
		$high = $high + haxe_Int64_6($a, $ah, $al, $b, $bh, $bl, $high, $low, $mask, $p00, $p01, $p10, $p11) | 0;
		return new haxe_Int64($high, $low);
	}
	static function divMod($modulus, $divisor) {
		$quotient = new haxe_Int64(0 | 0, 0 | 0);
		$mask = new haxe_Int64(0 | 0, 1 | 0);
		$divisor = new haxe_Int64($divisor->high, $divisor->low);
		while(!($divisor->high < 0)) {
			$cmp = haxe_Int64_7($divisor, $mask, $modulus, $quotient);
			$divisor->high = $divisor->high << 1 | _hx_shift_right($divisor->low, 31);
			$divisor->low = $divisor->low << 1;
			$mask->high = $mask->high << 1 | _hx_shift_right($mask->low, 31);
			$mask->low = $mask->low << 1;
			if($cmp >= 0) {
				break;
			}
			unset($cmp);
		}
		while(!(($mask->low | $mask->high) === 0)) {
			if(haxe_Int64_8($divisor, $mask, $modulus, $quotient) >= 0) {
				$quotient->high = $quotient->high | $mask->high;
				$quotient->low = $quotient->low | $mask->low;
				$modulus = haxe_Int64::sub($modulus, $divisor);
			}
			$mask->low = _hx_shift_right($mask->low, 1) | $mask->high << 31;
			$mask->high = _hx_shift_right($mask->high, 1);
			$divisor->low = _hx_shift_right($divisor->low, 1) | $divisor->high << 31;
			$divisor->high = _hx_shift_right($divisor->high, 1);
		}
		return _hx_anonymous(array("quotient" => $quotient, "modulus" => $modulus));
	}
	static function div($a, $b) {
		$sign = ($a->high | $b->high) < 0;
		if($a->high < 0) {
			$a = haxe_Int64_9($a, $b, $sign);
		}
		if($b->high < 0) {
			$b = haxe_Int64_10($a, $b, $sign);
		}
		$q = haxe_Int64::divMod($a, $b)->quotient;
		return haxe_Int64_11($a, $b, $q, $sign);
	}
	static function mod($a, $b) {
		$sign = ($a->high | $b->high) < 0;
		if($a->high < 0) {
			$a = haxe_Int64_12($a, $b, $sign);
		}
		if($b->high < 0) {
			$b = haxe_Int64_13($a, $b, $sign);
		}
		$m = haxe_Int64::divMod($a, $b)->modulus;
		return haxe_Int64_14($a, $b, $m, $sign);
	}
	static function shl($a, $b) {
		return ((($b & 63) === 0) ? $a : ((($b & 63) < 32) ? new haxe_Int64($a->high << $b | _hx_shift_right($a->low, 32 - ($b & 63)), $a->low << $b) : new haxe_Int64($a->low << $b - 32, 0 | 0)));
	}
	static function shr($a, $b) {
		return ((($b & 63) === 0) ? $a : ((($b & 63) < 32) ? new haxe_Int64($a->high >> $b, _hx_shift_right($a->low, $b) | $a->high << 32 - ($b & 63)) : new haxe_Int64($a->high >> 31, $a->high >> $b - 32)));
	}
	static function ushr($a, $b) {
		return ((($b & 63) === 0) ? $a : ((($b & 63) < 32) ? new haxe_Int64(_hx_shift_right($a->high, $b), _hx_shift_right($a->low, $b) | $a->high << 32 - ($b & 63)) : new haxe_Int64(0 | 0, _hx_shift_right($a->high, $b - 32))));
	}
	static function hand($a, $b) {
		return new haxe_Int64($a->high & $b->high, $a->low & $b->low);
	}
	static function hor($a, $b) {
		return new haxe_Int64($a->high | $b->high, $a->low | $b->low);
	}
	static function hxor($a, $b) {
		return new haxe_Int64($a->high ^ $b->high, $a->low ^ $b->low);
	}
	static function neg($a) {
		$high = ~$a->high;
		$low = -$a->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
	static function isNeg($a) {
		return $a->high < 0;
	}
	static function isZero($a) {
		return ($a->high | $a->low) === 0;
	}
	static function compare($a, $b) {
		$v = $a->high - $b->high;
		return (($v !== 0) ? $v : haxe_Int32::ucompare($a->low, $b->low));
	}
	static function ucompare($a, $b) {
		$v = haxe_Int32::ucompare($a->high, $b->high);
		return (($v !== 0) ? $v : haxe_Int32::ucompare($a->low, $b->low));
	}
	static function toStr($a) {
		return $a->toString();
	}
	function __toString() { return $this->toString(); }
}
function haxe_Int64_0(&$»this, &$i, &$neg, &$str) {
	{
		$high = ~$i->high;
		$low = -$i->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_1(&$»this, &$i, &$neg, &$r, &$str, &$ten) {
	{
		$x = $r->modulus->low;
		if(($x >> 30 & 1) !== _hx_shift_right($x, 31)) {
			throw new HException("Overflow " . Std::string($x));
		}
		return $x & -1;
	}
}
function haxe_Int64_2(&$x) {
	{
		$x1 = $x->high;
		if(($x1 >> 30 & 1) !== _hx_shift_right($x1, 31)) {
			throw new HException("Overflow " . Std::string($x1));
		}
		return $x1 & -1;
	}
}
function haxe_Int64_3(&$x) {
	{
		$high = ~$x->high;
		$low = -$x->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_4(&$x) {
	{
		$x1 = $x->low;
		if(($x1 >> 30 & 1) !== _hx_shift_right($x1, 31)) {
			throw new HException("Overflow " . Std::string($x1));
		}
		return $x1 & -1;
	}
}
function haxe_Int64_5(&$a, &$ah, &$al, &$b, &$bh, &$bl, &$high, &$low, &$mask, &$p00, &$p01, &$p10, &$p11) {
	{
		$a1 = $a->low; $b1 = $b->high;
		return $a1 * ($b1 & 65535) + ($a1 * (_hx_shift_right($b1, 16)) << 16 | 0) | 0;
	}
}
function haxe_Int64_6(&$a, &$ah, &$al, &$b, &$bh, &$bl, &$high, &$low, &$mask, &$p00, &$p01, &$p10, &$p11) {
	{
		$a1 = $a->high; $b1 = $b->low;
		return $a1 * ($b1 & 65535) + ($a1 * (_hx_shift_right($b1, 16)) << 16 | 0) | 0;
	}
}
function haxe_Int64_7(&$divisor, &$mask, &$modulus, &$quotient) {
	{
		$v = haxe_Int32::ucompare($divisor->high, $modulus->high);
		if($v !== 0) {
			return $v;
		} else {
			return haxe_Int32::ucompare($divisor->low, $modulus->low);
		}
		unset($v);
	}
}
function haxe_Int64_8(&$divisor, &$mask, &$modulus, &$quotient) {
	{
		$v = haxe_Int32::ucompare($modulus->high, $divisor->high);
		if($v !== 0) {
			return $v;
		} else {
			return haxe_Int32::ucompare($modulus->low, $divisor->low);
		}
		unset($v);
	}
}
function haxe_Int64_9(&$a, &$b, &$sign) {
	{
		$high = ~$a->high;
		$low = -$a->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_10(&$a, &$b, &$sign) {
	{
		$high = ~$b->high;
		$low = -$b->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_11(&$a, &$b, &$q, &$sign) {
	if($sign) {
		$high = ~$q->high;
		$low = -$q->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	} else {
		return $q;
	}
}
function haxe_Int64_12(&$a, &$b, &$sign) {
	{
		$high = ~$a->high;
		$low = -$a->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_13(&$a, &$b, &$sign) {
	{
		$high = ~$b->high;
		$low = -$b->low;
		if($low === 0) {
			$high = $high + (1 | 0) | 0;
		}
		return new haxe_Int64($high, $low);
	}
}
function haxe_Int64_14(&$a, &$b, &$m, &$sign) {
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
}

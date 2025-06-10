<?php

class haxe_SHA1 {
	public function __construct(){}
	static $hex_chr = "0123456789abcdef";
	static function newInt32($left, $right) {
		$result = $left | 0;
		$result = $result << 16;
		return $result + ($right | 0) | 0;
	}
	static function encode($s) {
		$x = haxe_SHA1::str2blks_SHA1($s);
		$w = new _hx_array(array());
		$a = haxe_SHA1_0($s, $w, $x);
		$b = haxe_SHA1_1($a, $s, $w, $x);
		$c = haxe_SHA1_2($a, $b, $s, $w, $x);
		$d = haxe_SHA1_3($a, $b, $c, $s, $w, $x);
		$e = haxe_SHA1_4($a, $b, $c, $d, $s, $w, $x);
		$i = 0;
		while($i < $x->length) {
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$olde = $e;
			$j = 0;
			while($j < 80) {
				if($j < 16) {
					$w[$j] = $x[$i + $j];
				} else {
					$w[$j] = haxe_SHA1::rol($w[$j - 3] ^ $w[$j - 8] ^ $w[$j - 14] ^ $w[$j - 16], 1);
				}
				$t = haxe_SHA1::add(haxe_SHA1::add(haxe_SHA1::rol($a, 5), haxe_SHA1::ft($j | 0, $b, $c, $d)), haxe_SHA1::add(haxe_SHA1::add($e, $w[$j]), haxe_SHA1::kt($j | 0)));
				$e = $d;
				$d = $c;
				$c = haxe_SHA1::rol($b, 30);
				$b = $a;
				$a = $t;
				$j++;
				unset($t);
			}
			$a = haxe_SHA1::add($a, $olda);
			$b = haxe_SHA1::add($b, $oldb);
			$c = haxe_SHA1::add($c, $oldc);
			$d = haxe_SHA1::add($d, $oldd);
			$e = haxe_SHA1::add($e, $olde);
			$i += 16;
			unset($olde,$oldd,$oldc,$oldb,$olda,$j);
		}
		return haxe_SHA1::hex($a) . haxe_SHA1::hex($b) . haxe_SHA1::hex($c) . haxe_SHA1::hex($d) . haxe_SHA1::hex($e);
	}
	static function hex($num) {
		$str = "";
		$j = 7;
		while($j >= 0) {
			$str .= _hx_char_at(haxe_SHA1::$hex_chr, haxe_SHA1_5($j, $num, $str));
			$j--;
		}
		return $str;
	}
	static function str2blks_SHA1($s) {
		$nblk = (strlen($s) + 8 >> 6) + 1;
		$blks = new _hx_array(array());
		{
			$_g1 = 0; $_g = $nblk * 16;
			while($_g1 < $_g) {
				$i = $_g1++;
				$blks[$i] = 0 | 0;
				unset($i);
			}
		}
		{
			$_g1 = 0; $_g = strlen($s);
			while($_g1 < $_g) {
				$i = $_g1++;
				$p = $i >> 2;
				$c = _hx_char_code_at($s, $i) | 0;
				$blks[$p] = $blks[$p] | $c << 24 - _hx_mod($i, 4) * 8;
				unset($p,$i,$c);
			}
		}
		$i = strlen($s);
		$p = $i >> 2;
		$blks[$p] = $blks[$p] | (128 | 0) << 24 - _hx_mod($i, 4) * 8;
		$blks[$nblk * 16 - 1] = strlen($s) * 8 | 0;
		return $blks;
	}
	static function add($x, $y) {
		$lsw = ($x & (65535 | 0)) + ($y & (65535 | 0)) | 0;
		$msw = (($x >> 16) + ($y >> 16) | 0) + ($lsw >> 16) | 0;
		return $msw << 16 | $lsw & (65535 | 0);
	}
	static function rol($num, $cnt) {
		return $num << $cnt | _hx_shift_right($num, 32 - $cnt);
	}
	static function ft($t, $b, $c, $d) {
		if($t - (20 | 0) < 0) {
			return $b & $c | ~$b & $d;
		}
		if($t - (40 | 0) < 0) {
			return $b ^ $c ^ $d;
		}
		if($t - (60 | 0) < 0) {
			return $b & $c | $b & $d | $c & $d;
		}
		return $b ^ $c ^ $d;
	}
	static function kt($t) {
		if($t - (20 | 0) < 0) {
			return haxe_SHA1_6($t);
		}
		if($t - (40 | 0) < 0) {
			return haxe_SHA1_7($t);
		}
		if($t - (60 | 0) < 0) {
			return haxe_SHA1_8($t);
		}
		return haxe_SHA1_9($t);
	}
	function __toString() { return 'haxe.SHA1'; }
}
function haxe_SHA1_0(&$s, &$w, &$x) {
	{
		$result = 26437 | 0;
		$result = $result << 16;
		return $result + (8961 | 0) | 0;
	}
}
function haxe_SHA1_1(&$a, &$s, &$w, &$x) {
	{
		$result = 61389 | 0;
		$result = $result << 16;
		return $result + (43913 | 0) | 0;
	}
}
function haxe_SHA1_2(&$a, &$b, &$s, &$w, &$x) {
	{
		$result = 39098 | 0;
		$result = $result << 16;
		return $result + (56574 | 0) | 0;
	}
}
function haxe_SHA1_3(&$a, &$b, &$c, &$s, &$w, &$x) {
	{
		$result = 4146 | 0;
		$result = $result << 16;
		return $result + (21622 | 0) | 0;
	}
}
function haxe_SHA1_4(&$a, &$b, &$c, &$d, &$s, &$w, &$x) {
	{
		$result = 50130 | 0;
		$result = $result << 16;
		return $result + (57840 | 0) | 0;
	}
}
function haxe_SHA1_5(&$j, &$num, &$str) {
	{
		$x = $num >> $j * 4 & (15 | 0);
		if(($x >> 30 & 1) !== _hx_shift_right($x, 31)) {
			throw new HException("Overflow " . Std::string($x));
		}
		return $x & -1;
	}
}
function haxe_SHA1_6(&$t) {
	{
		$result = 23170 | 0;
		$result = $result << 16;
		return $result + (31129 | 0) | 0;
	}
}
function haxe_SHA1_7(&$t) {
	{
		$result = 28377 | 0;
		$result = $result << 16;
		return $result + (60321 | 0) | 0;
	}
}
function haxe_SHA1_8(&$t) {
	{
		$result = 36635 | 0;
		$result = $result << 16;
		return $result + (48348 | 0) | 0;
	}
}
function haxe_SHA1_9(&$t) {
	{
		$result = 51810 | 0;
		$result = $result << 16;
		return $result + (49622 | 0) | 0;
	}
}

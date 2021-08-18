<?php

class haxe_BaseCode {
	public function __construct($base) {
		if(!php_Boot::$skip_constructor) {
		$len = $base->length;
		$nbits = 1;
		while($len > 1 << $nbits) {
			$nbits++;
		}
		if($nbits > 8 || $len !== 1 << $nbits) {
			throw new HException("BaseCode : base length must be a power of two.");
		}
		$this->base = $base;
		$this->nbits = $nbits;
	}}
	public function decodeString($s) {
		return $this->decodeBytes(haxe_io_Bytes::ofString($s))->toString();
	}
	public function encodeString($s) {
		return $this->encodeBytes(haxe_io_Bytes::ofString($s))->toString();
	}
	public function decodeBytes($b) {
		$nbits = $this->nbits;
		$base = $this->base;
		if($this->tbl === null) {
			$this->initTable();
		}
		$tbl = $this->tbl;
		$size = $b->length * $nbits >> 3;
		$out = haxe_io_Bytes::alloc($size);
		$buf = 0;
		$curbits = 0;
		$pin = 0;
		$pout = 0;
		while($pout < $size) {
			while($curbits < 8) {
				$curbits += $nbits;
				$buf <<= $nbits;
				$i = $tbl[ord($b->b[$pin++])];
				if($i === -1) {
					throw new HException("BaseCode : invalid encoded char");
				}
				$buf |= $i;
				unset($i);
			}
			$curbits -= 8;
			$out->b[$pout++] = chr($buf >> $curbits & 255);
		}
		return $out;
	}
	public function initTable() {
		$tbl = new _hx_array(array());
		{
			$_g = 0;
			while($_g < 256) {
				$i = $_g++;
				$tbl[$i] = -1;
				unset($i);
			}
		}
		{
			$_g1 = 0; $_g = $this->base->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$tbl[ord($this->base->b[$i])] = $i;
				unset($i);
			}
		}
		$this->tbl = $tbl;
	}
	public function encodeBytes($b) {
		$nbits = $this->nbits;
		$base = $this->base;
		$size = intval($b->length * 8 / $nbits);
		$out = haxe_io_Bytes::alloc($size + (((_hx_mod($b->length * 8, $nbits) === 0) ? 0 : 1)));
		$buf = 0;
		$curbits = 0;
		$mask = (1 << $nbits) - 1;
		$pin = 0;
		$pout = 0;
		while($pout < $size) {
			while($curbits < $nbits) {
				$curbits += 8;
				$buf <<= 8;
				$buf |= ord($b->b[$pin++]);
			}
			$curbits -= $nbits;
			$out->b[$pout++] = chr(ord($base->b[$buf >> $curbits & $mask]));
		}
		if($curbits > 0) {
			$out->b[$pout++] = chr(ord($base->b[$buf << $nbits - $curbits & $mask]));
		}
		return $out;
	}
	public $tbl;
	public $nbits;
	public $base;
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
	static function encode($s, $base) {
		$b = new haxe_BaseCode(haxe_io_Bytes::ofString($base));
		return $b->encodeString($s);
	}
	static function decode($s, $base) {
		$b = new haxe_BaseCode(haxe_io_Bytes::ofString($base));
		return $b->decodeString($s);
	}
	function __toString() { return 'haxe.BaseCode'; }
}

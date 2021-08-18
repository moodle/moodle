<?php

class haxe_io_Input {
	public function __construct(){}
	public function readString($len) {
		$b = haxe_io_Bytes::alloc($len);
		$this->readFullBytes($b, 0, $len);
		return $b->toString();
	}
	public function readInt32() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$ch3 = $this->readByte();
		$ch4 = $this->readByte();
		$i = haxe_io_Input_0($this, $ch1, $ch2, $ch3, $ch4);
		if($i > 2147483647) {
			$i -= 0x100000000;
		}
		return $i | 0;
	}
	public function readUInt30() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$ch3 = $this->readByte();
		$ch4 = $this->readByte();
		if(((($this->bigEndian) ? $ch1 : $ch4)) >= 64) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		return haxe_io_Input_1($this, $ch1, $ch2, $ch3, $ch4);
	}
	public function readInt31() {
		$ch1 = null; $ch2 = null; $ch3 = null; $ch4 = null;
		if($this->bigEndian) {
			$ch4 = $this->readByte();
			$ch3 = $this->readByte();
			$ch2 = $this->readByte();
			$ch1 = $this->readByte();
		} else {
			$ch1 = $this->readByte();
			$ch2 = $this->readByte();
			$ch3 = $this->readByte();
			$ch4 = $this->readByte();
		}
		if((($ch4 & 128) === 0) != (($ch4 & 64) === 0)) {
			throw new HException(haxe_io_Error::$Overflow);
		}
		return $ch1 | $ch2 << 8 | $ch3 << 16 | $ch4 << 24;
	}
	public function readUInt24() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$ch3 = $this->readByte();
		return haxe_io_Input_2($this, $ch1, $ch2, $ch3);
	}
	public function readInt24() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$ch3 = $this->readByte();
		$n = haxe_io_Input_3($this, $ch1, $ch2, $ch3);
		if(($n & 8388608) !== 0) {
			return $n - 16777216;
		}
		return $n;
	}
	public function readUInt16() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		return haxe_io_Input_4($this, $ch1, $ch2);
	}
	public function readInt16() {
		$ch1 = $this->readByte();
		$ch2 = $this->readByte();
		$n = haxe_io_Input_5($this, $ch1, $ch2);
		if(($n & 32768) !== 0) {
			return $n - 65536;
		}
		return $n;
	}
	public function readInt8() {
		$n = $this->readByte();
		if($n >= 128) {
			return $n - 256;
		}
		return $n;
	}
	public function readDouble() {
		$a = unpack("d", $this->readString(8));
		return $a[1];
	}
	public function readFloat() {
		$a = unpack("f", $this->readString(4));
		return $a[1];
	}
	public function readLine() {
		$buf = new StringBuf();
		$last = null;
		$s = null;
		try {
			while(($last = $this->readByte()) !== 10) {
				$buf->b .= chr($last);
			}
			$s = $buf->b;
			if(_hx_char_code_at($s, strlen($s) - 1) === 13) {
				$s = _hx_substr($s, 0, -1);
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(($e = $_ex_) instanceof haxe_io_Eof){
				$s = $buf->b;
				if(strlen($s) === 0) {
					throw new HException($e);
				}
			} else throw $»e;;
		}
		return $s;
	}
	public function readUntil($end) {
		$buf = new StringBuf();
		$last = null;
		while(($last = $this->readByte()) !== $end) {
			$buf->b .= chr($last);
		}
		return $buf->b;
	}
	public function read($nbytes) {
		$s = haxe_io_Bytes::alloc($nbytes);
		$p = 0;
		while($nbytes > 0) {
			$k = $this->readBytes($s, $p, $nbytes);
			if($k === 0) {
				throw new HException(haxe_io_Error::$Blocked);
			}
			$p += $k;
			$nbytes -= $k;
			unset($k);
		}
		return $s;
	}
	public function readFullBytes($s, $pos, $len) {
		while($len > 0) {
			$k = $this->readBytes($s, $pos, $len);
			$pos += $k;
			$len -= $k;
			unset($k);
		}
	}
	public function readAll($bufsize = null) {
		if($bufsize === null) {
			$bufsize = 8192;
		}
		$buf = haxe_io_Bytes::alloc($bufsize);
		$total = new haxe_io_BytesBuffer();
		try {
			while(true) {
				$len = $this->readBytes($buf, 0, $bufsize);
				if($len === 0) {
					throw new HException(haxe_io_Error::$Blocked);
				}
				{
					if($len < 0 || $len > $buf->length) {
						throw new HException(haxe_io_Error::$OutsideBounds);
					}
					$total->b .= substr($buf->b, 0, $len);
				}
				unset($len);
			}
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			if(($e = $_ex_) instanceof haxe_io_Eof){
			} else throw $»e;;
		}
		return $total->getBytes();
	}
	public function setEndian($b) {
		$this->bigEndian = $b;
		return $b;
	}
	public function close() {
	}
	public function readBytes($s, $pos, $len) {
		$k = $len;
		$b = $s->b;
		if($pos < 0 || $len < 0 || $pos + $len > $s->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		while($k > 0) {
			$b[$pos] = chr($this->readByte());
			$pos++;
			$k--;
		}
		return $len;
	}
	public function readByte() {
		haxe_io_Input_6($this);
	}
	public $bigEndian;
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
	static $__properties__ = array("set_bigEndian" => "setEndian");
	function __toString() { return 'haxe.io.Input'; }
}
function haxe_io_Input_0(&$»this, &$ch1, &$ch2, &$ch3, &$ch4) {
	if($»this->bigEndian) {
		return ($ch1 << 8 | $ch2) << 16 | ($ch3 << 8 | $ch4);
	} else {
		return ($ch4 << 8 | $ch3) << 16 | ($ch2 << 8 | $ch1);
	}
}
function haxe_io_Input_1(&$»this, &$ch1, &$ch2, &$ch3, &$ch4) {
	if($»this->bigEndian) {
		return $ch4 | $ch3 << 8 | $ch2 << 16 | $ch1 << 24;
	} else {
		return $ch1 | $ch2 << 8 | $ch3 << 16 | $ch4 << 24;
	}
}
function haxe_io_Input_2(&$»this, &$ch1, &$ch2, &$ch3) {
	if($»this->bigEndian) {
		return $ch3 | $ch2 << 8 | $ch1 << 16;
	} else {
		return $ch1 | $ch2 << 8 | $ch3 << 16;
	}
}
function haxe_io_Input_3(&$»this, &$ch1, &$ch2, &$ch3) {
	if($»this->bigEndian) {
		return $ch3 | $ch2 << 8 | $ch1 << 16;
	} else {
		return $ch1 | $ch2 << 8 | $ch3 << 16;
	}
}
function haxe_io_Input_4(&$»this, &$ch1, &$ch2) {
	if($»this->bigEndian) {
		return $ch2 | $ch1 << 8;
	} else {
		return $ch1 | $ch2 << 8;
	}
}
function haxe_io_Input_5(&$»this, &$ch1, &$ch2) {
	if($»this->bigEndian) {
		return $ch2 | $ch1 << 8;
	} else {
		return $ch1 | $ch2 << 8;
	}
}
function haxe_io_Input_6(&$»this) {
	throw new HException("Not implemented");
}

<?php

class com_wiris_system__Utf8_StringIterator {
	public function __construct($s) {
		if(!php_Boot::$skip_constructor) {
		$this->source = $s;
		$this->n = strlen($this->source);
		$this->offset = 0;
	}}
	public function next() {
		$c = ord($this->source[$this->offset++]);
		if(($c & 128) !== 0) {
			$c2 = ord($this->source[$this->offset++]);
			if(($c & 32) !== 0) {
				$c3 = ord($this->source[$this->offset++]);
				if(($c & 16) !== 0) {
					$c4 = ord($this->source[$this->offset++]);
					$c = ($c & 7) << 18 | ($c2 & 63) << 12 | ($c3 & 63) << 6 | $c4 & 63;
				} else {
					$c = ($c & 15) << 12 | ($c2 & 63) << 6 | $c3 & 63;
				}
			} else {
				$c = ($c & 31) << 6 | $c2 & 63;
			}
		}
		return $c;
	}
	public function nextByte() {
		return ord($this->source[$this->offset++]);
	}
	public function hasNext() {
		return $this->offset < $this->n;
	}
	public $source;
	public $n;
	public $offset;
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
	function __toString() { return 'com.wiris.system._Utf8.StringIterator'; }
}

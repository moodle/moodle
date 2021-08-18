<?php

class StringBuf {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->b = "";
	}}
	public function toString() {
		return $this->b;
	}
	public function addChar($c) {
		$this->b .= chr($c);
	}
	public function addSub($s, $pos, $len = null) {
		$this->b .= _hx_substr($s, $pos, $len);
	}
	public function add($x) {
		if(is_null($x)) {
			$x = "null";
		} else {
			if(is_bool($x)) {
				$x = (($x) ? "true" : "false");
			}
		}
		$this->b .= Std::string($x);
	}
	public $b;
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
	function __toString() { return $this->toString(); }
}

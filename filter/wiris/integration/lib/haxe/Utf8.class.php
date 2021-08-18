<?php

class haxe_Utf8 {
	public function __construct($size = null) {
		if(!php_Boot::$skip_constructor) {
		$this->__b = "";
	}}
	public function toString() {
		return $this->__b;
	}
	public function addChar($c) {
		$this->__b .= haxe_Utf8::uchr($c);
	}
	public $__b;
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
	static function encode($s) {
		return utf8_encode($s);
	}
	static function decode($s) {
		return utf8_decode($s);
	}
	static function iter($s, $chars) {
		$len = haxe_Utf8::length($s);
		{
			$_g = 0;
			while($_g < $len) {
				$i = $_g++;
				call_user_func_array($chars, array(haxe_Utf8::charCodeAt($s, $i)));
				unset($i);
			}
		}
	}
	static function charCodeAt($s, $index) {
		return haxe_Utf8::uord(haxe_Utf8::sub($s, $index, 1));
	}
	static function uchr($i) {
		return mb_convert_encoding(pack('N',$i), 'UTF-8', 'UCS-4BE');
	}
	static function uord($s) {
		$c = unpack('N', mb_convert_encoding($s, 'UCS-4BE', 'UTF-8'));
		return $c[1];
	}
	static function validate($s) {
		return mb_check_encoding($s, "UTF-8");
	}
	static function length($s) {
		return mb_strlen($s, "UTF-8");
	}
	static function compare($a, $b) {
		return strcmp($a, $b);
	}
	static function sub($s, $pos, $len) {
		return mb_substr($s, $pos, $len, "UTF-8");
	}
	static $enc = "UTF-8";
	function __toString() { return $this->toString(); }
}

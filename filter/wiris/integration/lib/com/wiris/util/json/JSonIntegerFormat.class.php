<?php

class com_wiris_util_json_JSonIntegerFormat {
	public function __construct($n, $format) {
		if(!php_Boot::$skip_constructor) {
		$this->n = $n;
		$this->format = $format;
	}}
	public function toString() {
		if($this->format === com_wiris_util_json_JSonIntegerFormat::$HEXADECIMAL) {
			return "0x" . StringTools::hex($this->n, 0);
		}
		return "" . _hx_string_rec($this->n, "");
	}
	public $format;
	public $n;
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
	static $HEXADECIMAL = 0;
	function __toString() { return $this->toString(); }
}

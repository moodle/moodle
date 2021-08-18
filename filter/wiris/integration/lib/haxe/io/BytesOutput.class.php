<?php

class haxe_io_BytesOutput extends haxe_io_Output {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->b = new haxe_io_BytesBuffer();
	}}
	public function getBytes() {
		return $this->b->getBytes();
	}
	public function writeBytes($buf, $pos, $len) {
		{
			if($pos < 0 || $len < 0 || $pos + $len > $buf->length) {
				throw new HException(haxe_io_Error::$OutsideBounds);
			}
			$this->b->b .= substr($buf->b, $pos, $len);
		}
		return $len;
	}
	public function writeByte($c) {
		$this->b->b .= chr($c);
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
	static $__properties__ = array("set_bigEndian" => "setEndian");
	function __toString() { return 'haxe.io.BytesOutput'; }
}

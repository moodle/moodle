<?php

class sys_io_FileOutput extends haxe_io_Output {
	public function __construct($f) {
		if(!php_Boot::$skip_constructor) {
		$this->__f = $f;
	}}
	public function tell() {
		$r = ftell($this->__f);
		if(($r === false)) {
			sys_io_FileOutput_0($this, $r);
		}
		return $r;
	}
	public function seek($p, $pos) {
		$w = null;
		$»t = ($pos);
		switch($»t->index) {
		case 0:
		{
			$w = SEEK_SET;
		}break;
		case 1:
		{
			$w = SEEK_CUR;
		}break;
		case 2:
		{
			$w = SEEK_END;
		}break;
		}
		$r = fseek($this->__f, $p, $w);
		if(($r === false)) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
	}
	public function close() {
		parent::close();
		if($this->__f !== null) {
			fclose($this->__f);
		}
	}
	public function flush() {
		$r = fflush($this->__f);
		if(($r === false)) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
	}
	public function writeBytes($b, $p, $l) {
		$s = $b->readString($p, $l);
		if(feof($this->__f)) {
			sys_io_FileOutput_1($this, $b, $l, $p, $s);
		}
		$r = fwrite($this->__f, $s, $l);
		if(($r === false)) {
			sys_io_FileOutput_2($this, $b, $l, $p, $r, $s);
		}
		return $r;
	}
	public function writeByte($c) {
		$r = fwrite($this->__f, chr($c));
		if(($r === false)) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
	}
	public $__f;
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
	function __toString() { return 'sys.io.FileOutput'; }
}
function sys_io_FileOutput_0(&$»this, &$r) {
	throw new HException(haxe_io_Error::Custom("An error occurred"));
}
function sys_io_FileOutput_1(&$»this, &$b, &$l, &$p, &$s) {
	throw new HException(new haxe_io_Eof());
}
function sys_io_FileOutput_2(&$»this, &$b, &$l, &$p, &$r, &$s) {
	throw new HException(haxe_io_Error::Custom("An error occurred"));
}

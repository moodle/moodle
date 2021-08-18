<?php

class sys_net_Host {
	public function __construct($name) {
		if(!php_Boot::$skip_constructor) {
		$this->hostName = $name;
		if(_hx_deref(new EReg("^(\\d{1,3}\\.){3}\\d{1,3}\$", ""))->match($name)) {
			$this->_ip = $name;
		} else {
			$this->_ip = gethostbyname($name);
			if($this->_ip === $name) {
				$this->ip = 0 | 0;
				return;
			}
		}
		$p = _hx_explode(".", $this->_ip);
		$this->ip = intval(sprintf("%02X%02X%02X%02X", $p[3], $p[2], $p[1], $p[0]), 16) | 0;
	}}
	public function reverse() {
		return gethostbyaddress($this->_ip);
	}
	public function toString() {
		return $this->_ip;
	}
	public $ip;
	public $hostName;
	public $_ip;
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
	static function localhost() {
		return $_SERVER["HTTP_HOST"];
	}
	function __toString() { return $this->toString(); }
}

<?php

class com_wiris_std_system_HttpProxy {
	public function __construct($host, $port) {
		if(!php_Boot::$skip_constructor) {
		$this->port = $port;
		$this->host = $host;
		$this->auth = null;
	}}
	public $auth;
	public $host;
	public $port;
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
	static function newHttpProxy($host, $port, $user, $pass) {
		$proxy = new com_wiris_std_system_HttpProxy($host, $port);
		$hpa = new com_wiris_std_system_HttpProxyAuth();
		$hpa->user = $user;
		$hpa->pass = $pass;
		$proxy->auth = $hpa;
		return $proxy;
	}
	function __toString() { return 'com.wiris.std.system.HttpProxy'; }
}

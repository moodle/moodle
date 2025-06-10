<?php

class com_wiris_util_sys_HttpConnection extends haxe_Http {
	public function __construct($url, $service, $listener) {
		if(!isset($this->onStatus)) $this->onStatus = array(new _hx_lambda(array(&$this, &$listener, &$service, &$url), "com_wiris_util_sys_HttpConnection_0"), 'execute');
		if(!isset($this->onError)) $this->onError = array(new _hx_lambda(array(&$this, &$listener, &$service, &$url), "com_wiris_util_sys_HttpConnection_1"), 'execute');
		if(!isset($this->onData)) $this->onData = array(new _hx_lambda(array(&$this, &$listener, &$service, &$url), "com_wiris_util_sys_HttpConnection_2"), 'execute');
		if(!php_Boot::$skip_constructor) {
		parent::__construct($url);
		$this->listener = $listener;
		$this->service = $service;
	}}
	public function onStatus($status) { return call_user_func_array($this->onStatus, array($status)); }
	public $onStatus = null;
	public function onError($error) { return call_user_func_array($this->onError, array($error)); }
	public $onError = null;
	public function onData($data) { return call_user_func_array($this->onData, array($data)); }
	public $onData = null;
	public $service;
	public $listener;
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
	function __toString() { return 'com.wiris.util.sys.HttpConnection'; }
}
function com_wiris_util_sys_HttpConnection_0(&$»this, &$listener, &$service, &$url, $status) {
	{
		$»this->listener->onHTTPStatus($status, $»this->service);
	}
}
function com_wiris_util_sys_HttpConnection_1(&$»this, &$listener, &$service, &$url, $error) {
	{
		$»this->listener->onHTTPError($error, $»this->service);
	}
}
function com_wiris_util_sys_HttpConnection_2(&$»this, &$listener, &$service, &$url, $data) {
	{
		$»this->listener->onHTTPData($data, $»this->service);
	}
}

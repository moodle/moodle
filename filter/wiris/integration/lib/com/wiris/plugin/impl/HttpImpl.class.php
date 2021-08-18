<?php

class com_wiris_plugin_impl_HttpImpl extends haxe_Http {
	public function __construct($url, $listener) {
		if(!isset($this->onStatus)) $this->onStatus = array(new _hx_lambda(array(&$this, &$listener, &$url), "com_wiris_plugin_impl_HttpImpl_0"), 'execute');
		if(!isset($this->onError)) $this->onError = array(new _hx_lambda(array(&$this, &$listener, &$url), "com_wiris_plugin_impl_HttpImpl_1"), 'execute');
		if(!isset($this->onData)) $this->onData = array(new _hx_lambda(array(&$this, &$listener, &$url), "com_wiris_plugin_impl_HttpImpl_2"), 'execute');
		if(!php_Boot::$skip_constructor) {
		parent::__construct($url);
		$this->listener = $listener;
	}}
	public function onStatus($status) { return call_user_func_array($this->onStatus, array($status)); }
	public $onStatus = null;
	public function setListener($listener) {
		$this->listener = $listener;
	}
	public function setProxy($proxy) {
		_hx_qtype("haxe.Http")->{"PROXY"} = $proxy;
	}
	public function getProxy() {
		$proxy = Reflect::field(_hx_qtype("haxe.Http"), "PROXY");
		if($proxy === null) {
			return null;
		}
		return $proxy;
	}
	public function getData() {
		return $this->data;
	}
	public function onError($msg) { return call_user_func_array($this->onError, array($msg)); }
	public $onError = null;
	public function onData($data) { return call_user_func_array($this->onData, array($data)); }
	public $onData = null;
	public function request($post) {
		$proxy = $this->getProxy();
		if($proxy !== null && $proxy->host !== null && strlen($proxy->host) > 0) {
			$hpa = $proxy->auth;
			if($hpa->user !== null && strlen($hpa->user) > 0) {
				$data = _hx_deref(new com_wiris_system_Base64())->encodeBytes(haxe_io_Bytes::ofString($hpa->user . ":" . $hpa->pass))->toString();
				$this->setHeader("Proxy-Authorization", "Basic " . $data);
			}
		}
		parent::request($post);
	}
	public $listener;
	public $data;
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
	function __toString() { return 'com.wiris.plugin.impl.HttpImpl'; }
}
function com_wiris_plugin_impl_HttpImpl_0(&$»this, &$listener, &$url, $status) {
	{
	}
}
function com_wiris_plugin_impl_HttpImpl_1(&$»this, &$listener, &$url, $msg) {
	{
		if($»this->listener !== null) {
			$»this->listener->onError($msg);
		} else {
			throw new HException($msg);
		}
	}
}
function com_wiris_plugin_impl_HttpImpl_2(&$»this, &$listener, &$url, $data) {
	{
		$»this->data = $data;
		if($»this->listener !== null) {
			$»this->listener->onData($data);
		}
	}
}

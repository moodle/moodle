<?php

class com_wiris_plugin_asyncimpl_HttpPostAndContinue implements com_wiris_plugin_impl_HttpListener{
	public function __construct($h, $obj, $methodName) {
		if(!php_Boot::$skip_constructor) {
		$this->h = $h;
		$this->obj = $obj;
		$this->method = Reflect::field($obj, $methodName);
		if(_hx_field($this, "method") === null) {
			throw new HException("Method not found: " . $methodName);
		}
		$h->setListener($this);
	}}
	public function onError($msg) {
		throw new HException($msg);
	}
	public function onData($data) {
		$args = new _hx_array(array());
		$args->push($data);
		Reflect::callMethod($this->obj, $this->method, $args);
	}
	public function post() {
		$this->h->request(true);
	}
	public $h;
	public $method;
	public $obj;
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
	static function doPost($h, $obj, $methodName) {
		_hx_deref(new com_wiris_plugin_asyncimpl_HttpPostAndContinue($h, $obj, $methodName))->post();
	}
	function __toString() { return 'com.wiris.plugin.asyncimpl.HttpPostAndContinue'; }
}

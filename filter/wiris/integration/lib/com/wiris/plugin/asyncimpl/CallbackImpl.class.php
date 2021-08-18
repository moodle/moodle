<?php

class com_wiris_plugin_asyncimpl_CallbackImpl implements com_wiris_plugin_asyncapi_StringCallback, com_wiris_plugin_asyncapi_BytesCallback{
	public function __construct($obj, $name, $errorName) {
		if(!php_Boot::$skip_constructor) {
		$this->method = Reflect::field($obj, $name);
		if(_hx_field($this, "method") === null) {
			throw new HException("Method not found: " . $name);
		}
		$this->errorMethod = Reflect::field($obj, $errorName);
		if(_hx_field($this, "errorMethod") === null) {
			throw new HException("Method not found: " . $errorName);
		}
		$this->obj = $obj;
	}}
	public function error($msg) {
		$args = new _hx_array(array());
		$args->push($msg);
		Reflect::callMethod($this->obj, $this->errorMethod, $args);
	}
	public function returnString($str) {
		$args = new _hx_array(array());
		$args->push($str);
		Reflect::callMethod($this->obj, $this->method, $args);
	}
	public function returnBytes($bs) {
		$args = new _hx_array(array());
		$args->push($bs);
		Reflect::callMethod($this->obj, $this->method, $args);
	}
	public $obj;
	public $errorMethod;
	public $method;
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
	static function newBytes($obj, $name, $errorName) {
		return new com_wiris_plugin_asyncimpl_CallbackImpl($obj, $name, $errorName);
	}
	static function newString($obj, $name, $errorName) {
		return new com_wiris_plugin_asyncimpl_CallbackImpl($obj, $name, $errorName);
	}
	function __toString() { return 'com.wiris.plugin.asyncimpl.CallbackImpl'; }
}

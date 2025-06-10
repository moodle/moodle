<?php

class com_wiris_quizzes_impl_HttpImpl extends haxe_Http {
	public function __construct($url, $listener) {
		if(!isset($this->onError)) $this->onError = array(new _hx_lambda(array(&$this, &$listener, &$url), "com_wiris_quizzes_impl_HttpImpl_0"), 'execute');
		if(!isset($this->onData)) $this->onData = array(new _hx_lambda(array(&$this, &$listener, &$url), "com_wiris_quizzes_impl_HttpImpl_1"), 'execute');
		if(!php_Boot::$skip_constructor) {
		parent::__construct($url);
		$this->listener = $listener;
		$c = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getConfiguration();
		$host = $c->get(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_HOST);
		$port = Std::parseInt($c->get(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PORT));
		if($host !== null && !($host === "")) {
			haxe_Http::$PROXY = new com_wiris_std_system_HttpProxy($host, $port);
			$user = $c->get(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_USER);
			$pass = $c->get(com_wiris_quizzes_api_ConfigurationKeys::$HTTPPROXY_PASS);
			if($user !== null && !($user === "")) {
				$data = _hx_deref(new com_wiris_quizzes_impl_Base64())->encodeBytes(haxe_io_Bytes::ofString($user . ":" . $pass))->toString();
				$this->setHeader("Proxy-Authorization", "Basic " . $data);
			}
		}
	}}
	public function setAsync($async) {
	}
	public function onError($msg) { return call_user_func_array($this->onError, array($msg)); }
	public $onError = null;
	public function onData($data) { return call_user_func_array($this->onData, array($data)); }
	public $onData = null;
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
	function __toString() { return 'com.wiris.quizzes.impl.HttpImpl'; }
}
function com_wiris_quizzes_impl_HttpImpl_0(&$»this, &$listener, &$url, $msg) {
	{
		$»this->listener->onError($msg);
	}
}
function com_wiris_quizzes_impl_HttpImpl_1(&$»this, &$listener, &$url, $data) {
	{
		$»this->listener->onData($data);
	}
}

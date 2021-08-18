<?php

class com_wiris_system_service_HttpRequest {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->extraParams = new Hash();
		$this->headers = new Hash();
		$this->setHeaders();
	}}
	public function getHeader($key) {
		$httpKey = null;
		$httpKey = "HTTP_" . $this->headers->get($key);
		$header = null;
		$header = isset($_SERVER[$httpKey]) ? $_SERVER[$httpKey] : '';
		return $header;
	}
	public function setParameter($key, $value) {
		$this->extraParams->set($key, $value);
	}
	public function getParameterNames() {
		$param = new _hx_array(array());
		$key = "";
		foreach ($_GET as $key => $value) {
		$param->insert(0, $key);
		}
		foreach ($_POST as $key => $value) {
		$param->insert(0, $key);
		}
		return $param;
	}
	public function getContextURL() {
		return "";
	}
	public function getParameter($key) {
		$param = null;
		if(isset($_POST[$key])) {
			$param = $_POST[$key];
		} else {
			if(isset($_GET[$key])) {
				$param = $_GET[$key];
			} else {
				if($this->extraParams->exists($key)) {
					return $this->extraParams->get($key);
				}
			}
		}
		return $param;
	}
	public function setHeaders() {
		$this->headers->set("User-Agent", "USER_AGENT");
	}
	public $headers;
	public $extraParams;
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
	function __toString() { return 'com.wiris.system.service.HttpRequest'; }
}

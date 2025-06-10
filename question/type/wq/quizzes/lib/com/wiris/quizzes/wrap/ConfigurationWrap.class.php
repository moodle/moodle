<?php

class com_wiris_quizzes_wrap_ConfigurationWrap implements com_wiris_quizzes_api_Configuration{
	public function __construct($config) {
		if(!php_Boot::$skip_constructor) {
		$this->config = $config;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function set($key, $value) {
		try {
			$this->wrapper->start();
			$this->config->set($key, $value);
			$this->wrapper->stop();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function get($key) {
		try {
			$this->wrapper->start();
			$r = $this->config->get($key);
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public $wrapper;
	public $config;
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
	function __toString() { return 'com.wiris.quizzes.wrap.ConfigurationWrap'; }
}

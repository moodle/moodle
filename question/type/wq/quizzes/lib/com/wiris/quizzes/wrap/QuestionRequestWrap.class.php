<?php

class com_wiris_quizzes_wrap_QuestionRequestWrap implements com_wiris_quizzes_api_QuestionRequest{
	public function __construct($impl) {
		if(!php_Boot::$skip_constructor) {
		$this->impl = $impl;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function prefixVariables($prefix, $variablesToPrefix) {
		try {
			$this->wrapper->start();
			$this->impl->prefixVariables($prefix, $variablesToPrefix);
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
	public function serialize() {
		try {
			$this->wrapper->start();
			$r = $this->impl->serialize();
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
	public function isEmpty() {
		try {
			$this->wrapper->start();
			$res = $this->impl->isEmpty();
			$this->wrapper->stop();
			return $res;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function addMetaProperty($name, $value) {
		try {
			$this->wrapper->start();
			$this->impl->addMetaProperty($name, $value);
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
	public $wrapper;
	public $impl;
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
	function __toString() { return 'com.wiris.quizzes.wrap.QuestionRequestWrap'; }
}

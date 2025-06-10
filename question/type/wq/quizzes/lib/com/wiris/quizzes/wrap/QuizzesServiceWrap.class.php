<?php

class com_wiris_quizzes_wrap_QuizzesServiceWrap implements com_wiris_quizzes_api_QuizzesService{
	public function __construct($service) {
		if(!php_Boot::$skip_constructor) {
		$this->service = $service;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function executeAsync($request, $listener) {
		try {
			$this->wrapper->start();
			$rw = $request;
			$request = $rw->impl;
			$this->service->executeAsync($request, $listener);
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
	public function execute($request) {
		try {
			$this->wrapper->start();
			$rw = $request;
			$request = $rw->impl;
			$response = $this->service->execute($request);
			$this->wrapper->stop();
			return $response;
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
	public $service;
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
	function __toString() { return 'com.wiris.quizzes.wrap.QuizzesServiceWrap'; }
}

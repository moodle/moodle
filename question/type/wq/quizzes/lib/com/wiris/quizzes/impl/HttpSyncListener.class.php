<?php

class com_wiris_quizzes_impl_HttpSyncListener implements com_wiris_quizzes_impl_HttpListener{
	public function __construct() {
		;
	}
	public function getData() {
		return $this->data;
	}
	public function onError($error) {
		throw new HException($error);
	}
	public function onData($data) {
		$this->data = $data;
	}
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
	function __toString() { return 'com.wiris.quizzes.impl.HttpSyncListener'; }
}

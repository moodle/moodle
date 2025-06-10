<?php

class com_wiris_quizzes_impl_QuizzesServiceSyncListener implements com_wiris_quizzes_impl_QuizzesServiceMultipleListener{
	public function __construct() {
		;
	}
	public function onResponse($mqs) {
		$this->mqs = $mqs;
	}
	public $mqs;
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
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesServiceSyncListener'; }
}

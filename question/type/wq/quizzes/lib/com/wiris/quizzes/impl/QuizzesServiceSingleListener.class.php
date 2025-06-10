<?php

class com_wiris_quizzes_impl_QuizzesServiceSingleListener implements com_wiris_quizzes_impl_QuizzesServiceMultipleListener{
	public function __construct($listener) {
		if(!php_Boot::$skip_constructor) {
		$this->listener = $listener;
	}}
	public function onResponse($mqs) {
		$qs = null;
		if($mqs->questionResponses->length === 0) {
			$qs = new com_wiris_quizzes_impl_QuestionResponseImpl();
		} else {
			$qs = $mqs->questionResponses[0];
		}
		$this->listener->onResponse($qs);
	}
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
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesServiceSingleListener'; }
}

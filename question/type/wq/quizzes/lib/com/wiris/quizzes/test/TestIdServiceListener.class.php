<?php

class com_wiris_quizzes_test_TestIdServiceListener implements com_wiris_quizzes_api_QuizzesServiceListener{
	public function __construct($id, $tester, $question, $instance) {
		if(!php_Boot::$skip_constructor) {
		$this->tester = $tester;
		$this->id = $id;
		$this->question = $question;
		$this->instance = $instance;
	}}
	public function onResponse($res) {
		$this->tester->onServiceResponse($this->id, $res, $this->question, $this->instance);
	}
	public $instance;
	public $question;
	public $id;
	public $tester;
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
	function __toString() { return 'com.wiris.quizzes.test.TestIdServiceListener'; }
}

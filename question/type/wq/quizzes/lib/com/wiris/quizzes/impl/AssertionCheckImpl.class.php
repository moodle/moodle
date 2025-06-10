<?php

class com_wiris_quizzes_impl_AssertionCheckImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_quizzes_api_AssertionCheck{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function getValue() {
		return $this->value;
	}
	public function getAssertionName() {
		return $this->assertion;
	}
	public function getAnswers() {
		return $this->answer;
	}
	public function getAnswer() {
		return $this->answer[0];
	}
	public function setAnswers($a) {
		$this->answer = $a;
	}
	public function setAnswer($a) {
		$this->setAnswers(new _hx_array(array($a)));
	}
	public function getCorrectAnswers() {
		return $this->correctAnswer;
	}
	public function getCorrectAnswer() {
		return $this->correctAnswer[0];
	}
	public function setCorrectAnswers($ca) {
		$this->correctAnswer = $ca;
	}
	public function setCorrectAnswer($ca) {
		$this->setCorrectAnswers(new _hx_array(array($ca)));
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_AssertionCheckImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_AssertionCheckImpl::$tagName);
		$this->assertion = $s->attributeString("assertion", $this->assertion, null);
		$this->answer = $s->attributeStringArray("answer", $this->answer, new _hx_array(array("0")));
		$this->correctAnswer = $s->attributeStringArray("correctAnswer", $this->correctAnswer, new _hx_array(array("0")));
		$this->value = $s->floatContent($this->value);
		$s->endTag();
	}
	public $correctAnswer;
	public $answer;
	public $assertion;
	public $value;
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
	static $tagName = "check";
	function __toString() { return 'com.wiris.quizzes.impl.AssertionCheckImpl'; }
}

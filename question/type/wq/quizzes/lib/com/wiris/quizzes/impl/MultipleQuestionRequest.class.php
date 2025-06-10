<?php

class com_wiris_quizzes_impl_MultipleQuestionRequest extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_MultipleQuestionRequest();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_MultipleQuestionRequest::$tagName);
		$this->questionRequests = $s->serializeArray($this->questionRequests, com_wiris_quizzes_impl_QuestionRequestImpl::$tagName);
		$s->endTag();
	}
	public $questionRequests;
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
	static $tagName = "processQuestions";
	function __toString() { return 'com.wiris.quizzes.impl.MultipleQuestionRequest'; }
}

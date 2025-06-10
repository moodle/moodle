<?php

class com_wiris_quizzes_impl_MultipleQuestionResponse extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_MultipleQuestionResponse();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_MultipleQuestionResponse::$tagName);
		$this->questionResponses = $s->serializeArray($this->questionResponses, com_wiris_quizzes_impl_QuestionResponseImpl::$tagName);
		$s->endTag();
	}
	public $questionResponses;
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
	static $tagName = "processQuestionsResult";
	function __toString() { return 'com.wiris.quizzes.impl.MultipleQuestionResponse'; }
}

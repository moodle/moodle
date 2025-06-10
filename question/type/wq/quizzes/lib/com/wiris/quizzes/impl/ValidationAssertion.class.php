<?php

class com_wiris_quizzes_impl_ValidationAssertion extends com_wiris_quizzes_impl_Assertion implements com_wiris_quizzes_api_assertion_Validation{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function removeParameter($name) {
		$value = $this->getParameter($name);
		$this->removeParam(com_wiris_quizzes_impl_QuizzesEnumUtils::validationParameterName2String($name));
		return $value;
	}
	public function getParameter($name) {
		return $this->getParam(com_wiris_quizzes_impl_QuizzesEnumUtils::validationParameterName2String($name));
	}
	public function setParameter($name, $value) {
		$this->setParam(com_wiris_quizzes_impl_QuizzesEnumUtils::validationParameterName2String($name), $value);
	}
	public function setName($name) {
		$this->name = com_wiris_quizzes_impl_QuizzesEnumUtils::validationName2String($name);
	}
	public function getName() {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::string2ValidationName($this->name);
	}
	static $TAGNAME = "validationAssertion";
	static function fromAssertion($a) {
		$v = new com_wiris_quizzes_impl_ValidationAssertion();
		$v->correctAnswer = $a->correctAnswer;
		$v->answer = $a->answer;
		$v->parameters = $a->parameters;
		$v->name = com_wiris_quizzes_impl_QuizzesEnumUtils::validationName2String(com_wiris_quizzes_impl_QuizzesEnumUtils::string2ValidationName($a->name));
		return $v;
	}
	function __toString() { return 'com.wiris.quizzes.impl.ValidationAssertion'; }
}

<?php

class com_wiris_quizzes_impl_SyntaxAssertion extends com_wiris_quizzes_impl_Assertion implements com_wiris_quizzes_api_assertion_Syntax{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function removeParameter($name) {
		$value = $this->getParameter($name);
		$this->removeParam(com_wiris_quizzes_impl_QuizzesEnumUtils::syntaxParameterName2String($name));
		return $value;
	}
	public function getParameter($name) {
		return $this->getParam(com_wiris_quizzes_impl_QuizzesEnumUtils::syntaxParameterName2String($name));
	}
	public function setParameter($name, $value) {
		$this->setParam(com_wiris_quizzes_impl_QuizzesEnumUtils::syntaxParameterName2String($name), $value);
	}
	public function setName($name) {
		$this->name = com_wiris_quizzes_impl_QuizzesEnumUtils::syntaxName2String($name);
	}
	public function getName() {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::string2SyntaxName($this->name);
	}
	static $TAGNAME = "syntaxAssertion";
	static function fromAssertion($a) {
		$s = new com_wiris_quizzes_impl_SyntaxAssertion();
		$s->correctAnswer = $a->correctAnswer;
		$s->answer = $a->answer;
		$s->parameters = $a->parameters;
		$s->name = com_wiris_quizzes_impl_QuizzesEnumUtils::syntaxName2String(com_wiris_quizzes_impl_QuizzesEnumUtils::string2SyntaxName($a->name));
		return $s;
	}
	static function getDefaultAnswerFieldType($name) {
		if($name === com_wiris_quizzes_api_assertion_SyntaxName::$MATH) {
			return com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_MATH_EDITOR;
		}
		return com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD;
	}
	static function getDefaultSyntax() {
		$s = new com_wiris_quizzes_impl_SyntaxAssertion();
		$s->setName(com_wiris_quizzes_api_assertion_SyntaxName::$MATH);
		$s->setParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$DECIMAL_SEPARATORS, ".,'");
		return $s;
	}
	function __toString() { return 'com.wiris.quizzes.impl.SyntaxAssertion'; }
}

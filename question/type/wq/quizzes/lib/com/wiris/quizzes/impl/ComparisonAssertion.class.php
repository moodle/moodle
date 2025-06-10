<?php

class com_wiris_quizzes_impl_ComparisonAssertion extends com_wiris_quizzes_impl_Assertion implements com_wiris_quizzes_api_assertion_Comparison{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function removeParameter($name) {
		$value = $this->getParameter($name);
		$this->removeParam(com_wiris_quizzes_impl_QuizzesEnumUtils::comparisonParameterName2String($name));
		return $value;
	}
	public function getParameter($name) {
		return $this->getParam(com_wiris_quizzes_impl_QuizzesEnumUtils::comparisonParameterName2String($name));
	}
	public function setParameter($name, $value) {
		$this->setParam(com_wiris_quizzes_impl_QuizzesEnumUtils::comparisonParameterName2String($name), $value);
	}
	public function getName() {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::string2ComparisonName($this->name);
	}
	public function setName($name) {
		$this->name = com_wiris_quizzes_impl_QuizzesEnumUtils::comparisonName2String($name);
	}
	static $TAGNAME = "comparisonAssertion";
	static $DEFAULT_COMPARISON_MATH;
	static $DEFAULT_COMPARISON_GRAPHIC;
	static $DEFAULT_COMPARISON_STRING;
	static function fromAssertion($a) {
		$c = new com_wiris_quizzes_impl_ComparisonAssertion();
		$c->correctAnswer = $a->correctAnswer;
		$c->answer = $a->answer;
		$c->parameters = $a->parameters;
		$c->name = com_wiris_quizzes_impl_QuizzesEnumUtils::comparisonName2String(com_wiris_quizzes_impl_QuizzesEnumUtils::string2ComparisonName($a->name));
		return $c;
	}
	static function getDefaultComparison($syntaxName) {
		$c = new com_wiris_quizzes_impl_ComparisonAssertion();
		$name = null;
		if($syntaxName === com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC) {
			$name = com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_GRAPHIC;
		} else {
			if($syntaxName === com_wiris_quizzes_api_assertion_SyntaxName::$STRING) {
				$name = com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_STRING;
			} else {
				$name = com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_MATH;
			}
		}
		$c->setName($name);
		return $c;
	}
	function __toString() { return 'com.wiris.quizzes.impl.ComparisonAssertion'; }
}
com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_MATH = com_wiris_quizzes_api_assertion_ComparisonName::$MATHEMATICALLY_EQUAL;
com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_GRAPHIC = com_wiris_quizzes_api_assertion_ComparisonName::$GRAPHICALLY_EQUAL;
com_wiris_quizzes_impl_ComparisonAssertion::$DEFAULT_COMPARISON_STRING = com_wiris_quizzes_api_assertion_ComparisonName::$LITERALLY_EQUAL;

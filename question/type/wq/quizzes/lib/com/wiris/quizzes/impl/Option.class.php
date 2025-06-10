<?php

class com_wiris_quizzes_impl_Option extends com_wiris_quizzes_impl_MathContent {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function onSerialize($s) {
		$s->beginTag("option");
		$this->name = $s->attributeString("name", $this->name, null);
		parent::onSerializeInner($s);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_Option();
	}
	public $name;
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
	static $options;
	function __toString() { return 'com.wiris.quizzes.impl.Option'; }
}
com_wiris_quizzes_impl_Option::$options = new _hx_array(array(com_wiris_quizzes_api_QuizzesConstants::$OPTION_RELATIVE_TOLERANCE, com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, com_wiris_quizzes_api_QuizzesConstants::$OPTION_PRECISION, com_wiris_quizzes_api_QuizzesConstants::$OPTION_TIMES_OPERATOR, com_wiris_quizzes_api_QuizzesConstants::$OPTION_IMAGINARY_UNIT, com_wiris_quizzes_api_QuizzesConstants::$OPTION_EXPONENTIAL_E, com_wiris_quizzes_api_QuizzesConstants::$OPTION_NUMBER_PI, com_wiris_quizzes_api_QuizzesConstants::$OPTION_IMPLICIT_TIMES_OPERATOR, com_wiris_quizzes_api_QuizzesConstants::$OPTION_FLOAT_FORMAT, com_wiris_quizzes_api_QuizzesConstants::$OPTION_DECIMAL_SEPARATOR, com_wiris_quizzes_api_QuizzesConstants::$OPTION_DIGIT_GROUP_SEPARATOR, com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER, com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME, com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE_DIGITS));

<?php

class com_wiris_quizzes_api_QuizzesConstants {
	public function __construct() { 
	}
	static function __meta__() { $»args = func_get_args(); return call_user_func_array(self::$__meta__, $»args); }
	static $__meta__;
	static $OPTION_RELATIVE_TOLERANCE = "relative_tolerance";
	static $OPTION_TOLERANCE = "tolerance";
	static $OPTION_TOLERANCE_DIGITS = "tolerance_digits";
	static $OPTION_PRECISION = "precision";
	static $OPTION_TIMES_OPERATOR = "times_operator";
	static $OPTION_IMAGINARY_UNIT = "imaginary_unit";
	static $OPTION_EXPONENTIAL_E = "exponential_e";
	static $OPTION_NUMBER_PI = "number_pi";
	static $OPTION_IMPLICIT_TIMES_OPERATOR = "implicit_times_operator";
	static $OPTION_FLOAT_FORMAT = "float_format";
	static $OPTION_DECIMAL_SEPARATOR = "decimal_separator";
	static $OPTION_DIGIT_GROUP_SEPARATOR = "digit_group_separator";
	static $OPTION_STUDENT_ANSWER_PARAMETER = "answer_parameter";
	static $OPTION_STUDENT_ANSWER_PARAMETER_NAME = "answer_parameter_name";
	static $PROPERTY_ANSWER_FIELD_TYPE = "inputField";
	static $ANSWER_FIELD_TYPE_INLINE_EDITOR = "inlineEditor";
	static $ANSWER_FIELD_TYPE_POPUP_EDITOR = "popupEditor";
	static $ANSWER_FIELD_TYPE_TEXT = "textField";
	static $META_PROPERTY_REFERER = "referer";
	static $META_PROPERTY_QUESTION = "question";
	static $META_PROPERTY_USER = "userref";
	static $PROPERTY_COMPOUND_ANSWER = "inputCompound";
	static $PROPERTY_VALUE_COMPOUND_ANSWER_TRUE = "true";
	static $PROPERTY_VALUE_COMPOUND_ANSWER_FALSE = "false";
	static $PROPERTY_COMPOUND_ANSWER_GRADE = "gradeCompound";
	static $PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_AND = "and";
	static $PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE = "distribute";
	static $PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION = "gradeCompoundDistribution";
	static $PROPERTY_SHOW_CAS = "cas";
	static $PROPERTY_VALUE_SHOW_CAS_FALSE = "false";
	static $PROPERTY_VALUE_SHOW_CAS_ADD = "add";
	static $PROPERTY_VALUE_SHOW_CAS_REPLACE = "replace";
	static $PROPERTY_CAS_INITIAL_SESSION = "casSession";
	static $PROPERTY_CAS_SESSION = "casSession";
	static $PROPERTY_SHOW_AUXILIAR_TEXT_INPUT = "auxiliaryTextInput";
	static $PROPERTY_SHOW_AUXILIARY_TEXT_INPUT = "auxiliaryTextInput";
	static $PROPERTY_AUXILIAR_TEXT = "auxiliaryText";
	static $PROPERTY_AUXILIARY_TEXT = "auxiliaryText";
	static $PARAMETER_USER_ID = "user_id";
	static $GRAPH_TOOLBAR = "graphToolbar";
	function __toString() { return 'com.wiris.quizzes.api.QuizzesConstants'; }
}
com_wiris_quizzes_api_QuizzesConstants::$__meta__ = _hx_anonymous(array("statics" => _hx_anonymous(array("PROPERTY_AUXILIAR_TEXT" => _hx_anonymous(array("Deprecated" => null))))));

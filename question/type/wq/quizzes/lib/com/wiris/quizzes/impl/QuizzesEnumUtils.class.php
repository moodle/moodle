<?php

class com_wiris_quizzes_impl_QuizzesEnumUtils {
	public function __construct() { 
	}
	static $syntaxNames;
	static $syntaxParameterNames;
	static function syntaxName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getSyntaxNamesHash());
	}
	static function string2SyntaxName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getSyntaxNamesHash()->get($name);
	}
	static function getSyntaxNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames->set(com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH, com_wiris_quizzes_api_assertion_SyntaxName::$MATH);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames->set(com_wiris_quizzes_impl_Assertion::$SYNTAX_GRAPHIC, com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames->set(com_wiris_quizzes_impl_Assertion::$SYNTAX_STRING, com_wiris_quizzes_api_assertion_SyntaxName::$STRING);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames->set(com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH_MULTISTEP, com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxNames;
	}
	static function getSyntaxParameterNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_CONSTANTS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$CONSTANTS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_FUNCTIONS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$FUNCTIONS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_GROUP_OPERATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$GROUP_OPERATORS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_LIST_OPERATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$LIST_OPERATORS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_UNITS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$UNITS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_UNIT_PREFIXES, com_wiris_quizzes_api_assertion_SyntaxParameterName::$UNIT_PREFIXES);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_MIXED_FRACTIONS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$MIXED_FRACTIONS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_ITEM_SEPARATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$ITEM_SEPARATORS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_DECIMAL_SEPARATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$DECIMAL_SEPARATORS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_DIGIT_GROUP_SEPARATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$DIGIT_GROUP_SEPARATORS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_NO_BRACKETS_LIST, com_wiris_quizzes_api_assertion_SyntaxParameterName::$NO_BRACKETS_LIST);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_INTERVALS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$INTERVALS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_RATIO, com_wiris_quizzes_api_assertion_SyntaxParameterName::$RATIO);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_SCIENTIFIC_NOTATION, com_wiris_quizzes_api_assertion_SyntaxParameterName::$SCIENTIFIC_NOTATION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_GRAPH_MODE, com_wiris_quizzes_api_assertion_SyntaxParameterName::$GRAPH_MODE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_TASK_TO_SOLVE, com_wiris_quizzes_api_assertion_SyntaxParameterName::$TASK_TO_SOLVE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_TYPE_OF_TASK, com_wiris_quizzes_api_assertion_SyntaxParameterName::$TYPE_OF_TASK);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_VARIABLE_NAME, com_wiris_quizzes_api_assertion_SyntaxParameterName::$VARIABLE_NAME);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_REF_ID, com_wiris_quizzes_api_assertion_SyntaxParameterName::$REF_ID);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_IMPLICIT_PRODUCT, com_wiris_quizzes_api_assertion_SyntaxParameterName::$IMPLICIT_PRODUCT);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_PRODUCT_OPERATORS, com_wiris_quizzes_api_assertion_SyntaxParameterName::$PRODUCT_OPERATORS);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$syntaxParameterNames;
	}
	static function syntaxParameterName2String($name) {
		$h = com_wiris_quizzes_impl_QuizzesEnumUtils::getSyntaxParameterNamesHash();
		return com_wiris_util_type_HashUtils::getKey($name, $h);
	}
	static function string2SyntaxParameterName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getSyntaxParameterNamesHash()->get($name);
	}
	static $comparisonNames;
	static $comparisonParameterNames;
	static function getComparisonNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, com_wiris_quizzes_api_assertion_ComparisonName::$MATHEMATICALLY_EQUAL);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_LITERAL, com_wiris_quizzes_api_assertion_ComparisonName::$LITERALLY_EQUAL);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_EQUATIONS, com_wiris_quizzes_api_assertion_ComparisonName::$EQUIVALENT_EQUATIONS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_ALL, com_wiris_quizzes_api_assertion_ComparisonName::$ANY_ANSWER);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_GRAPHIC, com_wiris_quizzes_api_assertion_ComparisonName::$GRAPHICALLY_EQUAL);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames->set(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SKETCH, com_wiris_quizzes_api_assertion_ComparisonName::$SKETCH_EQUAL);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonNames;
	}
	static function comparisonName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getComparisonNamesHash());
	}
	static function string2ComparisonName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getComparisonNamesHash()->get($name);
	}
	static function getComparisonParameterNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_ORDER_MATTERS, com_wiris_quizzes_api_assertion_ComparisonParameterName::$ORDER_MATTERS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_REPETITION_MATTERS, com_wiris_quizzes_api_assertion_ComparisonParameterName::$REPETITION_MATTERS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_TOLERANCE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$TOLERANCE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_TOLERANCE_DIGITS, com_wiris_quizzes_api_assertion_ComparisonParameterName::$TOLERANCE_DIGITS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_RELATIVE_TOLERANCE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$RELATIVE_TOLERANCE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_NAME, com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_USE_CASE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$MATCH_CASES);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_USE_SPACES, com_wiris_quizzes_api_assertion_ComparisonParameterName::$MATCH_SPACES);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_ELEMENTS_TO_GRADE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$ELEMENTS_TO_GRADE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_NOT_EVALUATE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$NOT_EVALUATE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_FUNCTION_ARGUMENT_MODE, com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_ARGUMENT_MODE);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$comparisonParameterNames;
	}
	static function comparisonParameterName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getComparisonParameterNamesHash());
	}
	static function string2ComparisonParameterName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getComparisonParameterNamesHash()->get($name);
	}
	static $validationNames;
	static $validationParameterNames;
	static function getValidationNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_SYMBOLIC, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_SYMBOLIC);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_SCIENTIFIC_NOTATION, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_SCIENTIFIC_NOTATION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_DECIMAL_NOTATION, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_DECIMAL_NOTATION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_SIMPLIFIED, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_SIMPLIFIED);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_EXPANDED, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_EXPANDED);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_FACTORIZED, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_FACTORIZED);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_NO_COMMON_FACTOR, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_NO_COMMON_FACTOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_COMMON_DENOMINATOR, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_COMMON_DENOMINATOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_RATIONALIZED, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_RATIONALIZED);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_MINIMAL_RADICANDS, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_MINIMAL_RADICANDS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_EQUIVALENT_UNITS, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_EQUIVALENT_UNITS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_PRECISION, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_PRECISION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_COLOR, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_COLOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_LINESTYLE, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_LINE_STYLE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames->set(com_wiris_quizzes_impl_Assertion::$CHECK_NO_SUPERFLUOUS, com_wiris_quizzes_api_assertion_ValidationName::$CHECK_NO_SUPERFLUOUS);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$validationNames;
	}
	static function validationName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getValidationNamesHash());
	}
	static function string2ValidationName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getValidationNamesHash()->get($name);
	}
	static function getValidationParameterNamesHash() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_MAX, com_wiris_quizzes_api_assertion_ValidationParameterName::$MAX);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_MIN, com_wiris_quizzes_api_assertion_ValidationParameterName::$MIN);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_RELATIVE, com_wiris_quizzes_api_assertion_ValidationParameterName::$RELATIVE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_ALLOW_PREFIXES, com_wiris_quizzes_api_assertion_ValidationParameterName::$ALLOW_PREFIXES);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames->set(com_wiris_quizzes_impl_Assertion::$PARAM_ELEMENTS_TO_GRADE, com_wiris_quizzes_api_assertion_ValidationParameterName::$ELEMENTS_TO_GRADE);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$validationParameterNames;
	}
	static function validationParameterName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getValidationParameterNamesHash());
	}
	static function string2ValidationParameterName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getValidationParameterNamesHash()->get($name);
	}
	static $answerFieldTypes;
	static function getAnswerFieldTypes() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_INLINE_EDITOR, com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_MATH_EDITOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_INLINE_HAND, com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_MATH_EDITOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_POPUP_EDITOR, com_wiris_quizzes_api_ui_AnswerFieldType::$POPUP_MATH_EDITOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_PLAIN_TEXT, com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_INLINE_GRAPH, com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_GRAPH_EDITOR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes->set(com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_MULTISTEP_EDITOR, com_wiris_quizzes_api_ui_AnswerFieldType::$MULTISTEP_MATH_EDITOR);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$answerFieldTypes;
	}
	static function string2answerFieldType($a) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getAnswerFieldTypes()->get($a);
	}
	static function answerFieldType2String($a) {
		return com_wiris_util_type_HashUtils::getKey($a, com_wiris_quizzes_impl_QuizzesEnumUtils::getAnswerFieldTypes());
	}
	static $propertyNames;
	static function getPropertyNames() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER, com_wiris_quizzes_api_PropertyName::$STUDENT_ANSWER_PARAMETER);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME, com_wiris_quizzes_api_PropertyName::$STUDENT_ANSWER_PARAMETER_NAME);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_ANSWER_FIELD_TYPE, com_wiris_quizzes_api_PropertyName::$ANSWER_FIELD_TYPE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER, com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE, com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION, com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE_DISTRIBUTION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_CAS, com_wiris_quizzes_api_PropertyName::$SHOW_CAS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_CAS_SESSION, com_wiris_quizzes_api_PropertyName::$CAS_SESSION);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_SHOW_AUXILIARY_TEXT_INPUT, com_wiris_quizzes_api_PropertyName::$SHOW_AUXILIARY_TEXT_INPUT);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_AUXILIARY_TEXT, com_wiris_quizzes_api_PropertyName::$AUXILIARY_TEXT);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_api_QuizzesConstants::$PARAMETER_USER_ID, com_wiris_quizzes_api_PropertyName::$USER_ID);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS, com_wiris_quizzes_api_PropertyName::$HANDWRITING_CONSTRAINTS);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_GRAPH_TOOLBAR, com_wiris_quizzes_api_PropertyName::$GRAPH_TOOLBAR);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_CAS_HIDE_FILE_MENU, com_wiris_quizzes_api_PropertyName::$AUXILIARY_CAS_HIDE_FILE_MENU);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_ELEMENTS_TO_HANDWRITE, com_wiris_quizzes_api_PropertyName::$ELEMENTS_TO_HANDWRITE);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_LOCK_INITIAL_CONTENT, com_wiris_quizzes_api_PropertyName::$GRAPH_LOCK_INITIAL_CONTENT);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_NAME_IN_LABEL, com_wiris_quizzes_api_PropertyName::$GRAPH_SHOW_NAME_IN_LABEL);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_VALUE_IN_LABEL, com_wiris_quizzes_api_PropertyName::$GRAPH_SHOW_VALUE_IN_LABEL);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_MAGNETIC_GRID, com_wiris_quizzes_api_PropertyName::$GRAPH_MAGNETIC_GRID);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames->set(com_wiris_quizzes_impl_LocalData::$KEY_MULTISTEP_SESSION_ID, com_wiris_quizzes_api_PropertyName::$MULTISTEP_SESSION_ID);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$propertyNames;
	}
	static function string2PropertyName($name) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getPropertyNames()->get($name);
	}
	static function propertyName2String($name) {
		return com_wiris_util_type_HashUtils::getKey($name, com_wiris_quizzes_impl_QuizzesEnumUtils::getPropertyNames());
	}
	static $graphModes;
	static function getGraphModes() {
		if(com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes === null) {
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes = new Hash();
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes->set(com_wiris_quizzes_impl_Assertion::$GRAPH_MODE_STANDARD, com_wiris_util_geometry_GraphMode::$STANDARD);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes->set(com_wiris_quizzes_impl_Assertion::$GRAPH_MODE_SKETCH, com_wiris_util_geometry_GraphMode::$SKETCH);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes->set(com_wiris_quizzes_impl_Assertion::$GRAPH_MODE_PIE_CHART, com_wiris_util_geometry_GraphMode::$PIE_CHART);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes->set(com_wiris_quizzes_impl_Assertion::$GRAPH_MODE_BAR_CHART, com_wiris_util_geometry_GraphMode::$BAR_CHART);
			com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes->set(com_wiris_quizzes_impl_Assertion::$GRAPH_MODE_LINE_CHART, com_wiris_util_geometry_GraphMode::$LINE_CHART);
		}
		return com_wiris_quizzes_impl_QuizzesEnumUtils::$graphModes;
	}
	static function string2GraphMode($mode) {
		return com_wiris_quizzes_impl_QuizzesEnumUtils::getGraphModes()->get($mode);
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesEnumUtils'; }
}

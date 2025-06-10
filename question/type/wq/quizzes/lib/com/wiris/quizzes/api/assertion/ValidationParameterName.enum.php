<?php

class com_wiris_quizzes_api_assertion_ValidationParameterName extends Enum {
	public static $ALLOW_PREFIXES;
	public static $ELEMENTS_TO_GRADE;
	public static $MAX;
	public static $MIN;
	public static $RELATIVE;
	public static $__constructors = array(3 => 'ALLOW_PREFIXES', 4 => 'ELEMENTS_TO_GRADE', 1 => 'MAX', 0 => 'MIN', 2 => 'RELATIVE');
	}
com_wiris_quizzes_api_assertion_ValidationParameterName::$ALLOW_PREFIXES = new com_wiris_quizzes_api_assertion_ValidationParameterName("ALLOW_PREFIXES", 3);
com_wiris_quizzes_api_assertion_ValidationParameterName::$ELEMENTS_TO_GRADE = new com_wiris_quizzes_api_assertion_ValidationParameterName("ELEMENTS_TO_GRADE", 4);
com_wiris_quizzes_api_assertion_ValidationParameterName::$MAX = new com_wiris_quizzes_api_assertion_ValidationParameterName("MAX", 1);
com_wiris_quizzes_api_assertion_ValidationParameterName::$MIN = new com_wiris_quizzes_api_assertion_ValidationParameterName("MIN", 0);
com_wiris_quizzes_api_assertion_ValidationParameterName::$RELATIVE = new com_wiris_quizzes_api_assertion_ValidationParameterName("RELATIVE", 2);

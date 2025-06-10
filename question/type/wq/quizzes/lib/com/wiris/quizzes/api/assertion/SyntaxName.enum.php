<?php

class com_wiris_quizzes_api_assertion_SyntaxName extends Enum {
	public static $GRAPHIC;
	public static $MATH;
	public static $MATH_MULTISTEP;
	public static $STRING;
	public static $__constructors = array(1 => 'GRAPHIC', 0 => 'MATH', 3 => 'MATH_MULTISTEP', 2 => 'STRING');
	}
com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC = new com_wiris_quizzes_api_assertion_SyntaxName("GRAPHIC", 1);
com_wiris_quizzes_api_assertion_SyntaxName::$MATH = new com_wiris_quizzes_api_assertion_SyntaxName("MATH", 0);
com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP = new com_wiris_quizzes_api_assertion_SyntaxName("MATH_MULTISTEP", 3);
com_wiris_quizzes_api_assertion_SyntaxName::$STRING = new com_wiris_quizzes_api_assertion_SyntaxName("STRING", 2);

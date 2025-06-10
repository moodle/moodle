<?php

class com_wiris_quizzes_api_ui_AuthoringFieldType extends Enum {
	public static $EMBEDDED_ANSWERS_EDITOR;
	public static $INLINE_MATH_EDITOR;
	public static $INLINE_STUDIO;
	public static $STUDIO;
	public static $__constructors = array(3 => 'EMBEDDED_ANSWERS_EDITOR', 2 => 'INLINE_MATH_EDITOR', 1 => 'INLINE_STUDIO', 0 => 'STUDIO');
	}
com_wiris_quizzes_api_ui_AuthoringFieldType::$EMBEDDED_ANSWERS_EDITOR = new com_wiris_quizzes_api_ui_AuthoringFieldType("EMBEDDED_ANSWERS_EDITOR", 3);
com_wiris_quizzes_api_ui_AuthoringFieldType::$INLINE_MATH_EDITOR = new com_wiris_quizzes_api_ui_AuthoringFieldType("INLINE_MATH_EDITOR", 2);
com_wiris_quizzes_api_ui_AuthoringFieldType::$INLINE_STUDIO = new com_wiris_quizzes_api_ui_AuthoringFieldType("INLINE_STUDIO", 1);
com_wiris_quizzes_api_ui_AuthoringFieldType::$STUDIO = new com_wiris_quizzes_api_ui_AuthoringFieldType("STUDIO", 0);

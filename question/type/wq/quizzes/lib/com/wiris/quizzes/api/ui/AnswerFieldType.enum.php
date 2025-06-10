<?php

class com_wiris_quizzes_api_ui_AnswerFieldType extends Enum {
	public static $INLINE_GRAPH_EDITOR;
	public static $INLINE_MATH_EDITOR;
	public static $MULTISTEP_MATH_EDITOR;
	public static $POPUP_MATH_EDITOR;
	public static $TEXT_FIELD;
	public static $__constructors = array(3 => 'INLINE_GRAPH_EDITOR', 1 => 'INLINE_MATH_EDITOR', 4 => 'MULTISTEP_MATH_EDITOR', 2 => 'POPUP_MATH_EDITOR', 0 => 'TEXT_FIELD');
	}
com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_GRAPH_EDITOR = new com_wiris_quizzes_api_ui_AnswerFieldType("INLINE_GRAPH_EDITOR", 3);
com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_MATH_EDITOR = new com_wiris_quizzes_api_ui_AnswerFieldType("INLINE_MATH_EDITOR", 1);
com_wiris_quizzes_api_ui_AnswerFieldType::$MULTISTEP_MATH_EDITOR = new com_wiris_quizzes_api_ui_AnswerFieldType("MULTISTEP_MATH_EDITOR", 4);
com_wiris_quizzes_api_ui_AnswerFieldType::$POPUP_MATH_EDITOR = new com_wiris_quizzes_api_ui_AnswerFieldType("POPUP_MATH_EDITOR", 2);
com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD = new com_wiris_quizzes_api_ui_AnswerFieldType("TEXT_FIELD", 0);

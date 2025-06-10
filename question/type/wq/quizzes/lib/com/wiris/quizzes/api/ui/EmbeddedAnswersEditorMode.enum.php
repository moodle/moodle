<?php

class com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode extends Enum {
	public static $AUTHORING;
	public static $DELIVERY;
	public static $REVIEW;
	public static $__constructors = array(0 => 'AUTHORING', 1 => 'DELIVERY', 2 => 'REVIEW');
	}
com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$AUTHORING = new com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode("AUTHORING", 0);
com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$DELIVERY = new com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode("DELIVERY", 1);
com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode::$REVIEW = new com_wiris_quizzes_api_ui_EmbeddedAnswersEditorMode("REVIEW", 2);

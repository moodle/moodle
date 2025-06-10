<?php

interface com_wiris_quizzes_api_ui_AnswerField extends com_wiris_quizzes_api_ui_QuizzesField{
	function reuseComponent($component);
	function getAnswerFieldComponent($listener);
	function setEditorInitialParams($parameters);
	function getFieldType();
}

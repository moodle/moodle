<?php

interface com_wiris_quizzes_api_ui_AuthoringField extends com_wiris_quizzes_api_ui_QuizzesField{
	function showAutomatedStudentGuidance($visible);
	function showAnswerFieldPlainText($visible);
	function showAnswerFieldPopupEditor($visible);
	function showAnswerFieldInlineEditor($visible);
	function showGraphicSyntax($visible);
	function showGradingFunction($visible);
	function showAuxiliaryTextInput($visible);
	function showAuxiliarTextInput($visible);
	function showAuxiliaryCasReplaceEditor($visible);
	function showAuxiliarCasReplaceEditor($visible);
	function showAuxiliaryCas($visible);
	function showAuxiliarCas($visible);
	function showCorrectAnswer($visible);
	function showGradingCriteria($visible);
	function showVariablesDefinition($visible);
	function showPreviewTab($visible);
	function showVariablesTab($visible);
	function showValidationTab($visible);
	function showCorrectAnswerTab($visible);
	function setConfiguration($configuration);
	function getFieldType();
	function setFieldType($type);
	//;
}

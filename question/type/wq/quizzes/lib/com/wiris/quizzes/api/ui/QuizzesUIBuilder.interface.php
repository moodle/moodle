<?php

interface com_wiris_quizzes_api_ui_QuizzesUIBuilder {
	function replaceFields($question, $instance, $element);
	function getMathViewer();
	function newAuxiliarCasField($question, $instance, $index);
	function newEmbeddedAnswersEditor($question, $instance);
	function newAuthoringField($question, $instance, $correctAnswer, $userAnswer);
	function newAnswerField($question, $instance, $index);
	function newAnswerFeedback($question, $instance, $correctAnswer, $studentAnswer);
	function setLanguage($lang);
	//;
}

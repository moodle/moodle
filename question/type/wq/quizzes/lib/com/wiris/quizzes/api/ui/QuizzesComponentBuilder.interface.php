<?php

interface com_wiris_quizzes_api_ui_QuizzesComponentBuilder {
	function replaceFields($question, $instance, $element);
	function getMathViewer();
	function newAuxiliaryCasField($instance, $slot);
	function newEmbeddedAnswersEditor($question, $instance);
	function newAuthoringField($question, $slot, $authorAnswer);
	function newAnswerField($instance, $slot);
	function newAnswerFeedback($instance, $slot, $authorAnswer);
	function setLanguage($lang);
}

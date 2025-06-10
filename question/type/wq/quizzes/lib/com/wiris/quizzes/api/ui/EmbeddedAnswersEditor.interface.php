<?php

interface com_wiris_quizzes_api_ui_EmbeddedAnswersEditor extends com_wiris_quizzes_api_ui_AuthoringField{
	function setEditableElement($element);
	function newEmbeddedAuthoringElement();
	function filterHTML($questionText, $mode);
	function analyzeHTML();
}

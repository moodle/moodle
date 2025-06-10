<?php

interface com_wiris_quizzes_api_ui_AnswerFeedback extends com_wiris_quizzes_api_ui_QuizzesComponent{
	function setAnswerWeight($fraction);
	function showFieldDecorationFeedback($visible);
	function showAssertionsFeedback($visible);
	function showCorrectAnswerFeedback($visible);
	function removeEmbedded($component);
	function setEmbedded($component);
}

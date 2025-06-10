<?php

interface com_wiris_quizzes_api_ui_QuizzesFieldListener {
	function contentChangeStarted($source);
	function contentChanged($source);
}

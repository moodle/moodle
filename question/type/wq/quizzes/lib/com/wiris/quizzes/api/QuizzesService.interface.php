<?php

interface com_wiris_quizzes_api_QuizzesService {
	function executeAsync($request, $listener);
	function execute($request);
}

<?php

interface com_wiris_quizzes_api_ui_MathViewer {
	function filterConstructions($root);
	function filterMathML($root);
	function filter($root);
	function graph($construction, $initialContent = null);
	function thumbnail($construction);
	function plot($construction, $width = null, $height = null);
	function render($mathml);
	function renderCorrectAnswer($instance, $slot, $authorAnswer);
}

<?php

class com_wiris_quizzes_api_Quizzes {
	public function __construct() { 
	}
	public function newFeaturedAssertionsRequest($question) {
		return null;
	}
	public function newFeaturedSyntaxAssertionsRequest($question) {
		return null;
	}
	public function getResourceUrl($name) {
		return null;
	}
	public function getConfiguration() {
		return null;
	}
	public function getMathFilter() {
		return null;
	}
	public function getQuizzesService() {
		return null;
	}
	public function newFeedbackRequest($html, $instance) {
		return null;
	}
	public function newMultipleAnswersGradeRequest($correctAnswers, $studentAnswers) {
		return null;
	}
	public function newSimpleGradeRequest($correctAnswer, $studentAnswer) {
		return null;
	}
	public function newGradeRequest($instance) {
		return null;
	}
	public function newVariablesRequestWithQuestionData($html, $instance) {
		return null;
	}
	public function newVariablesRequest($html, $instance) {
		return null;
	}
	public function readQuestionInstance($xml, $question) {
		return null;
	}
	public function readQuestion($xml) {
		return null;
	}
	public function newQuestionInstance($q) {
		return null;
	}
	public function newQuestion() {
		return null;
	}
	public function getQuizzesComponentBuilder() {
		return null;
	}
	static function getInstance() {
		return com_wiris_quizzes_wrap_QuizzesWrap::getInstance();
	}
	function __toString() { return 'com.wiris.quizzes.api.Quizzes'; }
}

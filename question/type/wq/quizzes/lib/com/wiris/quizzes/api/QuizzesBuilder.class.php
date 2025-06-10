<?php

class com_wiris_quizzes_api_QuizzesBuilder {
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
	public function getQuizzesUIBuilder() {
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
	public function newGradeRequest($question, $instance) {
		return null;
	}
	public function newFeedbackRequest($html, $question, $instance) {
		return null;
	}
	public function newEvalMultipleAnswersRequest($correctAnswer, $studentAnswer, $question, $instance) {
		return null;
	}
	public function newEvalRequest($correctAnswer, $studentAnswer, $question, $instance) {
		return null;
	}
	public function newVariablesRequest($html, $question, $instance) {
		return null;
	}
	public function readQuestionInstance($xml) {
		return null;
	}
	public function readQuestion($xml) {
		return null;
	}
	public function newQuestionInstanceImpl($question) {
		return null;
	}
	public function newQuestionInstance($question = null) {
		return $this->newQuestionInstanceImpl($question);
	}
	public function newQuestion() {
		return null;
	}
	static function getInstance() {
		return com_wiris_quizzes_wrap_QuizzesBuilderWrap::getInstance();
	}
	function __toString() { return 'com.wiris.quizzes.api.QuizzesBuilder'; }
}

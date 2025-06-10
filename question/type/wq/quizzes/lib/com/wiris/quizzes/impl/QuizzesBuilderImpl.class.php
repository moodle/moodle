<?php

class com_wiris_quizzes_impl_QuizzesBuilderImpl extends com_wiris_quizzes_api_QuizzesBuilder {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function setQuestionInInstance($question, $questionInstance) {
		if($questionInstance === null) {
			$questionInstance = new com_wiris_quizzes_impl_QuestionInstanceImpl();
		}
		$questionInstance->question = $question;
		return $questionInstance;
	}
	public function getAccessProvider() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getAccessProvider();
	}
	public function getLockProvider() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getLockProvider();
	}
	public function getVariablesCache() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getVariablesCache();
	}
	public function getImagesCache() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getImagesCache();
	}
	public function getResourceUrl($name) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl($name);
	}
	public function newTranslationRequest($q, $lang) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newTranslationRequest($q, $lang);
	}
	public function newFeedbackRequest($html, $question, $instance) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newFeedbackRequest($html, $this->setQuestionInInstance($question, $instance));
	}
	public function newEvalMultipleAnswersRequest($correctAnswers, $userAnswers, $question, $instance) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newEvalMultipleAnswersRequest($correctAnswers, $userAnswers, $question, $instance);
	}
	public function newEvalRequest($correctAnswer, $userAnswer, $q, $qi) {
		$correctAnswers = (($correctAnswer === null) ? null : new _hx_array(array($correctAnswer)));
		$userAnswers = (($userAnswer === null) ? null : new _hx_array(array($userAnswer)));
		return $this->newEvalMultipleAnswersRequest($correctAnswers, $userAnswers, $q, $qi);
	}
	public function getConfiguration() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration();
	}
	public function newVariablesRequest($html, $question, $instance) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newVariablesRequest($html, $this->setQuestionInInstance($question, $instance));
	}
	public function readQuestionInstance($xml) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->readQuestionInstance($xml, $this->newQuestion());
	}
	public function readQuestion($xml) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->readQuestion($xml);
	}
	public function getMathFilter() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getMathFilter();
	}
	public function getQuizzesService() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getQuizzesService();
	}
	public function newQuestionInstanceImpl($question) {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newQuestionInstance($question);
	}
	public function newQuestion() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newQuestion();
	}
	public function getQuizzesUIBuilder() {
		if($this->uibuilder === null) {
			$this->uibuilder = new com_wiris_quizzes_impl_QuizzesUIBuilderImpl();
		}
		return $this->uibuilder;
	}
	public $uibuilder = null;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function __meta__() { $»args = func_get_args(); return call_user_func_array(self::$__meta__, $»args); }
	static $__meta__;
	static $singleton = null;
	static function getInstance() {
		if(com_wiris_quizzes_impl_QuizzesBuilderImpl::$singleton === null) {
			com_wiris_quizzes_impl_QuizzesBuilderImpl::$singleton = new com_wiris_quizzes_impl_QuizzesBuilderImpl();
		}
		return com_wiris_quizzes_impl_QuizzesBuilderImpl::$singleton;
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesBuilderImpl'; }
}
com_wiris_quizzes_impl_QuizzesBuilderImpl::$__meta__ = _hx_anonymous(array("obj" => _hx_anonymous(array("Deprecated" => null))));

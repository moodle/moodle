<?php

class com_wiris_quizzes_wrap_QuizzesBuilderWrap extends com_wiris_quizzes_api_QuizzesBuilder {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		try {
			$this->wrapper = com_wiris_system_CallWrapper::getInstance();
			$this->wrapper->start();
			$this->builder = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$this->setReferrerPHP();
			$this->wrapper->stop();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}}
	public function setReferrerPHP() {
		$config = $this->builder->getConfiguration();
		$referrer = $config->get(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL);
		if($referrer === null || trim($referrer) === "") {
			if(array_key_exists("REQUEST_METHOD", $_SERVER)) {
				$isHttps = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]!="off";
				$host = $_SERVER["SERVER_NAME"];
				$port = $_SERVER["SERVER_PORT"];
				$path = $_SERVER["SCRIPT_NAME"];
				$query = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : null;
				$referrer = "http";
				if($isHttps) {
					$referrer .= "s";
				}
				$referrer .= "://" . $host;
				if($isHttps && $port !== "443" || !$isHttps && $port !== "80") {
					$referrer .= ":" . $port;
				}
				$referrer .= $path;
				if($query !== null && $query !== "") {
					$referrer .= "?" . $query;
				}
				$config->set(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL, $referrer);
			}
		}
	}
	public function getResourceUrl($name) {
		try {
			$this->wrapper->start();
			$r = $this->builder->getResourceUrl($name);
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function getConfiguration() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_ConfigurationWrap($this->builder->getConfiguration());
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function getMathFilter() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_MathFilterWrap($this->builder->getMathFilter());
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function getQuizzesService() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuizzesServiceWrap($this->builder->getQuizzesService());
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newFeedbackRequest($html, $question, $instance) {
		try {
			$qw = $question;
			$iw = $instance;
			if($qw !== null) {
				$question = $qw->question;
			}
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->builder->newFeedbackRequest($html, $question, $instance));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newEvalMultipleAnswersRequest($correctAnswers, $studentAnswers, $question, $instance) {
		if($correctAnswers !== null && !Std::is($correctAnswers, _hx_qtype("Array"))) {
			$correctAnswers = new _hx_array($correctAnswers);
		}
		if($studentAnswers !== null && !Std::is($correctAnswers, _hx_qtype("Array"))) {
			$studentAnswers = new _hx_array($studentAnswers);
		}
		try {
			$qw = $question;
			$iw = $instance;
			if($qw !== null) {
				$question = $qw->question;
			}
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->builder->newEvalMultipleAnswersRequest($correctAnswers, $studentAnswers, $question, $instance));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newEvalRequest($correctAnswer, $studentAnswer, $question, $instance) {
		try {
			$qw = $question;
			$iw = $instance;
			if($qw !== null) {
				$question = $qw->question;
			}
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->builder->newEvalRequest($correctAnswer, $studentAnswer, $question, $instance));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newVariablesRequest($html, $question, $instance) {
		try {
			$qw = $question;
			$iw = $instance;
			if($qw !== null) {
				$question = $qw->question;
			}
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->builder->newVariablesRequest($html, $question, $instance));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function readQuestionInstance($xml) {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionInstanceWrap($this->builder->readQuestionInstance($xml));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function readQuestion($xml) {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionWrap($this->builder->readQuestion($xml));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newQuestionInstance($question = null) {
		try {
			$this->wrapper->start();
			$qw = $question;
			if($qw !== null) {
				$question = $qw->question;
			}
			$r = new com_wiris_quizzes_wrap_QuestionInstanceWrap($this->builder->newQuestionInstance($question));
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function newQuestion() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionWrap($this->builder->newQuestion());
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function getQuizzesUIBuilder() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuizzesUIBuilderWrap($this->builder->getQuizzesUIBuilder());
			$this->wrapper->stop();
			return $r;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public $wrapper;
	public $builder;
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
	static $builderwrap;
	static function getInstance() {
		if(com_wiris_quizzes_wrap_QuizzesBuilderWrap::$builderwrap === null) {
			com_wiris_quizzes_wrap_QuizzesBuilderWrap::$builderwrap = new com_wiris_quizzes_wrap_QuizzesBuilderWrap();
		}
		return com_wiris_quizzes_wrap_QuizzesBuilderWrap::$builderwrap;
	}
	function __toString() { return 'com.wiris.quizzes.wrap.QuizzesBuilderWrap'; }
}

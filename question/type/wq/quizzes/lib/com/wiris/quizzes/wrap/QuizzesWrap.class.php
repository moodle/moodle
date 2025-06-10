<?php

class com_wiris_quizzes_wrap_QuizzesWrap extends com_wiris_quizzes_api_Quizzes {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		try {
			$this->wrapper = com_wiris_system_CallWrapper::getInstance();
			$this->wrapper->start();
			$this->quizzes = com_wiris_quizzes_impl_QuizzesImpl::getInstance();
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
		$config = $this->quizzes->getConfiguration();
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
			$r = $this->quizzes->getResourceUrl($name);
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
			$r = new com_wiris_quizzes_wrap_ConfigurationWrap($this->quizzes->getConfiguration());
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
			$r = new com_wiris_quizzes_wrap_MathFilterWrap($this->quizzes->getMathFilter());
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
			$r = new com_wiris_quizzes_wrap_QuizzesServiceWrap($this->quizzes->getQuizzesService());
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
	public function newFeedbackRequest($html, $instance) {
		try {
			$iw = $instance;
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newFeedbackRequest($html, $instance));
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
	public function newMultipleAnswersGradeRequest($correctAnswers, $studentAnswers) {
		if($correctAnswers !== null && !Std::is($correctAnswers, _hx_qtype("Array"))) {
			$correctAnswers = new _hx_array($correctAnswers);
		}
		if($studentAnswers !== null && !Std::is($correctAnswers, _hx_qtype("Array"))) {
			$studentAnswers = new _hx_array($studentAnswers);
		}
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newMultipleAnswersGradeRequest($correctAnswers, $studentAnswers));
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
	public function newSimpleGradeRequest($correctAnswer, $studentAnswer) {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newSimpleGradeRequest($correctAnswer, $studentAnswer));
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
	public function newGradeRequest($instance) {
		try {
			$iw = $instance;
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newGradeRequest($instance));
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
	public function newVariablesRequestWithQuestionData($html, $instance) {
		try {
			$iw = $instance;
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newVariablesRequestWithQuestionData($html, $instance));
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
	public function newVariablesRequest($html, $instance) {
		try {
			$iw = $instance;
			if($iw !== null) {
				$instance = $iw->instance;
			}
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuestionRequestWrap($this->quizzes->newVariablesRequest($html, $instance));
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
	public function readQuestionInstance($xml, $question) {
		try {
			$this->wrapper->start();
			$qw = $question;
			if($qw !== null) {
				$question = $qw->question;
			}
			$r = new com_wiris_quizzes_wrap_QuestionInstanceWrap($this->quizzes->readQuestionInstance($xml, $question));
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
			$r = new com_wiris_quizzes_wrap_QuestionWrap($this->quizzes->readQuestion($xml));
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
	public function newQuestionInstance($question) {
		try {
			$this->wrapper->start();
			$qw = $question;
			if($qw !== null) {
				$question = $qw->question;
			}
			$r = new com_wiris_quizzes_wrap_QuestionInstanceWrap($this->quizzes->newQuestionInstance($question));
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
			$r = new com_wiris_quizzes_wrap_QuestionWrap($this->quizzes->newQuestion());
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
	public function getQuizzesComponentBuilder() {
		try {
			$this->wrapper->start();
			$r = new com_wiris_quizzes_wrap_QuizzesComponentBuilderWrap($this->quizzes->getQuizzesComponentBuilder());
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
	public $quizzes;
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
	static $wrap;
	static function getInstance() {
		if(com_wiris_quizzes_wrap_QuizzesWrap::$wrap === null) {
			com_wiris_quizzes_wrap_QuizzesWrap::$wrap = new com_wiris_quizzes_wrap_QuizzesWrap();
		}
		return com_wiris_quizzes_wrap_QuizzesWrap::$wrap;
	}
	function __toString() { return 'com.wiris.quizzes.wrap.QuizzesWrap'; }
}

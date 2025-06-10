<?php

class com_wiris_quizzes_wrap_QuizzesUIBuilderWrap implements com_wiris_quizzes_api_ui_QuizzesUIBuilder{
	public function __construct($impl) {
		if(!php_Boot::$skip_constructor) {
		$this->impl = $impl;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function replaceFields($question, $instance, $element) {
		throw new HException("Not implemented");
	}
	public function getMathViewer() {
		throw new HException("Not implemented");
		return null;
	}
	public function newAuxiliarCasField($question, $instance, $index) {
		throw new HException("Not implemented");
		return null;
	}
	public function newAuthoringField($question, $instance, $correctAnswer, $userAnswer) {
		throw new HException("Not implemented");
		return null;
	}
	public function newAnswerField($question, $instance, $index) {
		throw new HException("Not implemented");
		return null;
	}
	public function newAnswerFeedback($question, $instance, $correctAnswer, $studentAnswer) {
		throw new HException("Not implemented");
		return null;
	}
	public function newEmbeddedAnswersEditor($question, $instance) {
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
			$r = new com_wiris_quizzes_wrap_EmbeddedAnswersEditorWrap($this->impl->newEmbeddedAnswersEditor($question, $instance));
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
	public function setLanguage($lang) {
		try {
			$this->wrapper->start();
			$this->impl->setLanguage($lang);
			$this->wrapper->stop();
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
	public $impl;
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
	function __toString() { return 'com.wiris.quizzes.wrap.QuizzesUIBuilderWrap'; }
}

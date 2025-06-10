<?php

class com_wiris_quizzes_wrap_SlotWrap implements com_wiris_quizzes_api_Slot{
	public function __construct($slot) {
		if(!php_Boot::$skip_constructor) {
		$this->slot = $slot;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function getGrammarUrl() {
		try {
			$this->wrapper->start();
			$r = $this->slot->getGrammarUrl();
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
	public function serialize() {
		try {
			$this->wrapper->start();
			$r = $this->slot->serialize();
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
	public function copy($model) {
		try {
			$this->wrapper->start();
			$r = $this->slot->copy($model);
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
	public function getAnswerFieldType() {
		try {
			$this->wrapper->start();
			$r = $this->slot->getAnswerFieldType();
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
	public function setAnswerFieldType($answerFieldType) {
		try {
			$this->wrapper->start();
			$this->slot->setAnswerFieldType($answerFieldType);
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
	public function setInitialContent($content) {
		try {
			$this->wrapper->start();
			$this->slot->setInitialContent($content);
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
	public function getInitialContent() {
		try {
			$this->wrapper->start();
			$r = $this->slot->getInitialContent();
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
	public function setSyntax($type) {
		try {
			$this->wrapper->start();
			$r = $this->slot->setSyntax($type);
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
	public function getSyntax() {
		try {
			$this->wrapper->start();
			$r = $this->slot->getSyntax();
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
	public function getProperty($name) {
		try {
			$this->wrapper->start();
			$r = $this->slot->getProperty($name);
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
	public function setProperty($name, $value) {
		try {
			$this->wrapper->start();
			$this->slot->setProperty($name, $value);
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
	public function removeAuthorAnswer($answer) {
		try {
			$this->wrapper->start();
			$aaw = $answer;
			if($aaw !== null) {
				$answer = $aaw->authorAnswer;
			}
			$this->slot->removeAuthorAnswer($answer);
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
	public function addNewAuthorAnswer($value) {
		try {
			$this->wrapper->start();
			$authorAnswer = new com_wiris_quizzes_wrap_AuthorAnswerWrap($this->slot->addNewAuthorAnswer($value));
			$this->wrapper->stop();
			return $authorAnswer;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function getAuthorAnswers() {
		try {
			$this->wrapper->start();
			$aaa = $this->slot->getAuthorAnswers();
			$r = new _hx_array(array());
			{
				$_g = 0;
				while($_g < $aaa->length) {
					$aa = $aaa[$_g];
					++$_g;
					$r->push(new com_wiris_quizzes_wrap_AuthorAnswerWrap($aa));
					unset($aa);
				}
			}
			$r = php_Lib::toPhpArray($r);
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
	public $slot;
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
	function __toString() { return 'com.wiris.quizzes.wrap.SlotWrap'; }
}

<?php

class com_wiris_quizzes_wrap_QuestionWrap implements com_wiris_quizzes_api_Question{
	public function __construct($question) {
		if(!php_Boot::$skip_constructor) {
		$this->question = $question;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function getDeprecationWarnings() {
		try {
			$this->wrapper->start();
			$r = $this->question->getDeprecationWarnings();
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
	public function removeSlot($slot) {
		try {
			$this->wrapper->start();
			$sw = $slot;
			if($sw !== null) {
				$slot = $sw->slot;
			}
			$this->question->removeSlot($slot);
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
	public function getSlots() {
		try {
			$this->wrapper->start();
			$sa = $this->question->getSlots();
			$r = new _hx_array(array());
			{
				$_g = 0;
				while($_g < $sa->length) {
					$s = $sa[$_g];
					++$_g;
					$r->push(new com_wiris_quizzes_wrap_SlotWrap($s));
					unset($s);
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
	public function addNewSlotFromModel($slot) {
		try {
			$this->wrapper->start();
			$s = new com_wiris_quizzes_wrap_SlotWrap($this->question->addNewSlotFromModel($slot));
			$this->wrapper->stop();
			return $s;
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				$this->wrapper->stop();
				throw new HException($e);
			}
		}
	}
	public function addNewSlot() {
		try {
			$this->wrapper->start();
			$s = new com_wiris_quizzes_wrap_SlotWrap($this->question->addNewSlot());
			$this->wrapper->stop();
			return $s;
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
			$this->question->setProperty($name, $value);
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
	public function getProperty($name) {
		try {
			$this->wrapper->start();
			$r = $this->question->getProperty($name);
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
			$r = $this->question->serialize();
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
	public function getAlgorithm() {
		try {
			$this->wrapper->start();
			$r = $this->question->getAlgorithm();
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
	public function setAlgorithm($session) {
		try {
			$this->wrapper->start();
			$this->question->setAlgorithm($session);
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
	public function getCorrectAnswer($index) {
		try {
			$this->wrapper->start();
			$r = $this->question->getCorrectAnswer($index);
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
	public function getCorrectAnswersLength() {
		try {
			$this->wrapper->start();
			$r = $this->question->getCorrectAnswersLength();
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
	public function setCorrectAnswer($index, $answer) {
		try {
			$this->wrapper->start();
			$this->question->setCorrectAnswer($index, $answer);
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
	public function getAnswerFieldType() {
		try {
			$this->wrapper->start();
			$r = $this->question->getAnswerFieldType();
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
	public function setAnswerFieldType($type) {
		try {
			$this->wrapper->start();
			$this->question->setAnswerFieldType($type);
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
	public function setOption($name, $value) {
		try {
			$this->wrapper->start();
			$this->question->setOption($name, $value);
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
	public function addAssertion($name, $correctAnswer, $studentAnswer, $parameters) {
		if($parameters !== null && !Std::is($parameters, _hx_qtype("Array"))) {
			$parameters = new _hx_array($parameters);
		}
		try {
			$this->wrapper->start();
			$this->question->addAssertion($name, $correctAnswer, $studentAnswer, $parameters);
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
	public function getStudentQuestion() {
		try {
			$this->wrapper->start();
			$response = new com_wiris_quizzes_wrap_QuestionWrap($this->question->getStudentQuestion());
			$this->wrapper->stop();
			return $response;
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
	public $question;
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
	function __toString() { return 'com.wiris.quizzes.wrap.QuestionWrap'; }
}

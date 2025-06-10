<?php

class com_wiris_quizzes_wrap_AuthorAnswerWrap implements com_wiris_quizzes_api_AuthorAnswer{
	public function __construct($authorAnswer) {
		if(!php_Boot::$skip_constructor) {
		$this->authorAnswer = $authorAnswer;
		$this->wrapper = com_wiris_system_CallWrapper::getInstance();
	}}
	public function copy($model) {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->copy($model);
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
			$r = $this->authorAnswer->serialize();
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
	public function setWeight($weight) {
		try {
			$this->wrapper->start();
			$this->authorAnswer->setWeight($weight);
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
	public function getWeight() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getWeight();
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
	public function removeValidation($validation) {
		try {
			$this->wrapper->start();
			$this->authorAnswer->removeValidation($validation);
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
	public function getValidation($name) {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getValidation($name);
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
	public function addNewValidation($name) {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->addNewValidation($name);
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
	public function getValidations() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getValidations();
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
	public function getComparison() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getComparison();
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
	public function setComparison($name) {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->setComparison($name);
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
	public function setValue($value) {
		try {
			$this->wrapper->start();
			$this->authorAnswer->setValue($value);
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
	public function getValueAsMathML() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getValueAsMathML();
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
	public function getFilterableValue() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getFilterableValue();
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
	public function getValue() {
		try {
			$this->wrapper->start();
			$r = $this->authorAnswer->getValue();
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
	public $authorAnswer;
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
	function __toString() { return 'com.wiris.quizzes.wrap.AuthorAnswerWrap'; }
}

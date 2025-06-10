<?php

class com_wiris_quizzes_impl_QuestionLazy extends com_wiris_quizzes_impl_QuestionInternal {
	public function __construct($xml) {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$s = _hx_index_of($xml, "<question", null);
		if($s > 0) {
			$xml = _hx_substr($xml, $s, null);
		}
		$s = _hx_last_index_of($xml, ">", null);
		if($s < strlen($xml) - 1) {
			$xml = _hx_substr($xml, 0, $s);
		}
		$s = _hx_index_of($xml, ">", null) + 1;
		$tag = _hx_substr($xml, 0, $s);
		$xml = _hx_substr($xml, $s, null);
		$s = _hx_last_index_of($xml, "<", null);
		if($s !== -1) {
			$xml = _hx_substr($xml, 0, $s);
		}
		$this->xml = $xml;
		if(StringTools::startsWith($tag, "<question id")) {
			$s = _hx_index_of($tag, "\"", null) + 1;
			$e = _hx_index_of($tag, "\"", $s);
			$this->id = _hx_substr($tag, $s, $e - $s);
		}
	}}
	public function getDeprecationWarnings() {
		return $this->getImpl()->getDeprecationWarnings();
	}
	public function addNewSlotFromModel($slot) {
		return $this->getImpl()->addNewSlotFromModel($slot);
	}
	public function removeSlot($slot) {
		$this->getImpl()->removeSlot($slot);
	}
	public function addNewSlot() {
		return $this->getImpl()->addNewSlot();
	}
	public function getSlots() {
		return $this->getImpl()->getSlots();
	}
	public function getImpl() {
		if($this->question === null) {
			$s = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getSerializer();
			$elem = $s->read("<question>" . $this->xml . "</question>");
			$tag = $s->getTagName($elem);
			if(!($tag === "question")) {
				throw new HException("Unexpected root tag " . $tag . ". Expected question.");
			}
			$this->question = $elem;
			$this->question->id = $this->id;
			$this->question->updateSlots();
		}
		return $this->question;
	}
	public function onSerialize($s) {
		if($this->question !== null) {
			$this->question->onSerialize($s);
		} else {
			$s->beginTag("question");
			$s->cacheAttribute("id", $this->id, null);
			$this->xml = $s->rawXml($this->xml);
			$s->endTag();
		}
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_QuestionImpl();
	}
	public function setId($id) {
		if($this->question !== null) {
			$this->question->setId($id);
		} else {
			$this->id = $id;
		}
	}
	public function hasId() {
		if($this->question !== null) {
			return $this->question->hasId();
		} else {
			return $this->id !== null;
		}
	}
	public function getProperty($name) {
		return $this->getImpl()->getProperty($name);
	}
	public function setProperty($name, $value) {
		$this->getImpl()->setProperty($name, $value);
	}
	public function getAlgorithm() {
		return $this->getImpl()->getAlgorithm();
	}
	public function setAlgorithm($session) {
		$this->getImpl()->setAlgorithm($session);
	}
	public function getCorrectAnswer($index) {
		return $this->getImpl()->getCorrectAnswer($index);
	}
	public function getCorrectAnswersLength() {
		return $this->getImpl()->getCorrectAnswersLength();
	}
	public function setCorrectAnswer($index, $answer) {
		$this->getImpl()->setCorrectAnswer($index, $answer);
	}
	public function getAnswerFieldType() {
		return $this->getImpl()->getAnswerFieldType();
	}
	public function setAnswerFieldType($type) {
		$this->getImpl()->setAnswerFieldType($type);
	}
	public function setOption($name, $value) {
		$this->getImpl()->setOption($name, $value);
	}
	public function addAssertion($name, $correctAnswer, $studentAnswer, $parameters) {
		$this->getImpl()->addAssertion($name, $correctAnswer, $studentAnswer, $parameters);
	}
	public function getStudentQuestion() {
		return $this->getImpl()->getStudentQuestion();
	}
	public $question;
	public $id;
	public $xml;
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
	function __toString() { return 'com.wiris.quizzes.impl.QuestionLazy'; }
}

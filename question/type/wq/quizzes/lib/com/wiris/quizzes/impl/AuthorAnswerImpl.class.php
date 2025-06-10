<?php

class com_wiris_quizzes_impl_AuthorAnswerImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_quizzes_api_AuthorAnswer{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->value = new com_wiris_quizzes_impl_CorrectAnswer();
		$this->validations = new _hx_array(array());
	}}
	public function importAuthorAnswer($authorAnswer) {
		$this->id = $authorAnswer->id;
		$this->value = $authorAnswer->value;
		$this->comparison = $authorAnswer->comparison;
		$this->validations = $authorAnswer->validations;
		$this->question = $authorAnswer->question;
		$this->slot = $authorAnswer->slot;
	}
	public function removeValidation($validation) {
		if($this->validations->remove($validation) && $this->question !== null) {
			$this->question->assertionRemoved($validation);
		}
	}
	public function getValidation($name) {
		{
			$_g = 0; $_g1 = $this->validations;
			while($_g < $_g1->length) {
				$validation = $_g1[$_g];
				++$_g;
				if($validation->getName() === $name) {
					return $validation;
				}
				unset($validation);
			}
		}
		return null;
	}
	public function addNewValidation($name) {
		if($name === null) {
			return null;
		}
		$prev = $this->getValidation($name);
		if($prev !== null) {
			return $prev;
		}
		$v = new com_wiris_quizzes_impl_ValidationAssertion();
		$v->setName($name);
		$this->validations->push($v);
		if($this->question !== null && $this->slot !== null) {
			$this->question->assertionAdded($v, $this->id, $this->slot->id);
		}
		return $v;
	}
	public function getValidations() {
		$vv = new _hx_array(array());
		$vv = $this->validations->copy();
		return $vv;
	}
	public function getComparison() {
		return $this->comparison;
	}
	public function setComparison($name) {
		if($name === null || $name === $this->comparison->getName()) {
			return $this->comparison;
		}
		$this->question->id = null;
		$this->comparison->setName($name);
		$this->comparison->parameters = new _hx_array(array());
		return $this->comparison;
	}
	public function setWeight($weight) {
		$this->value->weight = $weight;
	}
	public function getWeight() {
		return $this->value->weight;
	}
	public function setValue($value) {
		if($value === null || $value === $this->getValue()) {
			return;
		}
		$this->value->set($value);
		$this->question->id = null;
	}
	public function getFilterableValue() {
		return com_wiris_quizzes_impl_QuizzesImpl::getInstance()->mathContentToFilterableValue($this->value, $this->slot->getInitialContent());
	}
	public function getValueAsMathML() {
		if($this->value->type === com_wiris_quizzes_impl_MathContent::$TYPE_MATHML) {
			return $this->value->content;
		} else {
			if($this->value->type === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) {
				$html = new com_wiris_quizzes_impl_HTMLTools();
				return $html->textToMathML($this->value->content);
			}
		}
		throw new HException("Type not compatible with MathML");
	}
	public function getValue() {
		return $this->value->content;
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_AuthorAnswerImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_AuthorAnswerImpl::$TAGNAME);
		$s->attributeString(com_wiris_quizzes_impl_AuthorAnswerImpl::$ATTRIBUTE_ID, $this->id, "0");
		$s->serializeChildName($this->value, com_wiris_quizzes_impl_CorrectAnswer::$TAGNAME);
		$s->serializeChildName($this->comparison, com_wiris_quizzes_impl_ComparisonAssertion::$TAGNAME);
		$s->serializeArrayName($this->validations, com_wiris_quizzes_impl_AuthorAnswerImpl::$VALIDATIONS_TAGNAME);
		$s->endTag();
	}
	public function copy($model) {
		$aa = $model;
		$this->setValue($aa->getValue());
		$this->comparison->importAssertionNameAndParams($aa->comparison->copy());
		if($this->validations === null) {
			$this->validations = new _hx_array(array());
		}
		while($this->validations->length > 0) {
			$this->removeValidation($this->validations[$this->validations->length - 1]);
		}
		if($aa->validations !== null) {
			$vals = $aa->validations;
			{
				$_g = 0;
				while($_g < $vals->length) {
					$val = $vals[$_g];
					++$_g;
					$val2 = com_wiris_quizzes_impl_ValidationAssertion::fromAssertion($val);
					$this->validations->push($val2);
					unset($val2,$val);
				}
			}
		}
		return $this;
	}
	public $slot;
	public $question;
	public $validations;
	public $comparison;
	public $value;
	public $id;
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
	static $TAGNAME = "authorAnswer";
	static $VALIDATIONS_TAGNAME = "validationAssertions";
	static $ATTRIBUTE_ID = "id";
	static function newWithQuestionCallback($question, $slot) {
		$aa = new com_wiris_quizzes_impl_AuthorAnswerImpl();
		$aa->question = $question;
		$aa->slot = $slot;
		$aa->comparison = com_wiris_quizzes_impl_ComparisonAssertion::getDefaultComparison($slot->getSyntax()->getName());
		return $aa;
	}
	function __toString() { return 'com.wiris.quizzes.impl.AuthorAnswerImpl'; }
}

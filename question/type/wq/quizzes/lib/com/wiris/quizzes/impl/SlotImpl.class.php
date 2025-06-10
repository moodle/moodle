<?php

class com_wiris_quizzes_impl_SlotImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_quizzes_api_Slot{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->syntax = com_wiris_quizzes_impl_SyntaxAssertion::getDefaultSyntax();
		$this->initialContent = new com_wiris_quizzes_impl_InitialContent();
		$this->initialContent->set("");
		$this->authorAnswers = new _hx_array(array());
		$this->localData = new _hx_array(array());
	}}
	public function getMultiStepEquivalentMathAssertion($multiStepMath) {
		$math = com_wiris_quizzes_impl_SyntaxAssertion::getDefaultSyntax();
		$typeOfTask = $multiStepMath->getParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$TYPE_OF_TASK);
		if($typeOfTask !== null) {
			if($typeOfTask === com_wiris_quizzes_impl_Assertion::$TYPE_OF_TASK_SINGLE_VARIABLE_EQUATION) {
				$math->setParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$NO_BRACKETS_LIST, "true");
				$math->setParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$LIST_OPERATORS, ";");
			} else {
				if($typeOfTask === com_wiris_quizzes_impl_Assertion::$TYPE_OF_TASK_BASIC_OPERATIONS) {
				}
			}
		}
		return $math;
	}
	public function syntacticAssertionToURL($a) {
		$sb = new StringBuf();
		if($a->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
			$a = $this->getMultiStepEquivalentMathAssertion($a);
		}
		if($a->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH) {
			$sb->add("Math");
		} else {
			if($a->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC) {
				$sb->add("Graphic");
			} else {
				if($a->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$STRING) {
					$sb->add("String");
				}
			}
		}
		if($a->parameters !== null && $a->parameters->length > 0) {
			$sb->add("?");
			$i = null;
			{
				$_g1 = 0; $_g = $a->parameters->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$p = $a->parameters[$i1];
					if($i1 > 0) {
						$sb->add("&");
					}
					$sb->add(rawurlencode($p->name));
					$sb->add("=");
					$sb->add(rawurlencode($p->content));
					unset($p,$i1);
				}
			}
		}
		return $sb->b;
	}
	public function getGrammarUrl() {
		$prefix = com_wiris_quizzes_api_Quizzes::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL);
		$prefix .= "/grammar/";
		$url = null;
		if($this->syntax !== null) {
			$url = $prefix . $this->syntacticAssertionToURL($this->syntax);
		}
		if($url === null) {
			$url = $prefix . "Math";
		}
		return $url;
	}
	public function isSlotCompoundAnswer() {
		return $this->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE;
	}
	public function removeProperty($name) {
		$this->question->id = null;
		com_wiris_quizzes_impl_QuestionImpl::removeLocalDataFromArray(com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name), $this->localData);
	}
	public function getAnswerFieldType() {
		$stringType = $this->getProperty(com_wiris_quizzes_api_PropertyName::$ANSWER_FIELD_TYPE);
		return com_wiris_quizzes_impl_QuizzesEnumUtils::string2answerFieldType($stringType);
	}
	public function importSlot($slot) {
		$this->id = $slot->id;
		$this->syntax = $slot->syntax;
		$this->authorAnswers = $slot->authorAnswers;
		$this->initialContent = $slot->initialContent;
		$this->localData = $slot->localData;
		$this->question = $slot->question;
	}
	public function setInitialContent($content) {
		$this->initialContent->set($content);
	}
	public function getInitialContent() {
		return $this->initialContent->content;
	}
	public function setSyntax($type) {
		if($type === null || $type === $this->syntax->getName()) {
			return $this->syntax;
		}
		$this->question->id = null;
		$this->syntax->setName($type);
		if($type === com_wiris_quizzes_api_assertion_SyntaxName::$STRING && $this->getAnswerFieldType() != com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD) {
			$this->setAnswerFieldType(com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD);
		} else {
			if($type === com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC && $this->getAnswerFieldType() != com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_GRAPH_EDITOR) {
				$this->setAnswerFieldType(com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_GRAPH_EDITOR);
			} else {
				if($type === com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP && $this->getAnswerFieldType() != com_wiris_quizzes_api_ui_AnswerFieldType::$MULTISTEP_MATH_EDITOR) {
					$this->setAnswerFieldType(com_wiris_quizzes_api_ui_AnswerFieldType::$MULTISTEP_MATH_EDITOR);
				} else {
					if($this->getAnswerFieldType() == com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_GRAPH_EDITOR) {
						$this->setAnswerFieldType(com_wiris_quizzes_api_ui_AnswerFieldType::$INLINE_MATH_EDITOR);
					}
				}
			}
		}
		$this->syntax->parameters = new _hx_array(array());
		if($type === com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
			$this->syntax->setParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$REF_ID, _hx_string_rec($this->question->getAvailableRefId(), "") . "");
		}
		return $this->syntax;
	}
	public function getSyntax() {
		return $this->syntax;
	}
	public function getProperty($name) {
		$key = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		if(!com_wiris_util_type_Arrays::containsArray(com_wiris_quizzes_impl_LocalData::$keys, $key)) {
			throw new HException("Property " . $key . " is not supported in Slot. Please get it from the Question object instead.");
		}
		$ld = com_wiris_quizzes_impl_QuestionImpl::getLocalDataFromArray($key, $this->localData);
		return (($ld !== null) ? $ld : $this->question->getProperty($name));
	}
	public function setAnswerFieldType($type) {
		if($type !== null) {
			$this->setProperty(com_wiris_quizzes_api_PropertyName::$ANSWER_FIELD_TYPE, com_wiris_quizzes_impl_QuizzesEnumUtils::answerFieldType2String($type));
		} else {
			throw new HException("Null answer field type!");
		}
	}
	public function setLocalData($key, $value) {
		if($this->localData === null) {
			$this->localData = new _hx_array(array());
		}
		com_wiris_quizzes_impl_QuestionImpl::setLocalDataToArray($key, $value, $this->localData);
	}
	public function setProperty($name, $value) {
		$key = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		if(!com_wiris_util_type_Arrays::containsArray(com_wiris_quizzes_impl_LocalData::$keys, $key)) {
			throw new HException("Property " . $key . " is not supported in Slot. Please set it to the Question object instead.");
		}
		$this->setLocalData($key, $value);
	}
	public function removeAuthorAnswer($answer) {
		if($this->authorAnswers->remove($answer)) {
			$this->question->authorAnswerRemoved($answer);
		}
	}
	public function addAuthorAnswerImpl($aa) {
		$this->authorAnswers->push($aa);
		$this->question->authorAnswerAdded($aa, $this);
		return $aa;
	}
	public function addNewAuthorAnswer($value) {
		$aa = com_wiris_quizzes_impl_AuthorAnswerImpl::newWithQuestionCallback($this->question, $this);
		$value = com_wiris_util_xml_MathMLUtils::convertEditor2Newlines($value);
		$aa->value->set($value);
		return $this->addAuthorAnswerImpl($aa);
	}
	public function getAuthorAnswers() {
		$aa = new _hx_array(array());
		$aa = $this->authorAnswers->copy();
		return $aa;
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_SlotImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_SlotImpl::$TAGNAME);
		$this->id = $s->attributeString(com_wiris_quizzes_impl_SlotImpl::$ATTRIBUTE_ID, $this->id, "0");
		$this->localData = $s->serializeArrayName($this->localData, com_wiris_quizzes_impl_SlotImpl::$LOCALDATA_TAGNAME);
		$this->initialContent = $s->serializeChildName($this->initialContent, com_wiris_quizzes_impl_InitialContent::$TAGNAME);
		$s->endTag();
	}
	public function copyData($slotModel, $copyAuthorAnswers) {
		$slot = $slotModel;
		$this->setInitialContent($slot->getInitialContent());
		$this->syntax->importAssertionNameAndParams($slot->syntax->copy());
		if($slot->localData !== null) {
			$this->localData = new _hx_array(array());
			$ldArray = $slot->localData;
			{
				$_g = 0;
				while($_g < $ldArray->length) {
					$ld = $ldArray[$_g];
					++$_g;
					$this->setLocalData($ld->name, $ld->value);
					unset($ld);
				}
			}
		}
		$authorAnswers = $slot->authorAnswers;
		if($copyAuthorAnswers && $authorAnswers !== null) {
			$_g = 0;
			while($_g < $authorAnswers->length) {
				$aa = $authorAnswers[$_g];
				++$_g;
				$aaClone = com_wiris_quizzes_impl_AuthorAnswerImpl::newWithQuestionCallback($this->question, $this);
				$aaClone->copy($aa);
				$this->addAuthorAnswerImpl($aaClone);
				unset($aaClone,$aa);
			}
		}
	}
	public function copy($slotModel) {
		$this->copyData($slotModel, false);
		return $this;
	}
	public $question;
	public $localData;
	public $initialContent;
	public $authorAnswers;
	public $syntax;
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
	static $TAGNAME = "slot";
	static $ATTRIBUTE_ID = "id";
	static $AUTHORANSWERS_TAGNAME = "authorAnswers";
	static $LOCALDATA_TAGNAME = "localData";
	static function newWithQuestionCallback($question) {
		$slot = new com_wiris_quizzes_impl_SlotImpl();
		$slot->question = $question;
		return $slot;
	}
	function __toString() { return 'com.wiris.quizzes.impl.SlotImpl'; }
}

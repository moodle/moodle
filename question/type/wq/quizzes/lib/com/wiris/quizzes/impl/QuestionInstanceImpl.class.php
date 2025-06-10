<?php

class com_wiris_quizzes_impl_QuestionInstanceImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_util_type_Comparator, com_wiris_quizzes_api_QuestionInstance{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->userData = new com_wiris_quizzes_impl_UserData();
		$this->userData->randomSeed = Std::random(65536);
		$this->variables = null;
		$this->checks = null;
		$this->compoundChecks = null;
	}}
	public function cloneCheckStructureIntoQuestion($question) {
		if(!$this->hasEvaluation()) {
			return;
		}
		$q = $question->getImpl();
		if($q->correctAnswers === null) {
			$q->correctAnswers = new _hx_array(array());
		}
		if($q->assertions === null) {
			$q->assertions = new _hx_array(array());
		}
		$studentAnswers = $this->checks->keys();
		while($studentAnswers->hasNext()) {
			$studentAnswer = $studentAnswers->next();
			$assertionChecks = $this->checks->get($studentAnswer);
			{
				$_g = 0;
				while($_g < $assertionChecks->length) {
					$check = $assertionChecks[$_g];
					++$_g;
					$name = $check->getAssertionName();
					if(!com_wiris_quizzes_impl_Assertion::isSyntacticName($name)) {
						$ca = $check->getCorrectAnswer();
						$cai = Std::parseInt($ca);
						while($cai >= $q->correctAnswers->length) {
							$newCA = new com_wiris_quizzes_impl_CorrectAnswer();
							$newCA->id = _hx_string_rec($q->correctAnswers->length, "") . "";
							$newCA->set("");
							$q->correctAnswers->push($newCA);
							unset($newCA);
						}
						if($q->getAssertionIndex($check->getAssertionName(), $check->getCorrectAnswer(), $check->getAnswer()) === -1) {
							$a = new com_wiris_quizzes_impl_Assertion();
							$a->name = $check->getAssertionName();
							$a->answer = $check->getAnswers();
							$a->correctAnswer = $check->getCorrectAnswers();
							$q->assertions->push($a);
							unset($a);
						}
						unset($cai,$ca);
					}
					unset($name,$check);
				}
				unset($_g);
			}
			unset($studentAnswer,$assertionChecks);
		}
		$q->updateSlots();
	}
	public function getChecks($slot, $authorAnswer) {
		$slotIndex = Std::parseInt($slot->id);
		$authorAnswerIndex = Std::parseInt($authorAnswer->id);
		return $this->getAssertionChecks($authorAnswerIndex, $slotIndex);
	}
	public function getCompoundGrade($slot, $authorAnswer, $index) {
		$slotIndex = Std::parseInt($slot->id);
		$authorAnswerIndex = Std::parseInt($authorAnswer->id);
		return $this->getCompoundAnswerGrade($authorAnswerIndex, $slotIndex, $index, $this->question);
	}
	public function getGrade($slot, $authorAnswer) {
		$slotIndex = Std::parseInt($slot->id);
		$authorAnswerIndex = Std::parseInt($authorAnswer->id);
		return $this->getAnswerGrade($authorAnswerIndex, $slotIndex, $this->question);
	}
	public function isSlotAnswerCorrect($slot) {
		$index = Std::parseInt($slot->id);
		return $this->isAnswerCorrect($index);
	}
	public function setSlotAnswer($slot, $answer) {
		$index = Std::parseInt($slot->id);
		$this->setStudentAnswer($index, $answer);
	}
	public function getSlotAnswer($slot) {
		$index = Std::parseInt($slot->id);
		return $this->getStudentAnswer($index);
	}
	public function concatenate($a, $e) {
		$b = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$b[$i1] = $a[$i1];
				unset($i1);
			}
		}
		$b[$a->length] = $e;
		return $b;
	}
	public function equalsArrays($a1, $a2) {
		if($a1->length !== $a2->length) {
			return false;
		}
		{
			$_g1 = 0; $_g = $a1->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if(!($a1[$i] === $a2[$i])) {
					return false;
				}
				unset($i);
			}
		}
		return true;
	}
	public function hasCompoundAssociatedCheck($a) {
		$answers = $this->compoundChecks->keys();
		while($answers->hasNext()) {
			$answer = $answers->next();
			$correctAnswers = $this->compoundChecks->get($answer)->keys();
			while($correctAnswers->hasNext()) {
				$correctAnswer = $correctAnswers->next();
				$checks = $this->compoundChecks->get($answer)->get($correctAnswer);
				{
					$_g = 0;
					while($_g < $checks->length) {
						$aa = $checks[$_g];
						++$_g;
						if($aa->getAssertionName() === $a->getAssertionName() && $this->equalsArrays($a->getAnswers(), $aa->getAnswers()) && $this->equalsArrays($a->getCorrectAnswers(), $aa->getCorrectAnswers())) {
							return true;
						}
						unset($aa);
					}
					unset($_g);
				}
				unset($correctAnswer,$checks);
			}
			unset($correctAnswers,$answer);
		}
		return false;
	}
	public function setChecksCompoundAnswers() {
		if($this->compoundChecks === null) {
			return;
		}
		$answers = $this->checks->keys();
		while($answers->hasNext()) {
			$aa = $this->checks->get($answers->next());
			{
				$_g = 0;
				while($_g < $aa->length) {
					$a = $aa[$_g];
					++$_g;
					if($this->hasCompoundAssociatedCheck($a)) {
						$a->setAnswers(new _hx_array(array()));
						$a->setCorrectAnswers(new _hx_array(array()));
					}
					unset($a);
				}
				unset($_g);
			}
			unset($aa);
		}
		$answers = $this->compoundChecks->keys();
		while($answers->hasNext()) {
			$answer = $answers->next();
			$correctAnswers = $this->compoundChecks->get($answer)->keys();
			while($correctAnswers->hasNext()) {
				$correctAnswer = $correctAnswers->next();
				$checks = $this->compoundChecks->get($answer)->get($correctAnswer);
				{
					$_g = 0;
					while($_g < $checks->length) {
						$a = $checks[$_g];
						++$_g;
						$a->setCorrectAnswers($this->concatenate($a->getCorrectAnswers(), $correctAnswer));
						$a->setAnswers($this->concatenate($a->getAnswers(), $answer));
						unset($a);
					}
					unset($_g);
				}
				unset($correctAnswer,$checks);
			}
			unset($correctAnswers,$answer);
		}
	}
	public function setParameter($name, $value) {
		$this->userData->setParameter($name, $value);
	}
	public function getTextVariables() {
		return $this->getTypeVariables(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
	}
	public function getMathMLVariables() {
		return $this->getTypeVariables(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
	}
	public function getTypeVariables($type) {
		if($this->hasVariables() && $this->variables->exists($type)) {
			return $this->variables->get($type);
		} else {
			return null;
		}
	}
	public function getHandwritingConstraints() {
		if($this->handConstraints === null) {
			$json = $this->getLocalDataImpl(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS);
			if($json !== null) {
				$this->handConstraints = com_wiris_quizzes_impl_HandwritingConstraints::readHandwritingConstraints($json);
			} else {
				if($this->question !== null) {
					$this->handConstraints = com_wiris_quizzes_impl_HandwritingConstraints::newHandwritingConstraints();
					$this->handConstraints->addQuestionConstraints($this->question->getImpl());
					$this->handConstraints->addQuestionInstanceConstraints($this);
				}
			}
		}
		return $this->handConstraints;
	}
	public function serializeHandConstraints() {
		if($this->handConstraints !== null) {
			$this->setLocalDataImpl(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS, $this->handConstraints->toJSON(), false);
		}
	}
	public function areVariablesReady() {
		if($this->variables !== null) {
			if($this->variables->exists(com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF)) {
				$cache = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getImagesCache();
				$images = $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF);
				$names = $images->keys();
				while($names->hasNext()) {
					$filename = $images->get($names->next());
					if(com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
						$s = com_wiris_system_Storage::newStorage($filename);
						if(!$s->exists()) {
							return false;
						}
						unset($s);
					} else {
						if($cache->get($filename) === null) {
							return false;
						}
					}
					unset($filename);
				}
			}
		}
		return true;
	}
	public function getAssertionChecks($correctAnswer, $studentAnswer) {
		if($this->checks !== null) {
			$answerChecks = $this->checks->get("" . _hx_string_rec($studentAnswer, ""));
			if($answerChecks !== null) {
				$res = new _hx_array(array());
				$i = null;
				{
					$_g1 = 0; $_g = $answerChecks->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						$ca = _hx_array_get($answerChecks, $i1)->getCorrectAnswers();
						$j = null;
						{
							$_g3 = 0; $_g2 = $ca->length;
							while($_g3 < $_g2) {
								$j1 = $_g3++;
								if($ca[$j1] === "" . _hx_string_rec($correctAnswer, "")) {
									$res->push($answerChecks[$i1]);
								}
								unset($j1);
							}
							unset($_g3,$_g2);
						}
						unset($j,$i1,$ca);
					}
				}
				$resarray = new _hx_array(array());
				$resarray = $res->copy();
				return $resarray;
			}
		}
		return new _hx_array(array());
	}
	public function getStudentAnswersLength() {
		return com_wiris_quizzes_impl_QuestionInstanceImpl_0($this);
	}
	public function getStudentAnswer($index) {
		if($this->userData->answers !== null && $index < $this->userData->answers->length) {
			$a = $this->userData->answers[$index];
			if($a !== null) {
				return $a->content;
			}
		}
		return null;
	}
	public function setStudentAnswer($index, $answer) {
		$this->userData->setUserAnswer($index, $answer);
	}
	public function setAuxiliaryText($text) {
		if($text !== null && strlen(trim($text)) > 0) {
			$this->setLocalData(com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT, $text);
		} else {
			if($this->localData !== null) {
				$i = null;
				{
					$_g1 = 0; $_g = $this->localData->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						if(_hx_array_get($this->localData, $i1)->name === com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT) {
							$this->localData->remove($this->localData[$i1]);
						}
						unset($i1);
					}
				}
			}
		}
	}
	public function setAuxiliarText($text) {
		$this->setAuxiliaryText($text);
	}
	public function setCasSession($session) {
		if($session !== null && strlen(trim($session)) > 0) {
			$this->setLocalData(com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION, $session);
		} else {
			if($this->localData !== null) {
				$i = null;
				{
					$_g1 = 0; $_g = $this->localData->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						if(_hx_array_get($this->localData, $i1)->name === com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION) {
							$this->localData->remove($this->localData[$i1]);
						}
						unset($i1);
					}
				}
			}
		}
	}
	public function setRandomSeed($seed) {
		$this->setProperty(com_wiris_quizzes_api_PropertyName::$MULTISTEP_SESSION_ID, null);
		$this->userData->randomSeed = $seed;
	}
	public function parseTextBoolean($text) {
		$trues = new _hx_array(array("true", "cierto", "cert", "t" . com_wiris_quizzes_impl_QuestionInstanceImpl_1($this, $text) . "ene", "ziur", "vrai", "wahr", "vero", "waar", "verdadeiro", "certo"));
		$i = null;
		{
			$_g1 = 0; $_g = $trues->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($trues[$i1] === $text) {
					return true;
				}
				unset($i1);
			}
		}
		return false;
	}
	public function updateAnswer($qi) {
		$i = null;
		if($qi->userData->answers !== null) {
			if($this->userData->answers === null) {
				$this->userData->answers = new _hx_array(array());
			}
			{
				$_g1 = 0; $_g = $qi->userData->answers->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$a = $qi->userData->answers[$i1];
					if($this->userData->answers->length > $i1) {
						$this->userData->answers[$i1] = $a;
					} else {
						$this->userData->answers->push($a);
					}
					unset($i1,$a);
				}
			}
		}
		$this->setLocalData(com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION, $qi->getLocalData(com_wiris_quizzes_impl_LocalData::$KEY_CAS_SESSION));
		$this->setLocalData(com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT, $qi->getLocalData(com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_TEXT));
	}
	public function updateFromStudentQuestionInstance($qi) {
		$ii = $qi;
		$this->userData->answers = $ii->userData->answers;
		$this->localData = $ii->localData;
	}
	public function copyVariableToStudentHash($variable, $studentVariables) {
		$variableKeys = $this->variables->keys();
		while($variableKeys->hasNext()) {
			$type = $variableKeys->next();
			$typeVariables = $this->variables->get($type);
			if($typeVariables->exists($variable)) {
				if(!$studentVariables->exists($type)) {
					$studentVariables->set($type, new Hash());
				}
				$studentVariables->get($type)->set($variable, $typeVariables->get($variable));
			}
			unset($typeVariables,$type);
		}
	}
	public function getStudentQuestionInstanceVariables($variables, $question) {
		if($question === null || $variables === null) {
			return null;
		}
		$studentVariables = new Hash();
		$tools = new com_wiris_quizzes_impl_HTMLTools();
		$slots = $question->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$slot = $slots[$_g];
				++$_g;
				if($slot->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE && $slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH) {
					$authorAnswers = $slot->getAuthorAnswers();
					$authorAnswerValue = _hx_array_get($authorAnswers, 0)->getValue();
					$mathContent = new com_wiris_quizzes_impl_MathContent();
					$mathContent->set($authorAnswerValue);
					$compoundAnswer = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($mathContent);
					{
						$_g2 = 0; $_g1 = $compoundAnswer->length;
						while($_g2 < $_g1) {
							$i = $_g2++;
							$answer = $compoundAnswer[$i];
							$compoundAnswerVariables = $tools->extractVariableNames($answer[0]);
							{
								$_g3 = 0;
								while($_g3 < $compoundAnswerVariables->length) {
									$compoundAnswerVariable = $compoundAnswerVariables[$_g3];
									++$_g3;
									$this->copyVariableToStudentHash($compoundAnswerVariable, $studentVariables);
									unset($compoundAnswerVariable);
								}
								unset($_g3);
							}
							unset($i,$compoundAnswerVariables,$answer);
						}
						unset($_g2,$_g1);
					}
					unset($mathContent,$compoundAnswer,$authorAnswers,$authorAnswerValue);
				}
				if($slot->getSyntax()->getName() != com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC && $slot->getInitialContent() !== null) {
					$variableNames = $tools->extractVariableNames($slot->getInitialContent());
					{
						$_g1 = 0;
						while($_g1 < $variableNames->length) {
							$variable = $variableNames[$_g1];
							++$_g1;
							$this->copyVariableToStudentHash($variable, $studentVariables);
							unset($variable);
						}
						unset($_g1);
					}
					unset($variableNames);
				}
				unset($slot);
			}
		}
		return $studentVariables;
	}
	public function getStudentQuestionInstance() {
		$qi = new com_wiris_quizzes_impl_QuestionInstanceImpl();
		$qi->userData->randomSeed = 0;
		$qi->userData->answers = $this->userData->answers;
		$qi->handConstraints = $this->handConstraints;
		$qi->localData = $this->localData;
		$qi->checks = $this->checks;
		$qi->compoundChecks = $this->compoundChecks;
		$qi->variables = $this->getStudentQuestionInstanceVariables($this->variables, $this->question);
		return $qi;
	}
	public function getBooleanVariableValue($name) {
		if(!$this->hasVariables()) {
			return false;
		}
		$name = trim($name);
		if(StringTools::startsWith($name, "#")) {
			$name = _hx_substr($name, 1, null);
		}
		if($this->variables->exists(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT)) {
			$textvars = $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
			if($textvars->exists($name)) {
				$textValue = $textvars->get($name);
				return $this->parseTextBoolean($textValue);
			}
		}
		if($this->variables->exists(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML)) {
			$mmlvars = $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
			if($mmlvars->exists($name)) {
				$mmlValue = $mmlvars->get($name);
				$striptags = new EReg("<[^>]*>", "");
				$textValue = $striptags->replace($mmlValue, "");
				$textValue = trim($textValue);
				return $this->parseTextBoolean($textValue);
			}
		}
		return false;
	}
	public function hashToVariables($h, $a) {
		if($h === null) {
			return null;
		}
		if($a === null) {
			$a = new _hx_array(array());
		}
		$t = $h->keys();
		while($t->hasNext()) {
			$type = $t->next();
			$vars = $h->get($type);
			$names = $vars->keys();
			while($names->hasNext()) {
				$name = $names->next();
				$v = new com_wiris_quizzes_impl_Variable();
				$v->type = $type;
				$v->name = $name;
				$v->content = $vars->get($name);
				$a->push($v);
				unset($v,$name);
			}
			unset($vars,$type,$names);
		}
		return $a;
	}
	public function variablesToHash($a, $h) {
		if($a === null) {
			return null;
		}
		if($h === null) {
			$h = new Hash();
		}
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$v = $a[$i1];
				if(!$h->exists($v->type)) {
					$h->set($v->type, new Hash());
				}
				$h->get($v->type)->set($v->name, $v->content);
				unset($v,$i1);
			}
		}
		return $h;
	}
	public function hashToChecks($h) {
		if($h === null) {
			return null;
		}
		$a = new _hx_array(array());
		$answers = $h->keys();
		while($answers->hasNext()) {
			$answer = $answers->next();
			$a = $a->concat($h->get($answer));
			unset($answer);
		}
		return $a;
	}
	public function checksToHash($a, $h) {
		if($a === null) {
			return null;
		}
		if($h === null) {
			$h = new Hash();
		}
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c = $a[$i1];
				if(!$h->exists($c->getAnswer())) {
					$h->set($c->getAnswer(), new _hx_array(array()));
				}
				$answerChecks = $h->get($c->getAnswer());
				$answerChecks->push($c);
				unset($i1,$c,$answerChecks);
			}
		}
		return $h;
	}
	public function processMatchingChecks($correctAnswer, $checks) {
		$result = new _hx_array(array());
		$i = null;
		$eval = 0;
		$check = 0;
		{
			$_g1 = 0; $_g = $checks->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_array_get($checks, $i1)->getCorrectAnswer() === "" . _hx_string_rec($correctAnswer, "")) {
					$c = $checks[$i1];
					if(StringTools::startsWith($c->assertion, "syntax_")) {
						$result->insert($eval, $checks[$i1]);
						$eval++;
						$check++;
					} else {
						if(StringTools::startsWith($c->assertion, "equivalent_")) {
							$result->insert($check, $checks[$i1]);
							$check++;
						} else {
							$result->push($checks[$i1]);
						}
					}
					unset($c);
				}
				unset($i1);
			}
		}
		return $result;
	}
	public function getMatchingCompoundChecks($slot, $authorAnswer, $index) {
		$correctAnswer = Std::parseInt($authorAnswer->id);
		$userAnswer = Std::parseInt($slot->id);
		$isGraphical = $slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC;
		$checks = null;
		if($isGraphical) {
			$elements = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getElementsToGradeFromAuthorAnswer($authorAnswer);
			if($index >= 0 && $index < $elements->length) {
				$checks = $this->getCompoundAnswerChecks($correctAnswer, $userAnswer, $elements[$index], true);
			}
		} else {
			$checks = $this->getCompoundAnswerChecks($correctAnswer, $userAnswer, _hx_string_rec($index, "") . "", false);
		}
		if($checks === null) {
			return new _hx_array(array());
		} else {
			return $this->processMatchingChecks($correctAnswer, $checks);
		}
	}
	public function getMatchingChecks($slot, $authorAnswer) {
		$correctAnswer = Std::parseInt($authorAnswer->id);
		$userAnswer = Std::parseInt($slot->id);
		$result = new _hx_array(array());
		if($this->checks === null || !$this->checks->exists(_hx_string_rec($userAnswer, "") . "")) {
			return $result;
		}
		return $this->processMatchingChecks($correctAnswer, $this->checks->get(_hx_string_rec($userAnswer, "") . ""));
	}
	public function isAnswerSyntaxCorrect($answer) {
		$correct = true;
		if($this->checks !== null && $this->checks->exists(_hx_string_rec($answer, "") . "")) {
			$checks = $this->checks->get(_hx_string_rec($answer, "") . "");
			$i = null;
			{
				$_g1 = 0; $_g = $checks->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$ac = $checks[$i1];
					$j = null;
					{
						$_g3 = 0; $_g2 = com_wiris_quizzes_impl_Assertion::$syntactic->length;
						while($_g3 < $_g2) {
							$j1 = $_g3++;
							if($ac->assertion === com_wiris_quizzes_impl_Assertion::$syntactic[$j1]) {
								$correct = $correct && $ac->value === 1.0;
							}
							unset($j1);
						}
						unset($_g3,$_g2);
					}
					if($ac->assertion === com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH) {
						$correct = $correct && $ac->value === 1.0;
					}
					unset($j,$i1,$ac);
				}
			}
		}
		return $correct;
	}
	public function getCompoundComponents() {
		if($this->compoundChecks !== null) {
			$n = -1;
			$it = $this->compoundChecks->keys();
			$compoundGraphicalComponents = new Hash();
			while($it->hasNext()) {
				$key = $it->next();
				if(_hx_index_of($key, "_cg", null) === -1) {
					try {
						$m = Std::parseInt(_hx_substr($key, _hx_index_of($key, "_c", null) + 2, null));
						if($m > $n) {
							$n = $m;
						}
						unset($m);
					}catch(Exception $»e) {
						$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
						$e = $_ex_;
						{
						}
					}
					unset($e);
				} else {
					$answer = _hx_substr($key, 0, _hx_index_of($key, "_cg", null));
					if(!$compoundGraphicalComponents->exists($answer)) {
						$compoundGraphicalComponents->set($answer, "0");
					} else {
						$compoundGraphicalComponents->set($answer, "" . _hx_string_rec((Std::parseInt($compoundGraphicalComponents->get($answer)) + 1), ""));
					}
					unset($answer);
				}
				unset($key);
			}
			$itG = $compoundGraphicalComponents->keys();
			while($itG->hasNext()) {
				$m = Std::parseInt($compoundGraphicalComponents->get($itG->next()));
				if($m > $n) {
					$n = $m;
				}
				unset($m);
			}
			return $n + 1;
		} else {
			return 0;
		}
	}
	public function isNumberPart($c) {
		$parts = new _hx_array(array(".", "-", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"));
		$i = null;
		{
			$_g1 = 0; $_g = $parts->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($parts[$i1] === $c) {
					return true;
				}
				unset($i1);
			}
		}
		return false;
	}
	public function getCompoundGradeDistribution($s) {
		$n = $this->getCompoundComponents();
		$d = new _hx_array(array());
		if($s === null || trim($s) === "") {
			$i = null;
			{
				$_g = 0;
				while($_g < $n) {
					$i1 = $_g++;
					$d[$i1] = 1.0;
					unset($i1);
				}
			}
			$d[$n] = $n;
		} else {
			$content = false;
			$j = 0;
			$l = haxe_Utf8::length($s);
			$i = 0;
			$sb = new StringBuf();
			while($i < $l && $j < $n) {
				$c = com_wiris_quizzes_impl_QuestionInstanceImpl_2($this, $content, $d, $i, $j, $l, $n, $s, $sb);
				$digit = $this->isNumberPart($c);
				if($digit) {
					$sb->add($c);
					$content = true;
				}
				if($content && (!$digit || $i + 1 === $l)) {
					$t = 0.0;
					try {
						$t = Std::parseFloat($sb->b);
					}catch(Exception $»e) {
						$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
						$e = $_ex_;
						{
						}
					}
					$d[$j] = $t;
					$j++;
					$sb = new StringBuf();
					$content = false;
					unset($t,$e);
				}
				$i++;
				unset($digit,$c);
			}
			while($j < $n) {
				$d[$j] = 0.0;
				$j++;
			}
			$sum = 0.0;
			{
				$_g = 0;
				while($_g < $n) {
					$j1 = $_g++;
					$sum += $d[$j1];
					unset($j1);
				}
			}
			$d[$n] = $sum;
		}
		return $d;
	}
	public function getCompoundAnswerGrade($correctAnswer, $studentAnswer, $index, $q) {
		$n = $this->getCompoundComponents();
		if($index < 0 || $index >= $n) {
			throw new HException("Compound answer index out of bounds.");
		}
		$qimpl = $q->getImpl();
		$graphicalAssertionIndex = $qimpl->getAssertionIndex(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_GRAPHIC, _hx_string_rec($correctAnswer, "") . "", _hx_string_rec($studentAnswer, "") . "");
		$checks = null;
		if($graphicalAssertionIndex !== -1) {
			$ca = $qimpl->getCorrectAnswer($correctAnswer);
			$ass = $qimpl->getAssertion($graphicalAssertionIndex);
			$elements = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getElementsToGrade($ca, $ass);
			if($elements !== null && $index < $elements->length) {
				$checks = $this->getCompoundAnswerChecks($correctAnswer, $studentAnswer, $elements[$index] . "", true);
			}
			$noSuperfluousElementsIndex = $qimpl->getAssertionIndex(com_wiris_quizzes_impl_Assertion::$CHECK_NO_SUPERFLUOUS, _hx_string_rec($correctAnswer, "") . "", _hx_string_rec($studentAnswer, "") . "");
			if($checks !== null && $noSuperfluousElementsIndex !== -1) {
				$slotChecks = $this->checks->get(_hx_string_rec($studentAnswer, "") . "");
				{
					$_g = 0;
					while($_g < $slotChecks->length) {
						$c = $slotChecks[$_g];
						++$_g;
						if($c->getAnswer() !== null && $c->getAnswer() === _hx_string_rec($correctAnswer, "") . "" && com_wiris_quizzes_impl_Assertion::$CHECK_NO_SUPERFLUOUS === $c->getAssertionName()) {
							$checks->push($c);
							break;
						}
						unset($c);
					}
				}
			}
		} else {
			$checks = $this->getCompoundAnswerChecks($correctAnswer, $studentAnswer, _hx_string_rec($index, "") . "", false);
		}
		$grade = 0.0;
		if($checks !== null) {
			$grade = $this->prodChecks($checks, -1, -1);
		}
		return $grade;
	}
	public function prodChecks($checks, $correctAnswer, $studentAnswer) {
		$grade = 1.0;
		$i = null;
		{
			$_g1 = 0; $_g = $checks->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(($correctAnswer === -1 || com_wiris_util_type_Arrays::containsArray(_hx_array_get($checks, $i1)->getCorrectAnswers(), "" . _hx_string_rec($correctAnswer, ""))) && ($studentAnswer === -1 || com_wiris_util_type_Arrays::containsArray(_hx_array_get($checks, $i1)->getAnswers(), "" . _hx_string_rec($studentAnswer, "")))) {
					$grade = $grade * _hx_array_get($checks, $i1)->value;
				}
				unset($i1);
			}
		}
		return $grade;
	}
	public function andChecks($checks) {
		$j = null;
		$correct = true;
		{
			$_g = 0;
			while($_g < $checks->length) {
				$check = $checks[$_g];
				++$_g;
				$correct = $correct && $check->value > 0.999999;
				unset($check);
			}
		}
		return $correct;
	}
	public function getCompoundAnswerChecks($correctAnswer, $studentAnswer, $index, $isGraphical) {
		$infix = (($isGraphical) ? "_cg" : "_c");
		if($this->compoundChecks !== null) {
			return $this->compoundChecks->get(_hx_string_rec($studentAnswer, "") . $infix . $index)->get(_hx_string_rec($correctAnswer, "") . $infix . $index);
		} else {
			return null;
		}
	}
	public function getAnswerGrade($correctAnswer, $studentAnswer, $q) {
		$grade = 0.0;
		$question = (($q !== null) ? $q->getImpl() : null);
		if($question !== null && $question->isCompoundAnswer()) {
			$distributionProperty = null;
			$distribute = false;
			if($question->isQuestionCompoundAnswer() && $question->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTE) {
				$distributionProperty = $question->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE_DISTRIBUTION);
				$distribute = true;
			} else {
				$slots = $question->slots;
				{
					$_g = 0;
					while($_g < $slots->length) {
						$s = $slots[$_g];
						++$_g;
						if($studentAnswer === Std::parseInt($s->id) && $s->isSlotCompoundAnswer() && $s->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTE) {
							$distributionProperty = $s->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER_GRADE_DISTRIBUTION);
							$distribute = true;
							break;
						}
						unset($s);
					}
				}
			}
			if($distribute) {
				$distribution = $this->getCompoundGradeDistribution($distributionProperty);
				{
					$_g1 = 0; $_g = $distribution->length - 1;
					while($_g1 < $_g) {
						$i = $_g1++;
						$grade += $distribution->»a[$i] * $this->getCompoundAnswerGrade($correctAnswer, $studentAnswer, $i, $q);
						unset($i);
					}
				}
				return $grade / $distribution[$distribution->length - 1];
			}
		} else {
			if($question !== null && $question->getAssertionIndex(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($studentAnswer, "")) !== -1) {
				$checks = $this->checks->get(_hx_string_rec($studentAnswer, "") . "");
				return $this->prodChecks($checks, $correctAnswer, $studentAnswer);
			}
		}
		return (($this->isAnswerMatching($correctAnswer, $studentAnswer)) ? 1.0 : 0.0);
	}
	public function areAllAnswersCorrect() {
		if($this->checks !== null) {
			$it = $this->checks->keys();
			while($it->hasNext()) {
				$key = $it->next();
				$checks = $this->checks->get($key);
				if(!$this->andChecks($checks)) {
					return false;
				}
				unset($key,$checks);
			}
		}
		return true;
	}
	public function isAnswerCorrect($answer) {
		$correct = true;
		if($this->checks !== null && $this->checks->exists(_hx_string_rec($answer, "") . "")) {
			$checks = $this->checks->get(_hx_string_rec($answer, "") . "");
			$correct = $this->andChecks($checks);
		}
		return $correct;
	}
	public function getMatchingCorrectAnswer($studentAnswer, $q) {
		$correctAnswer = -1;
		if($this->checks !== null && $this->checks->exists(_hx_string_rec($studentAnswer, "") . "")) {
			$checks = $this->checks->get(_hx_string_rec($studentAnswer, "") . "");
			$correctAnswers = new _hx_array(array());
			$i = null;
			{
				$_g1 = 0; $_g = $checks->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$ca = _hx_array_get($checks, $i1)->getCorrectAnswers();
					$j = null;
					{
						$_g3 = 0; $_g2 = $ca->length;
						while($_g3 < $_g2) {
							$j1 = $_g3++;
							com_wiris_util_type_Arrays::insertSortedSet($correctAnswers, $ca[$j1]);
							unset($j1);
						}
						unset($_g3,$_g2);
					}
					unset($j,$i1,$ca);
				}
			}
			if($correctAnswers->length > 0) {
				$correctAnswer = Std::parseInt($correctAnswers[0]);
				$maxgrade = $this->getAnswerGrade($correctAnswer, $studentAnswer, $q);
				$j = null;
				{
					$_g1 = 1; $_g = $correctAnswers->length;
					while($_g1 < $_g) {
						$j1 = $_g1++;
						$thisCorrectAnswer = Std::parseInt($correctAnswers[$j1]);
						$grade = $this->getAnswerGrade($thisCorrectAnswer, $studentAnswer, $q);
						if($grade > $maxgrade) {
							$maxgrade = $grade;
							$correctAnswer = $thisCorrectAnswer;
						}
						unset($thisCorrectAnswer,$j1,$grade);
					}
				}
			}
		}
		return $correctAnswer;
	}
	public function areAllChecksCorrect($slot, $authorAnswer) {
		$slotIndex = Std::parseInt($slot->id);
		$authorAnswerIndex = Std::parseInt($authorAnswer->id);
		return $this->isAnswerMatching($authorAnswerIndex, $slotIndex);
	}
	public function isAnswerMatching($correctAnswer, $answer) {
		$correct = true;
		if($this->checks !== null && $this->checks->exists(_hx_string_rec($answer, "") . "")) {
			$checks = $this->checks->get(_hx_string_rec($answer, "") . "");
			$i = null;
			{
				$_g1 = 0; $_g = $checks->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$c = $checks[$i1];
					if(!(StringTools::startsWith($c->getAssertionName(), "syntax") && ($c->getAnswers()->length > 1 || $c->getCorrectAnswers()->length > 1))) {
						if(Std::parseInt($c->getCorrectAnswer()) === $correctAnswer) {
							$correct = $correct && $c->value > 0.999;
						}
					}
					unset($i1,$c);
				}
			}
		}
		return $correct;
	}
	public function isCacheReady() {
		return $this->areVariablesReady();
	}
	public function hasEvaluation() {
		return $this->checks !== null && $this->checks->keys()->hasNext();
	}
	public function hasVariables() {
		return $this->variables !== null && $this->variables->keys()->hasNext();
	}
	public function clearChecks() {
		$this->checks = null;
		$this->compoundChecks = null;
	}
	public function clearVariables() {
		$this->variables = null;
	}
	public function getBase64Code() {
		if(com_wiris_quizzes_impl_QuestionInstanceImpl::$base64 === null) {
			com_wiris_quizzes_impl_QuestionInstanceImpl::$base64 = new com_wiris_quizzes_impl_Base64();
		}
		return com_wiris_quizzes_impl_QuestionInstanceImpl::$base64;
	}
	public function storeImageVariable($v) {
		$filename = null;
		if(com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
			$filename = haxe_Md5::encode($v->content) . ".b64";
			$s = com_wiris_system_Storage::newStorage($filename);
			if(!$s->exists()) {
				$s->write($v->content);
			}
			if(!$s->exists()) {
				return $v;
			}
		} else {
			$base64 = $this->getBase64Code();
			$value = str_replace("=", "", $v->content);
			$b = $base64->decodeBytes(haxe_io_Bytes::ofString($value));
			$filename = haxe_Md5::encode($value) . ".png";
			$cache = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getImagesCache();
			$cache->set($filename, $b);
		}
		$w = new com_wiris_quizzes_impl_Variable();
		$w->type = com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF;
		$w->content = $filename;
		$w->name = $v->name;
		return $w;
	}
	public function isCompoundAnswer() {
		return $this->compoundChecks !== null;
	}
	public function isCompoundAnswerSingleCheck($check) {
		$id = $check->getCorrectAnswer();
		if($id === null) {
			return false;
		}
		if(_hx_index_of($id, "c", null) > -1) {
			return true;
		}
		$index = Std::parseInt($id);
		return $index >= 1000;
	}
	public function isCompoundAnswerChecks($checks) {
		if($checks !== null && $checks->length > 0) {
			$_g = 0;
			while($_g < $checks->length) {
				$check = $checks[$_g];
				++$_g;
				if($this->isCompoundAnswerSingleCheck($check)) {
					return true;
				}
				unset($check);
			}
		}
		return false;
	}
	public function collapseCompoundAnswerChecks($checks) {
		$this->compoundChecks = new Hash();
		{
			$_g = 0;
			while($_g < $checks->length) {
				$c = $checks[$_g];
				++$_g;
				if(!$this->isCompoundAnswerSingleCheck($c)) {
					continue;
				}
				$correctAnswers = $c->getCorrectAnswers();
				$answers = $c->getAnswers();
				$pairs = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getPairings($c->getCorrectAnswers()->length, $c->getAnswers()->length);
				$j = null;
				{
					$_g2 = 0; $_g1 = $pairs->length;
					while($_g2 < $_g1) {
						$j1 = $_g2++;
						$pair = $pairs[$j1];
						$correctAnswer = $this->updateCompoundId($correctAnswers[$pair[0]]);
						$userAnswer = $this->updateCompoundId($answers[$pair[1]]);
						if(!$this->compoundChecks->exists($userAnswer)) {
							$this->compoundChecks->set($userAnswer, new Hash());
						}
						$answerChecks = $this->compoundChecks->get($userAnswer);
						if(!$answerChecks->exists($correctAnswer)) {
							$answerChecks->set($correctAnswer, new _hx_array(array()));
						}
						$pairchecks = $answerChecks->get($correctAnswer);
						$pairchecks->push($c);
						unset($userAnswer,$pairchecks,$pair,$j1,$correctAnswer,$answerChecks);
					}
					unset($_g2,$_g1);
				}
				$idAnswer = $c->getAnswer();
				if(_hx_index_of($idAnswer, "_c", null) > 0) {
					$c->setAnswer(_hx_substr($idAnswer, 0, _hx_index_of($idAnswer, "_c", null)));
				} else {
					$numAnswer = Std::parseInt($idAnswer);
					if($numAnswer < 1000) {
						$c->setAnswer($idAnswer);
					} else {
						$numAnswer = Math::floor(($numAnswer - 1000) / 1000.0);
						$c->setAnswer("" . _hx_string_rec($numAnswer, ""));
					}
					unset($numAnswer);
				}
				$correctAnswerId = $c->getCorrectAnswer();
				if(_hx_index_of($correctAnswerId, "_c", null) > 0) {
					$c->setCorrectAnswer(_hx_substr($correctAnswerId, 0, _hx_index_of($correctAnswerId, "_c", null)));
				} else {
					$numCA = Std::parseInt($correctAnswerId);
					if($numCA < 1000) {
						$c->setCorrectAnswer($correctAnswerId);
					} else {
						$numCA = Math::floor(($numCA - 1000) / 1000.0);
						$c->setCorrectAnswer("" . _hx_string_rec($numCA, ""));
					}
					unset($numCA);
				}
				unset($pairs,$j,$idAnswer,$correctAnswers,$correctAnswerId,$c,$answers);
			}
		}
	}
	public function compare($a, $b) {
		return ((Std::parseInt($a->get("ordinal")) > Std::parseInt($b->get("ordinal"))) ? 1 : -1);
	}
	public function parseCompoundGraphicalAssertionChecks($checks) {
		$parsedChecks = new _hx_array(array());
		$assertionsInfo = new Hash();
		{
			$_g = 0;
			while($_g < $checks->length) {
				$c = $checks[$_g];
				++$_g;
				if(_hx_index_of($c->getCorrectAnswer(), "cg", null) === -1) {
					$parsedChecks->push($c);
					continue;
				}
				$strs = _hx_explode("_", $c->getCorrectAnswer());
				$correctAnswer = $strs[0];
				$elemCount = _hx_substr($strs[1], 2, null);
				$elemIdParts = new _hx_array(array());
				{
					$_g2 = 2; $_g1 = $strs->length;
					while($_g2 < $_g1) {
						$i = $_g2++;
						$elemIdParts->push($strs[$i]);
						unset($i);
					}
					unset($_g2,$_g1);
				}
				$elemId = $elemIdParts->join("_");
				$answer = $c->getAnswer();
				if(!$assertionsInfo->exists($c->assertion . "_" . $correctAnswer . "_" . $answer)) {
					$assertionsInfo->set($c->assertion . "_" . $correctAnswer . "_" . $answer, new _hx_array(array()));
				}
				$piece = new Hash();
				$piece->set("elemId", $elemId);
				$piece->set("ordinal", $elemCount);
				$piece->set("grade", _hx_string_rec($c->value, "") . "");
				$piece->set("correctAnswer", $correctAnswer);
				$piece->set("answer", $answer);
				$assertionsInfo->get($c->assertion . "_" . $correctAnswer . "_" . $answer)->push($piece);
				unset($strs,$piece,$elemIdParts,$elemId,$elemCount,$correctAnswer,$c,$answer);
			}
		}
		$it = $assertionsInfo->keys();
		while($it->hasNext()) {
			$assertion = $it->next();
			$pieces = $assertionsInfo->get($assertion);
			$correctAnswer = _hx_array_get($pieces, 0)->get("correctAnswer");
			$answer = _hx_array_get($pieces, 0)->get("answer");
			com_wiris_util_type_Arrays::sort($pieces, $this);
			$grade_prev = 0.0;
			{
				$_g1 = 0; $_g = $pieces->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					$piece = $pieces[$i];
					$grade = Std::parseFloat($piece->get("grade"));
					$elemId = $piece->get("elemId");
					$c = new com_wiris_quizzes_impl_AssertionCheckImpl();
					$c->assertion = $assertion;
					$c->value = (($grade > 0.99 || $grade > $grade_prev) ? 1 : 0);
					$c->setAnswer($answer . "_cg" . $elemId);
					$c->setCorrectAnswer($correctAnswer . "_cg" . $elemId);
					$grade_prev = $grade;
					$parsedChecks->push($c);
					unset($piece,$i,$grade,$elemId,$c);
				}
				unset($_g1,$_g);
			}
			$cc = new com_wiris_quizzes_impl_AssertionCheckImpl();
			$cc->assertion = $assertion;
			$cc->value = (($grade_prev > 0.99) ? 1 : 0);
			$cc->setAnswer($answer);
			$cc->setCorrectAnswer($correctAnswer);
			$parsedChecks->push($cc);
			unset($pieces,$grade_prev,$correctAnswer,$cc,$assertion,$answer);
		}
		return $parsedChecks;
	}
	public function isCompoundGraphicalAnswerChecks($checks) {
		if($checks !== null) {
			$_g = 0;
			while($_g < $checks->length) {
				$check = $checks[$_g];
				++$_g;
				if(_hx_index_of($check->getCorrectAnswer(), "cg", null) === -1) {
					return true;
				}
				unset($check);
			}
		}
		return false;
	}
	public function updateCompoundId($id) {
		if(_hx_index_of($id, "_c", null) > -1) {
			return $id;
		}
		$num = Std::parseInt($id);
		if($num < 1000) {
			return $id;
		}
		$index = Math::floor(($num - 1000) / 1000.0);
		$compoundIndex = _hx_mod($num, 1000);
		return _hx_string_rec($index, "") . "_c" . _hx_string_rec($compoundIndex, "");
	}
	public function hasHandwritingConstraints() {
		return $this->handConstraints !== null || $this->getLocalDataImpl(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS) !== null || $this->question !== null;
	}
	public function update($response) {
		$questionResponseImpl = $response;
		if($questionResponseImpl !== null && $questionResponseImpl->results !== null) {
			$variables = false;
			$checks = false;
			$i = null;
			{
				$_g1 = 0; $_g = $questionResponseImpl->results->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$result = $questionResponseImpl->results[$i1];
					$s = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getSerializer();
					$tag = $s->getTagName($result);
					$j = null;
					if($tag === com_wiris_quizzes_impl_ResultGetVariables::$tagName) {
						$variables = true;
						$variablesResult = $result;
						$resultVars = $variablesResult->variables;
						{
							$_g3 = 0; $_g2 = $resultVars->length;
							while($_g3 < $_g2) {
								$j1 = $_g3++;
								$variable = $resultVars[$j1];
								if($variable->type === com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE) {
									$resultVars[$j1] = $this->storeImageVariable($variable);
								}
								unset($variable,$j1);
							}
							unset($_g3,$_g2);
						}
						$this->variables = $this->variablesToHash($variablesResult->variables, $this->variables);
						unset($variablesResult,$resultVars);
					} else {
						if($tag === com_wiris_quizzes_impl_ResultGetCheckAssertions::$tagName) {
							if(!$checks) {
								$checks = true;
								$this->checks = null;
							}
							$assertionsResult = $result;
							$resultChecks = $assertionsResult->checks;
							if($this->isCompoundGraphicalAnswerChecks($resultChecks)) {
								$resultChecks = $this->parseCompoundGraphicalAssertionChecks($resultChecks);
							}
							if($this->isCompoundAnswerChecks($resultChecks)) {
								$this->collapseCompoundAnswerChecks($resultChecks);
							}
							$this->checks = $this->checksToHash($resultChecks, $this->checks);
							unset($resultChecks,$assertionsResult);
						}
					}
					unset($tag,$s,$result,$j,$i1);
				}
			}
			if($variables) {
				if($this->hasHandwritingConstraints()) {
					$this->getHandwritingConstraints()->addQuestionInstanceConstraints($this);
				}
			}
		}
	}
	public function expandVariablesTextEval($text) {
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$h->setPlotterLoadingSrc(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png"));
		$h->setProxyUrl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL));
		if($this->variables === null || $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT_EVAL) === null) {
			return $this->expandVariablesText($text);
		} else {
			$newvars = new Hash();
			$this->addAllHashElements($this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT), $newvars);
			$this->addAllHashElements($this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT_EVAL), $newvars);
			return $h->expandVariablesText($text, $newvars);
		}
	}
	public function expandVariablesText($text) {
		if($text === null) {
			return null;
		}
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$h->setAnswerKeyword($this->getAnswerParameterName());
		$h->setPlotterLoadingSrc(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png"));
		$h->setProxyUrl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL));
		$text = $h->extractActionExpressions($text, null);
		if(com_wiris_quizzes_impl_MathContent::getMathType($text) === com_wiris_quizzes_impl_MathContent::$TYPE_MATHML) {
			$text = $h->mathMLToText($text);
		}
		if($this->variables !== null && $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) !== null) {
			$textvars = $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
			$text = $h->expandVariablesText($text, $textvars);
		}
		if($this->userData->answers !== null) {
			$text = $h->expandAnswersText($text, $this->userData->answers, $this->isCompoundAnswer());
		}
		return $text;
	}
	public function addAllHashElements($src, $dest) {
		if($src !== null) {
			$it = $src->keys();
			while($it->hasNext()) {
				$name = $it->next();
				$dest->set($name, $src->get($name));
				unset($name);
			}
		}
	}
	public function expandVariablesMathMLEval($equation) {
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$h->setPlotterLoadingSrc(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png"));
		$h->setProxyUrl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL));
		if($this->variables === null || $this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML_EVAL) === null) {
			return $this->expandVariablesMathML($equation);
		} else {
			$vars = new Hash();
			$newvars = new Hash();
			$vars->set(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML, $newvars);
			$this->addAllHashElements($this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML), $newvars);
			$this->addAllHashElements($this->variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML_EVAL), $newvars);
			if(com_wiris_quizzes_impl_MathContent::getMathType($equation) === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) {
				$equation = $h->textToMathML($equation);
			}
			return $h->expandVariables($equation, $vars);
		}
	}
	public function getAnswerParameterName() {
		if($this->question === null || !("true" === $this->question->getProperty(com_wiris_quizzes_api_PropertyName::$STUDENT_ANSWER_PARAMETER))) {
			return null;
		}
		$keyword = $this->question->getProperty(com_wiris_quizzes_api_PropertyName::$STUDENT_ANSWER_PARAMETER_NAME);
		if($keyword === $this->question->getImpl()->defaultOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME)) {
			$lang = com_wiris_quizzes_impl_CalcDocumentTools::casSessionLang($this->question->getAlgorithm());
			if($lang !== null && !($lang === com_wiris_quizzes_impl_QuestionInstanceImpl::$DEF_ALGORITHM_LANGUAGE)) {
				$keyword = com_wiris_quizzes_impl_QuizzesTranslator::getInstance($lang)->t($keyword);
			}
		}
		return $keyword;
	}
	public function expandVariablesMathML($equation) {
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$h->setPlotterLoadingSrc(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png"));
		$h->setProxyUrl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL));
		$equation = $h->extractActionExpressions($equation, null);
		if(com_wiris_quizzes_impl_MathContent::getMathType($equation) === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) {
			$equation = $h->textToMathML($equation);
		}
		$h->setAnswerKeyword($this->getAnswerParameterName());
		$equation = $h->expandVariables($equation, $this->variables);
		$equation = $h->expandAnswers($equation, $this->userData->answers, $this->isCompoundAnswer());
		return $equation;
	}
	public function expandVariables($text) {
		if($text === null) {
			return null;
		}
		$h = new com_wiris_quizzes_impl_HTMLTools();
		$h->setItemSeparator($this->getItemSeparator());
		$h->setAnswerKeyword($this->getAnswerParameterName());
		$h->setPlotterLoadingSrc(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png"));
		$h->setProxyUrl(com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL));
		$text = $h->expandVariables($text, $this->variables);
		$h->setAnswerKeyword($this->getAnswerParameterName());
		$text = $h->expandAnswers($text, $this->userData->answers, $this->isCompoundAnswer());
		return $text;
	}
	public function getItemSeparator() {
		if($this->question !== null) {
			$itemSeparator = $this->question->getImpl()->getItemSeparator();
			if($itemSeparator !== null) {
				return $itemSeparator;
			}
		}
		return ",";
	}
	public function defaultLocalData($name) {
		return null;
	}
	public function getLocalDataImpl($name) {
		if($this->localData !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $this->localData->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(_hx_array_get($this->localData, $i1)->name === $name) {
						return _hx_array_get($this->localData, $i1)->value;
					}
					unset($i1);
				}
			}
		}
		return $this->defaultLocalData($name);
	}
	public function getProperty($name) {
		$pname = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		return (($pname !== null) ? $this->getLocalData($pname) : null);
	}
	public function setProperty($name, $value) {
		$pname = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		if($name !== null) {
			$this->setLocalData($pname, $value);
		}
	}
	public function getLocalData($name) {
		if($name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS) {
			if($this->hasHandwritingConstraints()) {
				return $this->getHandwritingConstraints()->getNegativeConstraints()->toJSON();
			} else {
				return null;
			}
		}
		return $this->getLocalDataImpl($name);
	}
	public function startMultiStepSession() {
		$q = $this->question;
		$slots = $q->getSlots();
		$hasMultistep = false;
		{
			$_g = 0;
			while($_g < $slots->length) {
				$s = $slots[$_g];
				++$_g;
				if($s->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
					$hasMultistep = true;
					break;
				}
				unset($s);
			}
		}
		if($hasMultistep) {
			$listener = new com_wiris_quizzes_impl_MultistepSessionStartListener($this);
			$payload = new Hash();
			$payload->set("question", $q->serialize());
			$payload->set("randomSeed", $this->userData->randomSeed);
			$path = "multistep/start/v5";
			$http = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getHttpObject($listener, com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$API_URL) . "/" . $path, new com_wiris_quizzes_impl_ServiceProxyRoute("api", $path), com_wiris_util_json_JSon::encode($payload), "application/json");
			$http->setAsync(false);
			$http->request(true);
		}
	}
	public function setLocalDataImpl($name, $value, $parseHandwritingConstraints) {
		if($this->localData === null) {
			$this->localData = new _hx_array(array());
		}
		$data = new com_wiris_quizzes_impl_LocalData();
		$data->name = $name;
		$data->value = $value;
		$i = null;
		$found = false;
		{
			$_g1 = 0; $_g = $this->localData->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_array_get($this->localData, $i1)->name === $name) {
					$this->localData[$i1] = $data;
					$found = true;
				}
				unset($i1);
			}
		}
		if(!$found) {
			$this->localData->push($data);
		}
		if($parseHandwritingConstraints && $name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_HANDWRITING_CONSTRAINTS) {
			$this->handConstraints = com_wiris_quizzes_impl_HandwritingConstraints::readHandwritingConstraints($value);
		}
	}
	public function setLocalData($name, $value) {
		$this->setLocalDataImpl($name, $value, true);
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_QuestionInstanceImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_QuestionInstanceImpl::$tagName);
		$this->userData = $s->serializeChildName($this->userData, com_wiris_quizzes_impl_UserData::$TAGNAME);
		$this->setChecksCompoundAnswers();
		$a = $s->serializeArrayName($this->hashToChecks($this->checks), "checks");
		if($this->isCompoundAnswerChecks($a)) {
			$this->collapseCompoundAnswerChecks($a);
		}
		$this->checks = $this->checksToHash($a, null);
		$this->variables = $this->variablesToHash($s->serializeArrayName($this->hashToVariables($this->variables, null), "variables"), null);
		$this->serializeHandConstraints();
		$this->localData = $s->serializeArrayName($this->localData, "localData");
		$s->endTag();
	}
	public $handConstraints;
	public $question;
	public $compoundChecks;
	public $localData;
	public $checks;
	public $variables;
	public $userData;
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
	static $tagName = "questionInstance";
	static $base64;
	static $DEF_ALGORITHM_LANGUAGE = "en";
	static $KEY_ALGORITHM_LANGUAGE = "sessionLang";
	function __toString() { return 'com.wiris.quizzes.impl.QuestionInstanceImpl'; }
}
com_wiris_quizzes_impl_QuestionInstanceImpl::$__meta__ = _hx_anonymous(array("fields" => _hx_anonymous(array("getCompoundAnswerGrade" => _hx_anonymous(array("Deprecated" => null)), "getAnswerGrade" => _hx_anonymous(array("Deprecated" => null)), "getMatchingCorrectAnswer" => _hx_anonymous(array("Deprecated" => null)), "isAnswerMatching" => _hx_anonymous(array("Deprecated" => null))))));
function com_wiris_quizzes_impl_QuestionInstanceImpl_0(&$»this) {
	if($»this->userData->answers !== null) {
		return $»this->userData->answers->length;
	} else {
		return 0;
	}
}
function com_wiris_quizzes_impl_QuestionInstanceImpl_1(&$»this, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(245);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_QuestionInstanceImpl_2(&$»this, &$content, &$d, &$i, &$j, &$l, &$n, &$s, &$sb) {
	{
		$s1 = new haxe_Utf8(null);
		$s1->addChar(haxe_Utf8::charCodeAt($s, $i));
		return $s1->toString();
	}
}

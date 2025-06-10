<?php

class com_wiris_quizzes_impl_QuestionImpl extends com_wiris_quizzes_impl_QuestionInternal {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->deprecationWarnings = new _hx_array(array());
		if(com_wiris_quizzes_impl_QuestionImpl::$defaultOptions === null) {
			com_wiris_quizzes_impl_QuestionImpl::$defaultOptions = com_wiris_quizzes_impl_QuestionImpl::getDefaultOptions();
		}
	}}
	public function getDeprecationWarnings() {
		return $this->deprecationWarnings;
	}
	public function clearDeprecationWarnings() {
		$this->deprecationWarnings = new _hx_array(array());
	}
	public function getAvailableRefId() {
		$i = 1;
		{
			$_g = 0; $_g1 = $this->slots;
			while($_g < $_g1->length) {
				$s = $_g1[$_g];
				++$_g;
				if($s->syntax->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
					$i += Std::parseInt($s->syntax->getParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$REF_ID));
				}
				unset($s);
			}
		}
		return $i;
	}
	public function removeCorrectAnswer($index) {
		$_g = 0; $_g1 = $this->slots;
		while($_g < $_g1->length) {
			$s = $_g1[$_g];
			++$_g;
			$aaa = $s->authorAnswers;
			{
				$_g2 = 0;
				while($_g2 < $aaa->length) {
					$aa = $aaa[$_g2];
					++$_g2;
					$aaIndex = Std::parseInt($aa->id);
					if($aaIndex === $index) {
						$s->removeAuthorAnswer($aa);
						return;
					}
					unset($aaIndex,$aa);
				}
				unset($_g2);
			}
			unset($s,$aaa);
		}
	}
	public function prepareForSlots() {
		$correctAnswersToSlots = new Hash();
		$assertionCorrectAnswerIndexes = new _hx_array(array());
		$assertionAnswerIndexes = new _hx_array(array());
		{
			$_g = 0; $_g1 = $this->assertions;
			while($_g < $_g1->length) {
				$a = $_g1[$_g];
				++$_g;
				if($a->answer !== null && $a->answer->length >= 2) {
					return false;
				}
				$slotIndex = $a->getAnswer();
				$slotIndexI = Std::parseInt($slotIndex);
				if(!com_wiris_system_ArrayEx::contains($assertionAnswerIndexes, $slotIndexI)) {
					$assertionAnswerIndexes->push($slotIndexI);
				}
				$correctAnswers = $a->getCorrectAnswers();
				{
					$_g2 = 0;
					while($_g2 < $correctAnswers->length) {
						$caIndex = $correctAnswers[$_g2];
						++$_g2;
						if($correctAnswersToSlots->exists($caIndex) && !($correctAnswersToSlots->get($caIndex) === $slotIndex)) {
							return false;
						}
						$caIndexI = Std::parseInt($caIndex);
						if(!com_wiris_system_ArrayEx::contains($assertionCorrectAnswerIndexes, $caIndexI)) {
							$assertionCorrectAnswerIndexes->push($caIndexI);
						}
						$correctAnswersToSlots->set($caIndex, $slotIndex);
						unset($caIndexI,$caIndex);
					}
					unset($_g2);
				}
				unset($slotIndexI,$slotIndex,$correctAnswers,$a);
			}
		}
		$questionCorrectAnswerIndexes = new _hx_array(array());
		if($this->correctAnswers !== null) {
			$_g = 0; $_g1 = $this->correctAnswers;
			while($_g < $_g1->length) {
				$ca = $_g1[$_g];
				++$_g;
				$id = Std::parseInt($ca->id);
				if(!com_wiris_system_ArrayEx::contains($questionCorrectAnswerIndexes, $id)) {
					$questionCorrectAnswerIndexes->push($id);
				}
				unset($id,$ca);
			}
		} else {
			$this->correctAnswers = new _hx_array(array());
		}
		{
			$_g = 0;
			while($_g < $questionCorrectAnswerIndexes->length) {
				$index = $questionCorrectAnswerIndexes[$_g];
				++$_g;
				if(!com_wiris_system_ArrayEx::contains($assertionCorrectAnswerIndexes, $index)) {
					$bound = false;
					$answerIndex = $assertionAnswerIndexes[$assertionAnswerIndexes->length - 1];
					while($answerIndex >= 0 && !$bound) {
						{
							$_g1 = 0; $_g2 = $this->assertions;
							while($_g1 < $_g2->length) {
								$assertion = $_g2[$_g1];
								++$_g1;
								if($assertion->isSyntactic() && $assertion->hasAnswer(_hx_string_rec($answerIndex, "") . "")) {
									$assertion->addCorrectAnswer(_hx_string_rec($index, "") . "");
									$bound = true;
								}
								unset($assertion);
							}
							unset($_g2,$_g1);
						}
						$answerIndex--;
					}
					if(!$bound) {
						$a = com_wiris_quizzes_impl_SyntaxAssertion::getDefaultSyntax();
						$a->setCorrectAnswer(_hx_string_rec($index, "") . "");
						$a->setAnswer(_hx_string_rec($index, "") . "");
						$this->assertions->push($a);
						unset($a);
					}
					unset($bound,$answerIndex);
				}
				unset($index);
			}
		}
		{
			$_g = 0;
			while($_g < $assertionCorrectAnswerIndexes->length) {
				$index = $assertionCorrectAnswerIndexes[$_g];
				++$_g;
				if(!com_wiris_system_ArrayEx::contains($questionCorrectAnswerIndexes, $index)) {
					$ca = new com_wiris_quizzes_impl_CorrectAnswer();
					$ca->id = _hx_string_rec($index, "") . "";
					$ca->set("");
					$this->correctAnswers->insert($index, $ca);
					unset($ca);
				}
				unset($index);
			}
		}
		return true;
	}
	public function ensureSlotsAndAuthorAnswersHaveReferences() {
		if($this->slots === null) {
			return;
		}
		{
			$_g = 0; $_g1 = $this->slots;
			while($_g < $_g1->length) {
				$s = $_g1[$_g];
				++$_g;
				$s->question = $this;
				if($s->authorAnswers !== null) {
					$authorAnswers = $s->authorAnswers;
					{
						$_g2 = 0;
						while($_g2 < $authorAnswers->length) {
							$aa = $authorAnswers[$_g2];
							++$_g2;
							$aa->question = $this;
							$aa->slot = $s;
							unset($aa);
						}
						unset($_g2);
					}
					unset($authorAnswers);
				}
				unset($s);
			}
		}
	}
	public function getSlotCorrectAnswersIds($slot) {
		$aa = $slot->authorAnswers;
		$correctAnswers = new _hx_array(array());
		{
			$_g1 = 0; $_g = $aa->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$correctAnswers[$i] = _hx_array_get($aa, $i)->id;
				unset($i);
			}
		}
		return $correctAnswers;
	}
	public function assertionIndex($a) {
		{
			$_g1 = 0; $_g = $this->assertions->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($this->assertions[$i] === $a) {
					return $i;
				}
				unset($i);
			}
		}
		return -1;
	}
	public function updateSlotsImpl($overrideDeprecated) {
		if($this->slots === null) {
			$this->slots = new _hx_array(array());
		}
		if($this->assertions === null || $this->assertions->length === 0) {
			if($this->correctAnswers === null || $this->correctAnswers->length === 0) {
				return;
			}
			{
				$_g = 0; $_g1 = $this->correctAnswers;
				while($_g < $_g1->length) {
					$ca = $_g1[$_g];
					++$_g;
					$s = $this->addNewSlot();
					$aa = com_wiris_quizzes_impl_AuthorAnswerImpl::newWithQuestionCallback($this, $s);
					$aa->value = $ca;
					$aa->id = $ca->id;
					$s->syntax->setCorrectAnswer($aa->id);
					$s->authorAnswers->push($aa);
					$this->assertionAdded($aa->comparison, $aa->id, $s->id);
					unset($s,$ca,$aa);
				}
			}
			return;
		}
		if(!$this->prepareForSlots() || $this->isDeprecated() === com_wiris_quizzes_impl_QuestionImpl::$DEPRECATED_NEEDS_CHECK && !$overrideDeprecated) {
			$this->slots = null;
			return;
		}
		$this->importDeprecated();
		$assertionsCopy = new _hx_array(array());
		{
			$_g = 0; $_g1 = $this->assertions;
			while($_g < $_g1->length) {
				$a = $_g1[$_g];
				++$_g;
				$assertionsCopy->push($a);
				unset($a);
			}
		}
		{
			$_g1 = 0; $_g = $assertionsCopy->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$a = $assertionsCopy[$i];
				$slotId = Std::parseInt($a->getAnswer());
				$correctAnswers = $a->getCorrectAnswers();
				while($slotId >= $this->slots->length) {
					$localData = com_wiris_quizzes_impl_QuestionImpl_0($this, $_g, $_g1, $a, $assertionsCopy, $correctAnswers, $i, $overrideDeprecated, $slotId);
					$slot = $this->addNewSlot();
					$slot->syntax->setCorrectAnswers($correctAnswers);
					if($localData !== null) {
						$slot->localData = new _hx_array(array());
						{
							$_g2 = 0;
							while($_g2 < $localData->length) {
								$datum = $localData[$_g2];
								++$_g2;
								$newDatum = new com_wiris_quizzes_impl_LocalData();
								$newDatum->name = $datum->name;
								$newDatum->value = $datum->value;
								$slot->localData->push($newDatum);
								unset($newDatum,$datum);
							}
							unset($_g2);
						}
					}
					unset($slot,$localData);
				}
				$slot = $this->slots[$slotId];
				{
					$_g2 = 0;
					while($_g2 < $correctAnswers->length) {
						$caIndex = $correctAnswers[$_g2];
						++$_g2;
						$authorAnswers = $slot->authorAnswers;
						$correctAnswer = null;
						{
							$_g3 = 0;
							while($_g3 < $authorAnswers->length) {
								$authorAnswer = $authorAnswers[$_g3];
								++$_g3;
								if($caIndex === $authorAnswer->id) {
									$correctAnswer = $authorAnswer;
									break;
								}
								unset($authorAnswer);
							}
							unset($_g3);
						}
						if($correctAnswer === null) {
							$correctAnswer = com_wiris_quizzes_impl_AuthorAnswerImpl::newWithQuestionCallback($this, $slot);
							$correctAnswer->id = $caIndex;
							$correctAnswer->comparison->setAnswer(_hx_string_rec($slotId, "") . "");
							$correctAnswer->comparison->setCorrectAnswer($caIndex);
							$correctAnswer->value = $this->correctAnswers[Std::parseInt($caIndex)];
							$this->assertions->push($correctAnswer->comparison);
							$slot->authorAnswers->push($correctAnswer);
						}
						if(Std::is($a, _hx_qtype("com.wiris.quizzes.impl.ComparisonAssertion"))) {
							if($correctAnswer->comparison !== $a) {
								$this->assertionRemoved($correctAnswer->comparison);
								$correctAnswer->comparison = $a;
							}
						} else {
							if(Std::is($a, _hx_qtype("com.wiris.quizzes.impl.ValidationAssertion"))) {
								if($correctAnswer->getValidation($a->getName()) === null) {
									$correctAnswer->validations->push($a);
								} else {
									if($correctAnswer->getValidation($a->getName()) != $a) {
										$_g4 = 0; $_g3 = $correctAnswer->validations->length;
										while($_g4 < $_g3) {
											$j = $_g4++;
											$v = $correctAnswer->validations[$j];
											if($v->name === $a->name) {
												$this->assertionRemoved($correctAnswer->validations[$j]);
												$correctAnswer->validations[$j] = $a;
											}
											unset($v,$j);
										}
										unset($_g4,$_g3);
									}
								}
							} else {
								if($a->isEquivalence()) {
									$comparisonAssertion = com_wiris_quizzes_impl_ComparisonAssertion::fromAssertion($a);
									$this->assertionRemoved($correctAnswer->comparison);
									$correctAnswer->comparison = $comparisonAssertion;
									$index = $this->assertionIndex($a);
									if($index !== -1) {
										$this->assertions[$index] = $comparisonAssertion;
									} else {
										$this->assertions->push($comparisonAssertion);
									}
									$a = $comparisonAssertion;
									unset($index,$comparisonAssertion);
								} else {
									if($a->isCheck() || $a->isStructure()) {
										$validationAssertion = com_wiris_quizzes_impl_ValidationAssertion::fromAssertion($a);
										if($correctAnswer->getValidation($validationAssertion->getName()) === null) {
											$correctAnswer->validations->push($validationAssertion);
										} else {
											$_g4 = 0; $_g3 = $correctAnswer->validations->length;
											while($_g4 < $_g3) {
												$j = $_g4++;
												$v = $correctAnswer->validations[$j];
												if($v->name === $validationAssertion->name) {
													$this->assertionRemoved($correctAnswer->validations[$j]);
													$correctAnswer->validations[$j] = $validationAssertion;
												}
												unset($v,$j);
											}
											unset($_g4,$_g3);
										}
										$index = $this->assertionIndex($a);
										if($index !== -1) {
											$this->assertions[$index] = $validationAssertion;
										} else {
											$this->assertions->push($validationAssertion);
										}
										$a = $validationAssertion;
										unset($validationAssertion,$index);
									}
								}
							}
						}
						unset($correctAnswer,$caIndex,$authorAnswers);
					}
					unset($_g2);
				}
				if(Std::is($a, _hx_qtype("com.wiris.quizzes.impl.SyntaxAssertion"))) {
					if($slot->syntax != $a) {
						$this->assertionRemoved($slot->syntax);
						$slot->syntax = $a;
					}
					$slot->syntax->setCorrectAnswers($this->getSlotCorrectAnswersIds($slot));
				} else {
					if($a->isSyntactic()) {
						$syntaxAssertion = com_wiris_quizzes_impl_SyntaxAssertion::fromAssertion($a);
						$this->assertionRemoved($slot->syntax);
						$slot->syntax = $syntaxAssertion;
						$slot->syntax->setCorrectAnswers($this->getSlotCorrectAnswersIds($slot));
						$index = $this->assertionIndex($a);
						if($index !== -1) {
							$this->assertions[$index] = $syntaxAssertion;
						} else {
							$this->assertions->push($syntaxAssertion);
						}
						$a = $syntaxAssertion;
						unset($syntaxAssertion,$index);
					}
				}
				unset($slotId,$slot,$i,$correctAnswers,$a);
			}
		}
		$this->ensureSlotsAndAuthorAnswersHaveReferences();
	}
	public function forceSlotStructure() {
		$this->updateSlotsImpl(true);
	}
	public function updateSlots() {
		$this->updateSlotsImpl(false);
	}
	public function assertionRemoved($assertion) {
		if($assertion === null) {
			return;
		}
		$this->id = null;
		$this->assertions->remove($assertion);
	}
	public function assertionAdded($assertion, $correctAnswerId, $userAnswerId) {
		if($assertion === null) {
			return;
		} else {
			if($this->assertions === null) {
				$this->assertions = new _hx_array(array());
			}
		}
		$this->id = null;
		if($correctAnswerId !== null) {
			$assertion->setCorrectAnswer($correctAnswerId);
		}
		if($userAnswerId !== null) {
			$assertion->setAnswer($userAnswerId);
		}
		$name = $assertion->name;
		$correctAnswer = $assertion->getCorrectAnswer();
		$userAnswer = $assertion->getAnswer();
		$index = $this->getAssertionIndex($name, $correctAnswer, $userAnswer);
		if($index === -1) {
			$this->assertions->push($assertion);
		} else {
			$this->assertions[$index] = $assertion;
		}
	}
	public function authorAnswerRemoved($authorAnswer) {
		$this->id = null;
		$this->assertionRemoved($authorAnswer->comparison);
		$validations = $authorAnswer->validations;
		{
			$_g = 0;
			while($_g < $validations->length) {
				$a = $validations[$_g];
				++$_g;
				$this->assertionRemoved($a);
				unset($a);
			}
		}
		$index = Std::parseInt($authorAnswer->id);
		$this->correctAnswers->remove($this->correctAnswers[$index]);
		{
			$_g = 0; $_g1 = $this->slots;
			while($_g < $_g1->length) {
				$s = $_g1[$_g];
				++$_g;
				$aaa = $s->authorAnswers;
				$ids = new _hx_array(array());
				$i = 0;
				{
					$_g2 = 0;
					while($_g2 < $aaa->length) {
						$aa = $aaa[$_g2];
						++$_g2;
						$aaIndex = Std::parseInt($aa->id);
						if($aaIndex > $index) {
							$aaIndex--;
							$aa->id = _hx_string_rec($aaIndex, "") . "";
							$aa->value->id = $aa->id;
							$aa->comparison->setCorrectAnswer($aa->id);
							$vaa = $aa->validations;
							{
								$_g3 = 0;
								while($_g3 < $vaa->length) {
									$va = $vaa[$_g3];
									++$_g3;
									$va->setCorrectAnswer($aa->id);
									unset($va);
								}
								unset($_g3);
							}
							unset($vaa);
						}
						$ids[$i++] = $aa->id;
						unset($aaIndex,$aa);
					}
					unset($_g2);
				}
				$s->syntax->setCorrectAnswers($ids);
				unset($s,$ids,$i,$aaa);
			}
		}
	}
	public function authorAnswerAdded($authorAnswer, $slot) {
		$this->id = null;
		if($this->correctAnswers === null) {
			$this->correctAnswers = new _hx_array(array());
		}
		$index = $this->correctAnswers->length;
		$authorAnswer->id = _hx_string_rec($index, "") . "";
		$authorAnswer->value->id = _hx_string_rec($index, "") . "";
		$this->correctAnswers->push($authorAnswer->value);
		$this->assertionAdded($authorAnswer->comparison, $authorAnswer->id, $slot->id);
		$slot->syntax->addCorrectAnswer(_hx_string_rec($index, "") . "");
		if($authorAnswer->validations !== null && $authorAnswer->validations->length > 0) {
			$validationAssertions = $authorAnswer->validations;
			{
				$_g = 0;
				while($_g < $validationAssertions->length) {
					$validation = $validationAssertions[$_g];
					++$_g;
					$this->assertionAdded($validation, $authorAnswer->id, $slot->id);
					unset($validation);
				}
			}
		}
	}
	public function removeSlot($slot) {
		$this->id = null;
		if($this->slots === null) {
			return;
		}
		$impl = $slot;
		$this->slots->remove($impl);
		$this->assertionRemoved($impl->syntax);
		$aaa = $impl->authorAnswers;
		{
			$_g = 0;
			while($_g < $aaa->length) {
				$aa = $aaa[$_g];
				++$_g;
				$this->authorAnswerRemoved($aa);
				unset($aa);
			}
		}
	}
	public function addSlotImpl($slot) {
		$this->id = null;
		if($this->slots === null) {
			$this->slots = new _hx_array(array());
		}
		$slot->id = _hx_string_rec($this->slots->length, "") . "";
		$this->slots->push($slot);
		$this->assertionAdded($slot->syntax, null, $slot->id);
	}
	public function addNewSlotFromModel($slot) {
		$newSlot = $this->addNewSlot();
		if($slot !== null) {
			$newSlot->copyData($slot, true);
		}
		return $newSlot;
	}
	public function addNewSlot() {
		$slot = com_wiris_quizzes_impl_SlotImpl::newWithQuestionCallback($this);
		$this->addSlotImpl($slot);
		return $slot;
	}
	public function getSlots() {
		if($this->slots === null) {
			return new _hx_array(array());
		}
		$slotsArray = new _hx_array(array());
		$slotsArray = $this->slots->copy();
		return $slotsArray;
	}
	public function importQuestion($question) {
		$this->id = $question->id;
		$this->wirisCasSession = $question->wirisCasSession;
		$this->options = $question->options;
		$this->correctAnswers = $question->correctAnswers;
		$this->assertions = $question->assertions;
		$this->localData = $question->localData;
		$this->slots = $question->slots;
	}
	public function getAssertion($i) {
		return $this->assertions[$i];
	}
	public function getAssertionsLength() {
		return com_wiris_quizzes_impl_QuestionImpl_1($this);
	}
	public function getProperty($name) {
		$key = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		if(com_wiris_util_type_Arrays::containsArray(com_wiris_quizzes_impl_LocalData::$keys, $key)) {
			return $this->getLocalData($key);
		} else {
			return $this->getOption($key);
		}
	}
	public function setProperty($name, $value) {
		$key = com_wiris_quizzes_impl_QuizzesEnumUtils::propertyName2String($name);
		if(com_wiris_util_type_Arrays::containsArray(com_wiris_quizzes_impl_LocalData::$keys, $key)) {
			$this->setLocalData($key, $value);
		} else {
			$this->setOption($key, $value);
		}
	}
	public function moveAnswers($correct, $user) {
		$this->id = null;
		$i = null;
		$answers = new _hx_array(array());
		{
			$_g1 = 0; $_g = $correct->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($i1 !== $correct[$i1]) {
					$answers[$i1] = $this->getCorrectAnswer($correct[$i1]);
					if($answers[$i1] === null) {
						$answers[$i1] = "";
					}
				}
				unset($i1);
			}
		}
		{
			$_g1 = 0; $_g = $correct->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($correct[$i1] !== $i1) {
					$this->setCorrectAnswer($i1, $answers[$i1]);
				}
				unset($i1);
			}
		}
		if($this->correctAnswers !== null) {
			$i = $this->correctAnswers->length - 1;
			while($i >= $correct->length) {
				$this->correctAnswers->remove($this->correctAnswers[$i]);
				$i--;
			}
		}
		if($this->assertions !== null) {
			$newAssertions = new _hx_array(array());
			{
				$_g1 = 0; $_g = $this->assertions->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$a = $this->assertions[$i1];
					$correctAnswers = $a->getCorrectAnswers();
					$newCorrectAnswersArray = new _hx_array(array());
					$j = null;
					{
						$_g3 = 0; $_g2 = $correctAnswers->length;
						while($_g3 < $_g2) {
							$j1 = $_g3++;
							$k = null;
							{
								$_g5 = 0; $_g4 = $correct->length;
								while($_g5 < $_g4) {
									$k1 = $_g5++;
									if($correct[$k1] === Std::parseInt($correctAnswers[$j1])) {
										$newCorrectAnswersArray->push($k1);
									}
									unset($k1);
								}
								unset($_g5,$_g4);
							}
							unset($k,$j1);
						}
						unset($_g3,$_g2);
					}
					if($newCorrectAnswersArray->length > 0) {
						$newCorrectAnswers = new _hx_array(array());
						{
							$_g3 = 0; $_g2 = $newCorrectAnswersArray->length;
							while($_g3 < $_g2) {
								$j1 = $_g3++;
								$newCorrectAnswers[$j1] = "" . _hx_string_rec($newCorrectAnswersArray[$j1], "");
								unset($j1);
							}
							unset($_g3,$_g2);
						}
						if($correctAnswers->length > 1 || $newCorrectAnswers->length === 1) {
							$a->setCorrectAnswers($newCorrectAnswers);
							$a->setAnswers($newCorrectAnswers);
							$newAssertions->push($a);
						} else {
							$k = null;
							{
								$_g3 = 0; $_g2 = $newCorrectAnswers->length;
								while($_g3 < $_g2) {
									$k1 = $_g3++;
									$b = $a->copy();
									$b->setCorrectAnswer($newCorrectAnswers[$k1]);
									$b->setAnswer($newCorrectAnswers[$k1]);
									$newAssertions->push($b);
									unset($k1,$b);
								}
								unset($_g3,$_g2);
							}
							unset($k);
						}
						unset($newCorrectAnswers);
					}
					unset($newCorrectAnswersArray,$j,$i1,$correctAnswers,$a);
				}
			}
			$this->assertions = $newAssertions;
		}
		$this->slots = new _hx_array(array());
		$this->updateSlots();
	}
	public function isImplicitOption($name, $value) {
		$i = 0;
		while($i < com_wiris_quizzes_impl_Option::$options->length) {
			if(com_wiris_quizzes_impl_Option::$options[$i] === $name) {
				break;
			}
			$i++;
		}
		return $i >= 8 && $this->defaultOption($name) === $value;
	}
	public function getAlgorithm() {
		if(com_wiris_quizzes_impl_HTMLTools::emptyCasSession($this->wirisCasSession)) {
			return null;
		} else {
			return $this->wirisCasSession;
		}
	}
	public function removeCalcOptions() {
		$_g1 = 0; $_g = com_wiris_quizzes_impl_CalcDocumentTools::$options->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			$opt = com_wiris_quizzes_impl_CalcDocumentTools::$options[$i];
			$this->removeOption($opt);
			unset($opt,$i);
		}
	}
	public function setAlgorithm($session) {
		if(com_wiris_quizzes_impl_HTMLTools::emptyCasSession($session)) {
			$session = null;
		}
		if($session !== $this->wirisCasSession || $session !== null && !($session === $this->wirisCasSession)) {
			$this->id = null;
			if(com_wiris_quizzes_impl_CalcDocumentTools::isCalc($session)) {
				$sessionDocument = new com_wiris_quizzes_impl_CalcDocumentTools($session);
				if(($this->wirisCasSession === null || !com_wiris_quizzes_impl_CalcDocumentTools::isCalc($this->wirisCasSession) || !$this->getCalcDocument()->hasQuizzesQuestionOptions()) && $sessionDocument->hasQuizzesQuestionOptions()) {
					$this->removeCalcOptions();
				}
				$this->calcDocument = $sessionDocument;
			} else {
				$this->calcDocument = null;
			}
			$this->wirisCasSession = $session;
		}
	}
	public function getAnswerFieldType() {
		$stringType = $this->getProperty(com_wiris_quizzes_api_PropertyName::$ANSWER_FIELD_TYPE);
		return com_wiris_quizzes_impl_QuizzesEnumUtils::string2answerFieldType($stringType);
	}
	public function setAnswerFieldType($type) {
		if($type !== null) {
			$this->setLocalData(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_INPUT_FIELD, com_wiris_quizzes_impl_QuizzesEnumUtils::answerFieldType2String($type));
		} else {
			throw new HException("Invalid type parameter.");
		}
	}
	public function changeAssertionParamName($a, $oldname, $newname) {
		if($a->parameters !== null) {
			$j = null;
			{
				$_g1 = 0; $_g = $a->parameters->length;
				while($_g1 < $_g) {
					$j1 = $_g1++;
					if(_hx_array_get($a->parameters, $j1)->name === $oldname) {
						_hx_array_get($a->parameters, $j1)->name = $newname;
					}
					unset($j1);
				}
			}
		}
	}
	public function importTolerance($tolerance) {
		if($this->isDeprecatedTolerance($tolerance)) {
			$pattern10 = new EReg("^10\\^\\(-(.*)\\)\$", "");
			if($pattern10->match($tolerance)) {
				$exponent = trim($pattern10->matched(1));
				if(StringTools::startsWith($exponent, "(") && StringTools::endsWith($exponent, ")")) {
					$exponent = trim(_hx_substr($exponent, 1, strlen($exponent) - 2));
				}
				if(com_wiris_system_TypeTools::isFloating($exponent)) {
					$expd = -Std::parseFloat($exponent);
					$tolerance = _hx_string_rec(Math::pow(10.0, $expd), "") . "";
				} else {
					$patternlog = new EReg("-?log\\((.*)\\)", "");
					if($patternlog->match($exponent)) {
						$arg = $patternlog->matched(1);
						if(StringTools::startsWith($exponent, "-")) {
							$tolerance = $arg;
						} else {
							if(com_wiris_system_TypeTools::isFloating($arg)) {
								$tolerance = _hx_string_rec(1.0 / Std::parseFloat($arg), "") . "";
							}
						}
					}
				}
			}
		}
		return $tolerance;
	}
	public function isDeprecatedTolerance($tol) {
		return _hx_index_of($tol, "10^", null) !== -1;
	}
	public function addDeprecationWarning($warning) {
		if(!com_wiris_system_ArrayEx::contains($this->deprecationWarnings, $warning)) {
			$this->deprecationWarnings->push($warning);
		}
	}
	public function importDeprecated() {
		if($this->assertions !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $this->assertions->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$a = $this->assertions[$i1];
					if($a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SET) {
						$a->name = com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC;
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_ORDER_MATTERS, "false");
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_REPETITION_MATTERS, "false");
						$this->addDeprecationWarning(com_wiris_quizzes_impl_QuestionImpl::$EQUIVALENT_SET_ASSERTION);
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_LIST) {
						$a->name = com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC;
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_NO_BRACKETS_LIST, "true");
						$this->addDeprecationWarning(com_wiris_quizzes_impl_QuestionImpl::$SYNTAX_LIST_ASSERTION);
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION) {
						$a->name = com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH;
						if($a->hasParam(com_wiris_quizzes_impl_Assertion::$PARAM_TEXT_LOGIC_OPERATORS)) {
							$a->removeParam(com_wiris_quizzes_impl_Assertion::$PARAM_TEXT_LOGIC_OPERATORS);
							$this->addDeprecationWarning(com_wiris_quizzes_impl_QuestionImpl::$TEXT_LOGIC_OPERATORS);
						}
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_QUANTITY) {
						$constants = $a->getParam("constants");
						$units = $a->getParam("units");
						$unitPrefixes = $a->getParam("unitprefixes");
						$itemseparators = $a->getParam("itemseparators");
						$decimalseparators = $a->getParam("decimalseparators");
						$a->name = com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH;
						$a->setParam("constants", $constants);
						$a->setParam("functions", "");
						$a->setParam("units", $units);
						$a->setParam("unitprefixes", $unitPrefixes);
						$a->setParam("itemseparators", $itemseparators);
						$a->setParam("decimalseparators", $decimalseparators);
						$a->setParam("ratio", "true");
						$a->setParam("scientificnotation", "true");
						unset($units,$unitPrefixes,$itemseparators,$decimalseparators,$constants);
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$CHECK_NO_MORE_DECIMALS) {
						$a->name = com_wiris_quizzes_impl_Assertion::$CHECK_PRECISION;
						$this->changeAssertionParamName($a, "digits", "max");
						$a->setParam("relative", "false");
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$CHECK_NO_MORE_DIGITS) {
						$a->name = com_wiris_quizzes_impl_Assertion::$CHECK_PRECISION;
						$this->changeAssertionParamName($a, "digits", "max");
						$a->setParam("relative", "true");
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$CHECK_UNIT) {
						$a->name = com_wiris_quizzes_impl_Assertion::$CHECK_EQUIVALENT_UNITS;
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_ALLOW_PREFIXES, "false");
						$a->removeParam(com_wiris_quizzes_impl_Assertion::$PARAM_UNIT);
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$CHECK_UNIT_LITERAL) {
						$a->name = com_wiris_quizzes_impl_Assertion::$CHECK_EQUIVALENT_UNITS;
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_ALLOW_PREFIXES, "false");
						$a->removeParam(com_wiris_quizzes_impl_Assertion::$PARAM_UNIT);
						$this->addDeprecationWarning(com_wiris_quizzes_impl_QuestionImpl::$EQUIVALENT_UNIT_LITERAL);
					}
					if($a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION && $a->hasParam(com_wiris_quizzes_impl_Assertion::$PARAM_NOT_EVALUATE)) {
						$value = $a->getParam(com_wiris_quizzes_impl_Assertion::$PARAM_NOT_EVALUATE);
						$a->removeParam(com_wiris_quizzes_impl_Assertion::$PARAM_NOT_EVALUATE);
						$a->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_FUNCTION_ARGUMENT_MODE, com_wiris_quizzes_impl_QuestionImpl_2($this, $_g, $_g1, $a, $i, $i1, $value));
						unset($value);
					}
					if($a->isEquivalence()) {
						$tol = $a->getParam(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE);
						if($tol !== null && $this->isDeprecatedTolerance($tol)) {
							$a->setParam(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, $this->importTolerance($tol));
						}
						unset($tol);
					}
					unset($i1,$a);
				}
			}
		}
		$tolerance = $this->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE);
		if($this->isDeprecatedTolerance($tolerance)) {
			$this->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, $this->importTolerance($tolerance));
		}
	}
	public function isAssertionDeprecatedCompatible($a) {
		return $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_NO_MORE_DIGITS || $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_NO_MORE_DECIMALS || $a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION || $a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_QUANTITY || $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_UNIT || $a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION && $a->hasParam(com_wiris_quizzes_impl_Assertion::$PARAM_NOT_EVALUATE);
	}
	public function isAssertionDeprecatedNeedsCheck($a) {
		return $a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SET || $a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_LIST || $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_UNIT_LITERAL || $a->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION && $a->hasParam(com_wiris_quizzes_impl_Assertion::$PARAM_TEXT_LOGIC_OPERATORS);
	}
	public function isDeprecated() {
		if($this->assertions !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $this->assertions->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$a = $this->assertions[$i1];
					if($this->isAssertionDeprecatedNeedsCheck($a)) {
						return com_wiris_quizzes_impl_QuestionImpl::$DEPRECATED_NEEDS_CHECK;
					} else {
						if($this->isAssertionDeprecatedCompatible($a)) {
							return com_wiris_quizzes_impl_QuestionImpl::$DEPRECATED_COMPATIBLE;
						}
					}
					if($a->isEquivalence()) {
						$tol = $a->getParam(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE);
						if($tol !== null && $this->isDeprecatedTolerance($tol)) {
							return com_wiris_quizzes_impl_QuestionImpl::$DEPRECATED_COMPATIBLE;
						}
						unset($tol);
					}
					unset($i1,$a);
				}
			}
		}
		if($this->isDeprecatedTolerance($this->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE))) {
			return com_wiris_quizzes_impl_QuestionImpl::$DEPRECATED_COMPATIBLE;
		}
		return com_wiris_quizzes_impl_QuestionImpl::$NO_DEPRECATED;
	}
	public function getImpl() {
		return $this;
	}
	public function hasId() {
		return $this->id !== null && strlen($this->id) > 0;
	}
	public function addAssertion($name, $correctAnswer, $studentAnswer, $parameters) {
		$this->setParametrizedAssertion($name, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($studentAnswer, ""), $parameters);
	}
	public function isEquivalent($q) {
		$te = com_wiris_quizzes_impl_HTMLTools::emptyCasSession($this->wirisCasSession);
		$qe = com_wiris_quizzes_impl_HTMLTools::emptyCasSession($q->wirisCasSession);
		if($te && !$qe || !$te && $qe) {
			return false;
		} else {
			if(!$te && !$qe && !($this->wirisCasSession === $q->wirisCasSession)) {
				return false;
			}
		}
		if($this->correctAnswers !== null && $q->correctAnswers !== null) {
			if($this->correctAnswers->length !== $q->correctAnswers->length) {
				return false;
			}
			$i = null;
			{
				$_g1 = 0; $_g = $this->correctAnswers->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$tca = $this->correctAnswers[$i1];
					$qca = $q->correctAnswers[$i1];
					if(!($tca->id === $qca->id)) {
						return false;
					}
					if(!($tca->content === $qca->content)) {
						return false;
					}
					unset($tca,$qca,$i1);
				}
			}
		}
		if($this->assertions !== null && $q->assertions !== null) {
			if($this->assertions->length !== $q->assertions->length) {
				return false;
			}
			$i = null;
			{
				$_g1 = 0; $_g = $this->assertions->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$ta = $this->assertions[$i1];
					$qa = $q->assertions[$i1];
					if($ta->getCorrectAnswer() !== $qa->getCorrectAnswer() || $ta->getAnswer() !== $qa->getAnswer() || !($ta->name === $qa->name)) {
						return false;
					}
					if($ta->parameters === null && $qa->parameters !== null || $ta->parameters !== null && $qa->parameters === null) {
						return false;
					}
					if($ta->parameters !== null && $qa->parameters !== null) {
						if($ta->parameters->length !== $qa->parameters->length) {
							return false;
						}
						$j = null;
						{
							$_g3 = 0; $_g2 = $ta->parameters->length;
							while($_g3 < $_g2) {
								$j1 = $_g3++;
								$tp = $ta->parameters[$j1];
								$qp = $qa->parameters[$j1];
								if($tp->name !== $qp->name || $tp->content !== $qp->content) {
									return false;
								}
								unset($tp,$qp,$j1);
							}
							unset($_g3,$_g2);
						}
						unset($j);
					}
					unset($ta,$qa,$i1);
				}
			}
		}
		$k = null;
		{
			$_g1 = 0; $_g = com_wiris_quizzes_impl_Option::$options->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$to = $this->getOption(com_wiris_quizzes_impl_Option::$options[$k1]);
				$qo = $q->getOption(com_wiris_quizzes_impl_Option::$options[$k1]);
				if($to === null && $qo !== null || $to !== null && $qo === null || !($to === $qo)) {
					return false;
				}
				unset($to,$qo,$k1);
			}
		}
		{
			$_g1 = 0; $_g = com_wiris_quizzes_impl_LocalData::$keys->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$td = $this->getLocalData(com_wiris_quizzes_impl_LocalData::$keys[$k1]);
				$qd = $q->getLocalData(com_wiris_quizzes_impl_LocalData::$keys[$k1]);
				if($td === null && $qd !== null || $td !== null && $qd === null || !($td === $qd)) {
					return false;
				}
				unset($td,$qd,$k1);
			}
		}
		return true;
	}
	public function update($response) {
		$this->id = null;
		$qs = $response;
		if($qs !== null && $qs->results !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $qs->results->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$r = $qs->results[$i1];
					$s = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getSerializer();
					$tag = $s->getTagName($r);
					if($tag === com_wiris_quizzes_impl_ResultGetTranslation::$tagName) {
						$rgt = $r;
						$this->wirisCasSession = trim($rgt->wirisCasSession);
						unset($rgt);
					}
					unset($tag,$s,$r,$i1);
				}
			}
		}
	}
	public function hideCompoundAnswerAnswers($m) {
		if(com_wiris_quizzes_impl_MathContent::getMathType($m) === com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE) {
			$geometryFile = com_wiris_util_geometry_GeometryFile::readJSON($m);
			$geometryFile->data->remove(com_wiris_util_geometry_GeometryFile::$CONSTRAINTS);
			$geometryFile->data->remove(com_wiris_util_geometry_GeometryFile::$HANDWRITING_TRACES);
			$elements = com_wiris_util_json_JSon::getArray($geometryFile->data->get(com_wiris_util_geometry_GeometryFile::$ELEMENTS));
			$elements2 = new _hx_array(array());
			{
				$_g = 0;
				while($_g < $elements->length) {
					$o = $elements[$_g];
					++$_g;
					$element = com_wiris_util_json_JSon::getHash($o);
					$element2 = new Hash();
					$element2->set(com_wiris_util_geometry_GeometryElement::$ID, $element->get(com_wiris_util_geometry_GeometryElement::$ID));
					$element2->set(com_wiris_util_geometry_GeometryElement::$TYPE, $element->get(com_wiris_util_geometry_GeometryElement::$TYPE));
					$elements2->push($element2);
					unset($o,$element2,$element);
				}
			}
			$geometryFile->data->set(com_wiris_util_geometry_GeometryElement::$ELEMENTS, $elements2);
			return $geometryFile->toJSON();
		} else {
			$a = new com_wiris_quizzes_impl_MathContent();
			$a->set($m);
			$c = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($a);
			$i = null;
			{
				$_g1 = 0; $_g = $c->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$c[$i1][1] = "<math></math>";
					unset($i1);
				}
			}
			$a = com_wiris_quizzes_impl_CompoundAnswerParser::joinCompoundAnswer($c);
			return $a->content;
		}
	}
	public function isQuestionCompoundAnswer() {
		return $this->getProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE;
	}
	public function isCompoundAnswer() {
		if($this->isQuestionCompoundAnswer()) {
			return true;
		}
		if($this->slots !== null) {
			$_g = 0; $_g1 = $this->slots;
			while($_g < $_g1->length) {
				$s = $_g1[$_g];
				++$_g;
				if($s->isSlotCompoundAnswer()) {
					return true;
				}
				unset($s);
			}
		}
		return false;
	}
	public function getStudentQuestion() {
		$q = new com_wiris_quizzes_impl_QuestionImpl();
		$q->id = $this->id;
		$i = null;
		$q->assertions = $this->assertions;
		$q->localData = $this->localData;
		if($this->slots !== null) {
			$q->slots = new _hx_array(array());
			{
				$_g = 0; $_g1 = $this->slots;
				while($_g < $_g1->length) {
					$s = $_g1[$_g];
					++$_g;
					$sq = new com_wiris_quizzes_impl_SlotImpl();
					$sq->question = $q;
					$sq->syntax = $s->syntax;
					$sq->localData = $s->localData;
					$sq->initialContent = $s->initialContent;
					$sq->id = $s->id;
					$q->slots->push($sq);
					unset($sq,$s);
				}
			}
		}
		if($this->isCompoundAnswer() && $this->correctAnswers !== null) {
			$q->correctAnswers = new _hx_array(array());
			if($this->isQuestionCompoundAnswer()) {
				$_g1 = 0; $_g = $this->correctAnswers->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$ca = $this->correctAnswers[$i1];
					$content = $ca->content;
					if($ca->content !== null && strlen($ca->content) > 0) {
						$content = $this->hideCompoundAnswerAnswers($ca->content);
					}
					$q->setCorrectAnswer($i1, $content);
					unset($i1,$content,$ca);
				}
			} else {
				if($this->slots !== null) {
					$_g = 0; $_g1 = $this->slots;
					while($_g < $_g1->length) {
						$s = $_g1[$_g];
						++$_g;
						if($s->isSlotCompoundAnswer()) {
							$aaa = $s->authorAnswers;
							{
								$_g2 = 0;
								while($_g2 < $aaa->length) {
									$aa = $aaa[$_g2];
									++$_g2;
									$ca = $aa->value;
									$content = $ca->content;
									if($ca->content !== null && strlen($ca->content) > 0) {
										$content = $this->hideCompoundAnswerAnswers($ca->content);
									}
									$q->setCorrectAnswer(Std::parseInt($ca->id), $content);
									unset($content,$ca,$aa);
								}
								unset($_g2);
							}
							unset($aaa);
						}
						unset($s);
					}
				}
			}
		}
		return $q;
	}
	public function defaultLocalData($name) {
		if($name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER) {
			return com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_FALSE;
		} else {
			if($name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_INPUT_FIELD) {
				return com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_INPUT_FIELD_INLINE_EDITOR;
			} else {
				if($name === com_wiris_quizzes_impl_LocalData::$KEY_SHOW_CAS || $name === com_wiris_quizzes_impl_LocalData::$KEY_SHOW_AUXILIARY_TEXT_INPUT) {
					return com_wiris_quizzes_impl_LocalData::$VALUE_SHOW_CAS_FALSE;
				} else {
					if($name === com_wiris_quizzes_impl_LocalData::$KEY_CAS_INITIAL_SESSION) {
						return null;
					} else {
						if($name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE) {
							return com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_GRADE_AND;
						} else {
							if($name === com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTION) {
								return null;
							} else {
								if($name === com_wiris_quizzes_impl_LocalData::$KEY_AUXILIARY_CAS_HIDE_FILE_MENU) {
									return com_wiris_quizzes_impl_LocalData::$VALUE_AUXILIARY_CAS_HIDE_FILE_MENU_FALSE;
								} else {
									if($name === com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_LOCK_INITIAL_CONTENT) {
										return "false";
									} else {
										if($name === com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_NAME_IN_LABEL) {
											return com_wiris_quizzes_impl_LocalData::$VALUE_ALWAYS;
										} else {
											if($name === com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_SHOW_VALUE_IN_LABEL) {
												return com_wiris_quizzes_impl_LocalData::$VALUE_FOCUS;
											} else {
												if($name === com_wiris_quizzes_impl_LocalData::$KEY_GRAPH_MAGNETIC_GRID) {
													return com_wiris_quizzes_impl_LocalData::$VALUE_SNAP;
												} else {
													return null;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public function getLocalData($name) {
		$ld = com_wiris_quizzes_impl_QuestionImpl::getLocalDataFromArray($name, $this->localData);
		return (($ld !== null) ? $ld : $this->defaultLocalData($name));
	}
	public function setLocalData($name, $value) {
		$this->id = null;
		if($this->localData === null) {
			$this->localData = new _hx_array(array());
		}
		com_wiris_quizzes_impl_QuestionImpl::setLocalDataToArray($name, $value, $this->localData);
	}
	public function getAssertionIndex($name, $correctAnswer, $userAnswer) {
		if($this->assertions === null) {
			return -1;
		}
		$i = null;
		{
			$_g1 = 0; $_g = $this->assertions->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$assertion = $this->assertions[$i1];
				if($assertion->getCorrectAnswer() === $correctAnswer && $assertion->getAnswer() === $userAnswer && $assertion->name === $name) {
					return $i1;
				}
				unset($i1,$assertion);
			}
		}
		return -1;
	}
	public function getCorrectAnswersLength() {
		return com_wiris_quizzes_impl_QuestionImpl_3($this);
	}
	public function getCorrectAnswer($index) {
		if($this->correctAnswers !== null && $this->correctAnswers->length > $index) {
			$a = $this->correctAnswers[$index];
			if($a !== null) {
				return $a->content;
			}
		}
		return null;
	}
	public function setCorrectAnswer($index, $content) {
		$this->id = null;
		if($index < 0) {
			throw new HException("Invalid index: " . _hx_string_rec($index, ""));
		}
		if($this->correctAnswers === null) {
			$this->correctAnswers = new _hx_array(array());
		}
		while($index >= $this->correctAnswers->length) {
			$this->correctAnswers->push(new com_wiris_quizzes_impl_CorrectAnswer());
		}
		$ca = $this->correctAnswers[$index];
		$ca->id = "" . _hx_string_rec($index, "");
		$ca->weight = 1.0;
		$content = com_wiris_util_xml_MathMLUtils::convertEditor2Newlines($content);
		$ca->set($content);
		$this->updateSlots();
	}
	public function defaultOption($name) {
		return com_wiris_quizzes_impl_QuestionImpl::$defaultOptions->get($name);
	}
	public function removeLocalData($name) {
		$this->id = null;
		com_wiris_quizzes_impl_QuestionImpl::removeLocalDataFromArray($name, $this->localData);
	}
	public function removeOption($name) {
		if($this->hasCalcmeSessionWithOptions() && $this->isCalcmeOption($name)) {
			$this->wirisCasSession = $this->calcDocument->removeOption($name);
			return;
		}
		$this->id = null;
		if($this->options !== null) {
			$i = $this->options->length - 1;
			while($i >= 0) {
				if(_hx_array_get($this->options, $i)->name === $name) {
					$this->options->remove($this->options[$i]);
				}
				$i--;
			}
		}
	}
	public function getOption($name) {
		if($this->hasCalcmeSessionWithOptions() && $this->isCalcmeOption($name)) {
			$calcOption = $this->getCalcDocument()->getOption($name);
			if($calcOption !== null) {
				return $calcOption;
			}
		} else {
			if($this->options !== null) {
				$i = null;
				{
					$_g1 = 0; $_g = $this->options->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						if(_hx_array_get($this->options, $i1)->name === $name) {
							return _hx_array_get($this->options, $i1)->content;
						}
						unset($i1);
					}
				}
			}
		}
		return $this->defaultOption($name);
	}
	public function isCalcmeOption($name) {
		{
			$_g1 = 0; $_g = com_wiris_quizzes_impl_CalcDocumentTools::$options->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$opt = com_wiris_quizzes_impl_CalcDocumentTools::$options[$i];
				if($opt === $name) {
					return true;
				}
				unset($opt,$i);
			}
		}
		return false;
	}
	public function getItemSeparator() {
		if($this->hasCalcmeSessionWithOptions()) {
			return $this->getCalcDocument()->getOption("item_separator");
		}
		if("," === $this->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DECIMAL_SEPARATOR) || "," === $this->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DIGIT_GROUP_SEPARATOR) && StringTools::startsWith($this->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_FLOAT_FORMAT), ",")) {
			return ";";
		}
		return ",";
	}
	public function getCalcDocument() {
		if($this->calcDocument === null) {
			$this->calcDocument = new com_wiris_quizzes_impl_CalcDocumentTools($this->wirisCasSession);
		}
		return $this->calcDocument;
	}
	public function setOption($name, $value) {
		$this->id = null;
		if($value === null) {
			$this->removeOption($name);
		} else {
			if($this->hasCalcmeSessionWithOptions() && $this->isCalcmeOption($name)) {
				$this->wirisCasSession = $this->getCalcDocument()->setOption($name, $value);
			} else {
				if($this->isImplicitOption($name, $value)) {
					$this->removeOption($name);
				} else {
					if($this->options === null) {
						$this->options = new _hx_array(array());
					}
					$opt = new com_wiris_quizzes_impl_Option();
					$opt->name = $name;
					$opt->content = $value;
					$opt->type = com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
					$i = null;
					$found = false;
					{
						$_g1 = 0; $_g = $this->options->length;
						while($_g1 < $_g) {
							$i1 = $_g1++;
							if(_hx_array_get($this->options, $i1)->name === $name) {
								$this->options[$i1] = $opt;
								$found = true;
							}
							unset($i1);
						}
					}
					if(!$found) {
						$this->options->push($opt);
					}
				}
			}
		}
	}
	public function getAssertionParameter($assertionName, $correctAnswer, $studentAnswer, $name) {
		$ind = $this->getAssertionIndex($assertionName, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($studentAnswer, ""));
		if($ind > -1) {
			return $this->getAssertion($ind)->getParam($name);
		}
		return null;
	}
	public function setAssertionParameter($assertionName, $correctAnswer, $studentAnswer, $name, $value) {
		$ind = $this->getAssertionIndex($assertionName, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($studentAnswer, ""));
		if($ind === -1) {
			$this->addAssertion($assertionName, $correctAnswer, $studentAnswer, null);
			$ind = $this->getAssertionIndex($assertionName, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($studentAnswer, ""));
		}
		if($value === null) {
			$this->getAssertion($ind)->removeParam($name);
		} else {
			$this->getAssertion($ind)->setParam($name, $value);
		}
	}
	public function setParametrizedAssertion($name, $correctAnswer, $userAnswer, $parameters) {
		$this->id = null;
		if($this->assertions === null) {
			$this->assertions = new _hx_array(array());
		}
		$a = new com_wiris_quizzes_impl_Assertion();
		$a->name = $name;
		$a->setCorrectAnswer($correctAnswer);
		$a->setAnswer($userAnswer);
		$names = com_wiris_quizzes_impl_Assertion::getParameterNames($name);
		if($parameters !== null && $names !== null) {
			$a->parameters = new _hx_array(array());
			$n = com_wiris_quizzes_impl_QuestionImpl_4($this, $a, $correctAnswer, $name, $names, $parameters, $userAnswer);
			$i = null;
			{
				$_g = 0;
				while($_g < $n) {
					$i1 = $_g++;
					if($parameters[$i1] !== null) {
						$ap = new com_wiris_quizzes_impl_AssertionParam();
						$ap->name = $names[$i1];
						$ap->content = $parameters[$i1];
						$ap->type = com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
						$a->parameters->push($ap);
						unset($ap);
					}
					unset($i1);
				}
			}
		}
		$index = $this->getAssertionIndex($name, $correctAnswer, $userAnswer);
		if($index === -1) {
			$this->assertions->push($a);
		} else {
			$this->assertions[$index] = $a;
		}
		$this->updateSlots();
	}
	public function removeAssertion($name, $correctAnswer, $userAnswer) {
		$this->id = null;
		if($this->assertions !== null) {
			$i = $this->assertions->length - 1;
			while($i >= 0) {
				$a = $this->assertions[$i];
				if($a->name === $name && $a->getCorrectAnswer() === $correctAnswer && $a->getAnswer() === $userAnswer) {
					$this->assertions->remove($a);
				}
				$i--;
				unset($a);
			}
		}
	}
	public function hasCalcmeSessionWithOptions() {
		return com_wiris_quizzes_impl_CalcDocumentTools::isCalc($this->wirisCasSession) && $this->getCalcDocument()->hasQuizzesQuestionOptions();
	}
	public function setAssertion($name, $correctAnswer, $userAnswer) {
		$this->setParametrizedAssertion($name, "" . _hx_string_rec($correctAnswer, ""), "" . _hx_string_rec($userAnswer, ""), null);
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_QuestionImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_QuestionImpl::$TAGNAME);
		$this->id = $s->cacheAttribute("id", $this->id, null);
		$this->wirisCasSession = $s->childString("wirisCasSession", $this->wirisCasSession, null);
		$this->correctAnswers = $s->serializeArrayName($this->correctAnswers, "correctAnswers");
		$this->assertions = $s->serializeArrayName($this->assertions, "assertions");
		$this->slots = $s->serializeArrayName($this->slots, "slots");
		$this->options = $s->serializeArrayName($this->options, "options");
		$this->localData = $s->serializeArrayName($this->localData, "localData");
		$s->endTag();
	}
	public $deprecationWarnings;
	public $calcDocument;
	public $localData;
	public $options;
	public $wirisCasSession;
	public $id;
	public $assertions;
	public $correctAnswers;
	public $slots;
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
	static $defaultOptions = null;
	static $TAGNAME = "question";
	static $NO_DEPRECATED = 0;
	static $DEPRECATED_COMPATIBLE = 1;
	static $DEPRECATED_NEEDS_CHECK = 2;
	static $EQUIVALENT_SET_NO_AVAILABLE = "equivalent_set_no_available";
	static $EQUIVALENT_SET_ASSERTION = "quizzes_studio_equivalent_set_assertion";
	static $SYNTAX_LIST_ASSERTION = "quizzes_studio_syntax_list_assertion";
	static $TEXT_LOGIC_OPERATORS = "quizzes_studio_text_logic_operators";
	static $EQUIVALENT_UNIT_LITERAL = "quizzes_studio_equivalent_unit_literal";
	static function removeLocalDataFromArray($name, $localData) {
		if($localData !== null) {
			$i = $localData->length - 1;
			while($i >= 0) {
				if(_hx_array_get($localData, $i)->name === $name) {
					$localData->remove($localData[$i]);
				}
				$i--;
			}
		}
	}
	static function getDefaultOptions() {
		$dopt = new Hash();
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_EXPONENTIAL_E, "e");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_IMAGINARY_UNIT, "i");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_IMPLICIT_TIMES_OPERATOR, "false");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_NUMBER_PI, com_wiris_quizzes_impl_QuestionImpl_5($dopt));
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_PRECISION, "4");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_RELATIVE_TOLERANCE, "true");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TIMES_OPERATOR, com_wiris_quizzes_impl_QuestionImpl_6($dopt));
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, "0.001");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE_DIGITS, "false");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_FLOAT_FORMAT, "mg");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DECIMAL_SEPARATOR, ".");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DIGIT_GROUP_SEPARATOR, "");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER, "false");
		$dopt->set(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME, "answer");
		return $dopt;
	}
	static function setLocalDataToArray($name, $value, $ld) {
		$data = new com_wiris_quizzes_impl_LocalData();
		$data->name = $name;
		$data->value = $value;
		$i = null;
		$found = false;
		{
			$_g1 = 0; $_g = $ld->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_array_get($ld, $i1)->name === $name) {
					$ld[$i1] = $data;
					$found = true;
				}
				unset($i1);
			}
		}
		if(!$found) {
			$ld->push($data);
		}
	}
	static function getLocalDataFromArray($name, $ld) {
		if($ld !== null) {
			$i = null;
			{
				$_g1 = 0; $_g = $ld->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(_hx_array_get($ld, $i1)->name === $name) {
						return _hx_array_get($ld, $i1)->value;
					}
					unset($i1);
				}
			}
		}
		return null;
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuestionImpl'; }
}
function com_wiris_quizzes_impl_QuestionImpl_0(&$»this, &$_g, &$_g1, &$a, &$assertionsCopy, &$correctAnswers, &$i, &$overrideDeprecated, &$slotId) {
	if($»this->slots->length > 0) {
		return _hx_array_get($»this->slots, $»this->slots->length - 1)->localData;
	}
}
function com_wiris_quizzes_impl_QuestionImpl_1(&$»this) {
	if($»this->assertions === null) {
		return 0;
	} else {
		return $»this->assertions->length;
	}
}
function com_wiris_quizzes_impl_QuestionImpl_2(&$»this, &$_g, &$_g1, &$a, &$i, &$i1, &$value) {
	if("true" === $value) {
		return com_wiris_quizzes_impl_Assertion::$PARAM_VALUE_FUNCTION_ARGUMENT_UNEVALUATED;
	} else {
		return com_wiris_quizzes_impl_Assertion::$PARAM_VALUE_FUNCTION_ARGUMENT_EVALUATED;
	}
}
function com_wiris_quizzes_impl_QuestionImpl_3(&$»this) {
	if($»this->correctAnswers === null) {
		return 0;
	} else {
		return $»this->correctAnswers->length;
	}
}
function com_wiris_quizzes_impl_QuestionImpl_4(&$»this, &$a, &$correctAnswer, &$name, &$names, &$parameters, &$userAnswer) {
	if($parameters->length < $names->length) {
		return $parameters->length;
	} else {
		return $names->length;
	}
}
function com_wiris_quizzes_impl_QuestionImpl_5(&$dopt) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(960);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_QuestionImpl_6(&$dopt) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(183);
		return $s->toString();
	}
}

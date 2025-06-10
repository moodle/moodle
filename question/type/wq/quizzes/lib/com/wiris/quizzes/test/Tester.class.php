<?php

class com_wiris_quizzes_test_Tester {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->numCalls = 0;
	}}
	public function testConvertEditor2Newlines() {
		$tests = new _hx_array(array("<mtable columnalign=\"left\" rowspacing=\"0\"><mtr><mtd><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd></mtr><mtr><mtd><mtable><mtr><mtd><mi>a</mi></mtd><mtd><mi>b</mi></mtd></mtr></mtable></mtd><mtd><mn>2</mn></mtd></mtr></mtable></mfenced></mtd></mtr><mtr><mtd><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd></mtr></mtable></mtd></mtr></mtable>"));
		$res = new _hx_array(array("<math><mfenced><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd></mtr><mtr><mtd><mtable><mtr><mtd><mi>a</mi></mtd><mtd><mi>b</mi></mtd></mtr></mtable></mtd><mtd><mn>2</mn></mtd></mtr></mtable></mfenced><mspace linebreak=\"newline\"/><mtable><mtr><mtd><mn>1</mn></mtd><mtd><mn>2</mn></mtd></mtr></mtable></math>"));
		$i = null;
		{
			$_g1 = 0; $_g = $tests->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$u = com_wiris_util_xml_MathMLUtils::convertEditor2Newlines($tests[$i1]);
				if(!($u === $res[$i1])) {
					throw new HException("Expected: " . $res[$i1] . ". Got: " . $u . ".");
				}
				unset($u,$i1);
			}
		}
	}
	public function testCompatibility() {
		if($this->apiVersion > com_wiris_quizzes_test_Tester::$QUIZZES3) {
			return;
		}
		$question = "<question><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-3)</option><option name=\"relative_tolerance\">true</option><option name=\"precision\">4</option><option name=\"times_operator\">Â·</option><option name=\"implicit_times_operator\">false</option><option name=\"imaginary_unit\">i</option></options><localData><data name=\"inputField\">popupEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"inputCompound\">true</data></localData></question>";
		$instance = "<questionInstance><userData><randomSeed>64038</randomSeed><answers><answer type=\"mathml\"><![CDATA[<math><mi>a</mi><mo>=</mo><semantics><mrow><mn>1</mn></mrow><annotation encoding=\"text/plain\">1</annotation></semantics><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><semantics><mrow><mn>3</mn></mrow><annotation encoding=\"text/plain\">3</annotation></semantics></math>]]></answer></answers></userData><checks><check assertion=\"syntax_expression\" answer=\"1000\" correctAnswer=\"1000\">1</check><check assertion=\"syntax_expression\" answer=\"1001\" correctAnswer=\"1001\">1</check><check assertion=\"equivalent_symbolic\" answer=\"1000\" correctAnswer=\"1000\">1</check><check assertion=\"equivalent_symbolic\" answer=\"1001\" correctAnswer=\"1001\">0</check></checks><localData><data name=\"handwritingConstraints\">{&quot;symbols&quot;:[&quot;1&quot;,&quot;2&quot;,&quot;=&quot;,&quot;a&quot;,&quot;b&quot;],&quot;structure&quot;:[&quot;General&quot;,&quot;Fraction&quot;,&quot;Multiline&quot;]}</data></localData></questionInstance>";
		$builder = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
		$q = $builder->readQuestion($question)->getImpl();
		$qi = $builder->readQuestionInstance($instance);
		if(!($q->getCorrectAnswer(0) === "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>")) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if(!($q->getLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER) === com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE)) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if($qi->getCompoundAnswerGrade(0, 0, 0, $q) !== 1.0) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if($qi->getCompoundAnswerGrade(0, 0, 1, $q) !== 0.0) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		$q2 = $builder->readQuestion($q->serialize())->getImpl();
		$s = $qi->serialize();
		$qi2 = $builder->readQuestionInstance($s);
		if(!($q2->getCorrectAnswer(0) === "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>")) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if(!($q2->getLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER) === com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE)) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if($qi2->getCompoundAnswerGrade(0, 0, 0, $q) !== 1.0) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		if($qi2->getCompoundAnswerGrade(0, 0, 1, $q) !== 0.0) {
			throw new HException(new com_wiris_system_Exception("Failed compatibility test!", null));
		}
		haxe_Log::trace("Test compatibility OK!", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 2199, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "testCompatibility")));
	}
	public function responseFeedback3($r, $q, $qi) {
		$qi->update($r);
		$a1 = $qi->expandVariables("#answer1");
		$a2 = $qi->expandVariables("#answer2");
		if(!($a1 === "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math>" && $a2 === "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>3</mn></math>")) {
			throw new HException(new com_wiris_system_Exception("Failed test feedback3!", null));
		}
	}
	public function testFeedback3() {
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		$u = new com_wiris_quizzes_impl_UserData();
		$u->setUserCompoundAnswer(0, 0, "2");
		$u->setUserCompoundAnswer(0, 1, "3");
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$qb = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $qb->readQuestion("<question><wirisCasSession><![CDATA[<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer1</mi><mo>=</mo><mn>0</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer2</mi><mo>=</mo><mn>0</mn></math></input></command></group></library></session>]]></wirisCasSession><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-3)</option><option name=\"relative_tolerance\">true</option><option name=\"precision\">4</option><option name=\"times_operator\">Â·</option><option name=\"implicit_times_operator\">false</option><option name=\"imaginary_unit\">i</option><option name=\"answer_parameter\">true</option></options><localData><data name=\"inputField\">popupEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"inputCompound\">true</data></localData></question>");
			$qi = $qb->newQuestionInstance($q);
			$qi->setStudentAnswer(0, _hx_array_get($u->answers, 0)->content);
			$r = $qb->newFeedbackRequest("<p>a=#answer1</p><p>b=#answer2</p>", $q, $qi);
			$s = $qb->getQuizzesService();
		} else {
			$b = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $b->readQuestion("<question><wirisCasSession><![CDATA[<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer1</mi><mo>=</mo><mn>0</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer2</mi><mo>=</mo><mn>0</mn></math></input></command></group></library></session>]]></wirisCasSession><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-3)</option><option name=\"relative_tolerance\">true</option><option name=\"precision\">4</option><option name=\"times_operator\">Â·</option><option name=\"implicit_times_operator\">false</option><option name=\"imaginary_unit\">i</option><option name=\"answer_parameter\">true</option></options><localData><data name=\"inputField\">popupEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"inputCompound\">true</data></localData></question>");
			$qi = $b->newQuestionInstance($q);
			$slots = $q->getSlots();
			$qi->setSlotAnswer($slots[0], _hx_array_get($u->answers, 0)->content);
			$r = $b->newFeedbackRequest("<p>a=#answer1</p><p>b=#answer2</p>", $qi);
			$s = $b->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("feedback3", $this, $q, $qi));
	}
	public function inArray($a, $b) {
		$i = null;
		{
			$_g1 = 0; $_g = $b->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_equal($b[$i1], $a)) {
					return true;
				}
				unset($i1);
			}
		}
		return false;
	}
	public function onServiceResponse($id, $res, $q, $qi) {
		try {
			if($id === "compound1") {
				$this->responseCompound1($res, $q, $qi);
			} else {
				if($id === "compound2") {
					$this->responseCompound2($res, $q, $qi);
				} else {
					if($id === "compound3") {
						$this->responseCompound3($res, $q, $qi);
					} else {
						if($id === "compound4") {
							$this->responseCompound4($res, $q, $qi);
						} else {
							if($id === "compound5") {
								$this->responseCompound5($res, $q, $qi);
							} else {
								if($id === "images1") {
									$this->responseImages1($res, $q, $qi);
								} else {
									if($id === "images2") {
										$this->responseImages2($res, $q, $qi);
									} else {
										if($id === "lang1") {
											$this->responseLang1($res, $q, $qi);
										} else {
											if($id === "openquestion1") {
												$this->responseOpenQuestion1($res);
											} else {
												if($id === "tolerance1") {
													$this->responseTolerance1($res, $q, $qi);
												} else {
													if($id === "randomquestion1") {
														$this->responseRandomQuestion1($res, $q, $qi);
													} else {
														if($id === "randomquestion2") {
															$this->responseRandomQuestion2($res, $q, $qi);
														} else {
															if($id === "encodings1") {
																$this->responseEncodings1($res, $q, $qi);
															} else {
																if($id === "encodings2") {
																	$this->responseEncodings2($res, $q, $qi);
																} else {
																	if($id === "translation1") {
																		$this->responseTranslation1($res, $q);
																	} else {
																		if($id === "bugs1") {
																			$this->responseBugs1($res, $q, $qi);
																		} else {
																			if($id === "multianswer") {
																				$this->responseMultianswer($res, $q, $qi);
																			} else {
																				if($id === "multianswer2") {
																					$this->responseMultianswer2($res, $q, $qi);
																				} else {
																					if($id === "anyanswer1") {
																						$this->responseAnyAnswer1($res, $q, $qi);
																					} else {
																						if($id === "floateval1") {
																							$this->responseFloatEval1($res, $q, $qi);
																						} else {
																							if($id === "handwritingConstraints") {
																								$this->responseHandwritingConstraints($res, $q, $qi);
																							} else {
																								if($id === "parameters") {
																									$this->responseParameters($res, $q, $qi);
																								} else {
																									if($id === "unicode1") {
																										$this->responseUnicode1($res, $q, $qi);
																									} else {
																										if($id === "unicode2") {
																											$this->responseUnicode2($res, $q, $qi);
																										} else {
																											if($id === "floatformat1") {
																												$this->responseFloatFormat1($res, $q, $qi);
																											} else {
																												if($id === "feedback") {
																													$this->responseFeedback($res, $q, $qi);
																												} else {
																													if($id === "feedback2") {
																														$this->responseFeedback2($res, $q, $qi);
																													} else {
																														if($id === "feedback3") {
																															$this->responseFeedback3($res, $q, $qi);
																														} else {
																															if($id === "userid") {
																																$this->responseUserId($res, $q, $qi);
																															} else {
																																if($id === "compoundVariableLabels") {
																																	$this->responseCompoundVariableLabels($res, $q, $qi);
																																} else {
																																	if($id === "evaluateRandomVariables1") {
																																		$this->responseEvaluateRandomVariables1($res, $q, $qi);
																																	} else {
																																		if($id === "evaluateRandomVariables2") {
																																			$this->responseEvaluateRandomVariables2($res, $q, $qi);
																																		} else {
																																			throw new HException(new com_wiris_system_Exception("Unknown test id.", null));
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
										}
									}
								}
							}
						}
					}
				}
			}
			haxe_Log::trace("Test " . $id . " OK!", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 2102, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "onServiceResponse")));
			$this->endCall();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				haxe_Log::trace("Failed test " . $id . "!!!", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 2105, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "onServiceResponse")));
				throw new HException($e);
			}
		}
	}
	public function responseEvaluateRandomVariables2($qr, $q, $qi) {
		$qi->update($qr);
		$slot = _hx_array_get($q->getSlots(), 0);
		$authorAnswer = _hx_array_get($slot->getAuthorAnswers(), 0);
		if($qi->getGrade($slot, $authorAnswer) !== 1.0) {
			throw new HException("Failed test evaluateRandomVariables2");
		}
		$authorAnswer = _hx_array_get($slot->getAuthorAnswers(), 1);
		if($qi->getGrade($slot, $authorAnswer) !== 1.0) {
			throw new HException("Failed test evaluateRandomVariables2");
		}
	}
	public function responseEvaluateRandomVariables1($qr, $q, $qi) {
		$qi->update($qr);
		$slot = _hx_array_get($q->getSlots(), 0);
		$authorAnswer = _hx_array_get($slot->getAuthorAnswers(), 0);
		$t = $qi->expandVariables($authorAnswer->getValue());
		if(!($t === "<math><mrow><mn>8</mn></mrow></math>")) {
			throw new HException("Failed test evaluateRandomVariables1");
		}
		$authorAnswer = _hx_array_get($slot->getAuthorAnswers(), 1);
		$t = $qi->expandVariables($authorAnswer->getValue());
		if(!($t === "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mrow><mn>8</mn></mrow></mrow></math>")) {
			throw new HException("Failed test evaluateRandomVariables1 " . $t);
		}
		$qi->setSlotAnswer($slot, "8");
		$request = com_wiris_quizzes_api_Quizzes::getInstance()->newGradeRequest($qi);
		$service = com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesService();
		$service->executeAsync($request, new com_wiris_quizzes_test_TestIdServiceListener("evaluateRandomVariables2", $this, $q, $qi));
	}
	public function testEvaluateRandomVariables() {
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			return;
		}
		$statement = "Sum #a and #b";
		$correctAnswer = "evaluate(#a + #b)";
		$correctAnswer2 = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>evaluate</mi><mfenced><mrow><mo>#</mo><mi>a</mi><mo>+</mo><mo>#</mo><mi>b</mi></mrow></mfenced></math>";
		$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
		$question = $quizzes->newQuestion();
		$slot = $question->addNewSlot();
		$slot->addNewAuthorAnswer($correctAnswer);
		$slot->addNewAuthorAnswer($correctAnswer2);
		$algorithm = "<wiriscalc version=\"3.2\">\x0A" . "  <title>\x0A" . "    <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "      <mtext>UntitledÂ calc</mtext>\x0A" . "    </math>\x0A" . "  </title>\x0A" . "  <session version=\"3.0\" lang=\"en\">\x0A" . "    <task>\x0A" . "      <title>\x0A" . "        <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "          <mtext>SheetÂ 1</mtext>\x0A" . "        </math>\x0A" . "      </title>\x0A" . "      <group>\x0A" . "        <algorithm># Automatically generated by WirisQuizzes.\x0A" . "a = random(1, 5);\x0A" . "b = random(2, 5);\x0A" . "        </algorithm>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>\x0A" . "          </input>\x0A" . "        </command>\x0A" . "      </group>\x0A" . "    </task>\x0A" . "  </session>\x0A" . "</wiriscalc>";
		$question->setAlgorithm($algorithm);
		$instance = $quizzes->newQuestionInstance($question);
		$instance->setRandomSeed(1);
		$request = $quizzes->newVariablesRequestWithQuestionData($statement, $instance);
		$service = $quizzes->getQuizzesService();
		$service->executeAsync($request, new com_wiris_quizzes_test_TestIdServiceListener("evaluateRandomVariables1", $this, $question, $instance));
	}
	public function responseFeedback2($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!($qi->getAnswerGrade(0, 0, $q) === 1.0)) {
				throw new HException("Failed test!");
			}
		} else {
			$slots = $q->getSlots();
			$authorAnswers = _hx_array_get($slots, 0)->getAuthorAnswers();
			if(!($qi->getGrade($slots[0], $authorAnswers[0]) === 1.0)) {
				throw new HException("Failed test!");
			}
		}
		$t = $qi->expandVariables("#answer");
		if(!($t === "<math><mn>122</mn><mo>+</mn><mn>1</mn></math>")) {
			throw new HException("Failed test!");
		}
		$t = $qi->expandVariablesMathML("<math><mo>#</mo><mi>a</mi><mi>nswer</mi><mn>1</mn></math>");
		if(!($t === "<math><mrow><mn>122</mn><mo>+</mn><mn>1</mn></mrow></math>")) {
			throw new HException("Failed test!");
		}
	}
	public function responseFeedback($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!($qi->getAnswerGrade(0, 0, $q) === 1.0)) {
				throw new HException("Failed test!");
			}
		} else {
			$slots = $q->getSlots();
			$authorAnswers = _hx_array_get($slots, 0)->getAuthorAnswers();
			if(!($qi->getGrade($slots[0], $authorAnswers[0]) === 1.0)) {
				throw new HException("Failed test!");
			}
		}
		$t = $qi->expandVariablesText("#c");
		if(!($t === "124")) {
			throw new HException("Failed test!");
		}
		$t = $qi->expandVariables("#answer");
		if(!($t === "122+1")) {
			throw new HException("Failed test!");
		}
		$t = $qi->expandVariablesText("#answer1");
		if(!($t === "122+1")) {
			throw new HException("Failed test!");
		}
	}
	public function testFeedback() {
		$s = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>answer</mi><mo>=</mo><mn>0</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>c</mi><mo>=</mo><mi>answer</mi><mo>+</mo><mn>1</mn></math></input></command></group></library></session>";
		$html = "#c #answer #answer1";
		$q = null;
		$i = null;
		$r = null;
		$ss = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $b->newQuestion();
			$q->setAlgorithm($s);
			$q->setCorrectAnswer(0, "123");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER, "true");
			$i = $b->newQuestionInstance($q);
			$i->setStudentAnswer(0, "122+1");
			$r = $b->newFeedbackRequest($html, $q, $i);
			$ss = $b->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$q->setAlgorithm($s);
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer("123");
			$q->setProperty(com_wiris_quizzes_api_PropertyName::$STUDENT_ANSWER_PARAMETER, "true");
			$i = $quizzes->newQuestionInstance($q);
			$i->setSlotAnswer($slot, "122+1");
			$r = $quizzes->newFeedbackRequest($html, $i);
			$ss = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$ss->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("feedback", $this, $q, $i));
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$i = $b->newQuestionInstance($q);
			$i->setStudentAnswer(0, "<math><mn>122</mn><mo>+</mn><mn>1</mn></math>");
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$i = $quizzes->newQuestionInstance($q);
			$slots = $q->getSlots();
			$i->setSlotAnswer($slots[0], "<math><mn>122</mn><mo>+</mn><mn>1</mn></math>");
		}
		$this->numCalls++;
		$ss->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("feedback2", $this, $q, $i));
	}
	public function responseUnicode2($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if($qi->getAnswerGrade(0, 0, $q) !== 1.0) {
				throw new HException("Failed test!");
			}
		} else {
			$slots = $q->getSlots();
			$authorAnswers = _hx_array_get($slots, 0)->getAuthorAnswers();
			if($qi->getGrade($slots[0], $authorAnswers[0]) !== 1.0) {
				throw new HException("Failed test!");
			}
		}
	}
	public function responseUnicode1($s, $q, $qi) {
		$qi->update($s);
		$studentAnswer = "<math><mi>a</mi><mo>&#x2009;</mo><mo>=</mo><mo>&nbsp;</mo><mi mathvariant=\"normal\">&#120128;</mi><mspace linebreak=\"newline\"/><mi>b</mi><mo>&#x2009;</mo><mo>=</mo><mo>&nbsp;</mo><mi mathvariant=\"normal\">&#8474;</mi></math>";
		$r = null;
		$ss = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$correctAnswer = $qi->expandVariablesMathML($q->getCorrectAnswer(0));
			$r = $b->newEvalRequest($correctAnswer, $studentAnswer, $q, $qi);
			$ss = $b->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$slots = $q->getSlots();
			$qi->setSlotAnswer($slots[0], $studentAnswer);
			$r = $quizzes->newGradeRequest($qi);
			$ss = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$ss->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("unicode2", $this, $q, $qi));
	}
	public function testUnicode() {
		$correctAnswer = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi mathvariant=\"normal\">&#x1d540;</mi><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mo>#</mo><mi>S</mi></math>";
		$algorithm = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>S</mi><mo>=</mo><rationals/></math></input></command></group></library></session>";
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $b->newQuestion()->getImpl();
			$q->setCorrectAnswer(0, $correctAnswer);
			$q->setAlgorithm($algorithm);
			$q->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE);
			$qi = $b->newQuestionInstance($q);
			$r = $b->newVariablesRequest($correctAnswer, $q, $qi);
			$s = $b->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$q->addNewSlot()->addNewAuthorAnswer($correctAnswer);
			$q->setAlgorithm($algorithm);
			$q->setProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE);
			$qi = $quizzes->newQuestionInstance($q);
			$r = $quizzes->newVariablesRequest($correctAnswer, $qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("unicode1", $this, $q, $qi));
	}
	public function responseParameters($res, $q, $qi) {
		$qi->update($res);
		$html = "<p>Es cert o fals?</p><p>#s</p>";
		$expected = "<p>Es cert o fals?</p><p>El polinomi <math><mrow><msup><mi>x</mi><mn>3</mn></msup><mo>-</mo><msup><mi>x</mi><mn>2</mn></msup><mo>+</mo><mn>2</mn></mrow></math> te grau <math><mrow><mn>3</mn></mrow></math>.</p>";
		$computed = $qi->expandVariables($html);
		if(!($computed === $expected)) {
			throw new HException("Failed test! \x0AComputed: " . $computed . "\x0A" . "Expected: " . $expected);
		}
	}
	public function testParameters() {
		$str = "<question><wirisCasSession><![CDATA[<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>sentence</mi><mo>=</mo><mo>&quot;</mo><mi>The</mi><mo>&nbsp;</mo><mi>polynomial</mi><mo>&nbsp;</mo><mo>#</mo><mn>1</mn><mo>&nbsp;</mo><mi>has</mi><mo>&nbsp;</mo><mi>degree</mi><mo>&nbsp;</mo><mo>#</mo><mn>2</mn><mo>.</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parameter</mi><mo>&nbsp;</mo><mi>p</mi><mo>=</mo><msup><mi>x</mi><mn>2</mn></msup><mo>-</mo><mi>x</mi><mo>-</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>d</mi><mo>=</mo><mi>degree</mi><mo>(</mo><mi>p</mi><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>s</mi><mo>=</mo><mi>string_substitution</mi><mo>(</mo><mi>sentence</mi><mo>,</mo><mo>&nbsp;</mo><mi>p</mi><mo>,</mo><mo>&nbsp;</mo><mi>d</mi><mo>)</mo></math></input></command></group></library></session>]]></wirisCasSession></question>";
		$html = "<p>Es cert o fals?</p><p>#s</p>";
		$q = null;
		$qi = null;
		$s = null;
		$req = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $b->readQuestion($str);
			$qi = $b->newQuestionInstance($q);
			$req = $b->newVariablesRequest($html, $q, $qi);
			$s = $b->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->readQuestion($str);
			$qi = $quizzes->newQuestionInstance($q);
			$req = $quizzes->newVariablesRequest($html, $qi);
			$s = $quizzes->getQuizzesService();
		}
		$qi->setParameter("sentence", "<ms>El polinomi #1 te grau #2.</ms>");
		$qi->setParameter("p", "x^3-x^2+2");
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("parameters", $this, $q, $qi));
	}
	public function responseUserId($res, $q, $qi) {
		$qi->update($res);
		$a = $qi->expandVariablesText("#a");
		if(!($a === "697")) {
			throw new HException("Failed test user_id! \x0AComputed: " . $a . "\x0A" . "Expected: 697");
		}
	}
	public function testUserId() {
		$session = "<session lang=\"ca\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"ca\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>parÃ metre</mi><mo>&nbsp;</mo><mi>id_usuari</mi><mo>=</mo><mn>0</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>llavor_aleatori</mi><mo>(</mo><mi>id_usuari</mi><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>aleatori</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>1000</mn><mo>)</mo></math></input></command></group></library><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>697</mn></math></output></command></group><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></session>";
		$q = null;
		$qi = null;
		$req = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $b->newQuestion();
			$q->setAlgorithm($session);
			$qi = $b->newQuestionInstance($q);
			$qi->setParameter(com_wiris_quizzes_api_QuizzesConstants::$PARAMETER_USER_ID, "123");
			$req = $b->newVariablesRequest("#a", $q, $qi);
			$s = $b->getQuizzesService();
		} else {
			$qq = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $qq->newQuestion();
			$q->setAlgorithm($session);
			$qi = $qq->newQuestionInstance($q);
			$qi->setParameter(com_wiris_quizzes_api_QuizzesConstants::$PARAMETER_USER_ID, "123");
			$req = $qq->newVariablesRequest("#a", $qi);
			$s = $qq->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("userid", $this, $q, $qi));
	}
	public function testDeprecated() {
		if($this->apiVersion !== com_wiris_quizzes_test_Tester::$QUIZZES3) {
			return;
		}
		$str = "<question><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>s</mi></math>]]></correctAnswer><correctAnswer id=\"1\"></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_set\"/></assertions><options><option name=\"tolerance\">10^(-4)</option><option name=\"relative_tolerance\">false</option><option name=\"precision\">4</option><option name=\"implicit_times_operator\">false</option><option name=\"times_operator\">Â·</option><option name=\"imaginary_unit\">i</option></options><localData><data name=\"inputField\">inlineEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"casSession\"/></localData></question>";
		$b = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
		$q = $b->readQuestion($str);
		$qi = $b->newQuestionInstance($q);
		$eqs = $b->newEvalRequest("{{x=0},{x=1}}", "{{x=1},{x=0}}", $q, $qi);
		$this->numCalls++;
		$b->getQuizzesService()->executeAsync($eqs, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, null, null));
	}
	public function responseBugs1($s, $q, $qi) {
		$qi->update($s);
		$a = $qi->expandVariablesText("#a");
		if(!($a === "10")) {
			throw new HException(new com_wiris_system_Exception("Failed test", null));
		}
	}
	public function testBugs() {
		if($this->apiVersion !== com_wiris_quizzes_test_Tester::$QUIZZES3) {
			return;
		}
		$str = "<question><wirisCasSession><![CDATA[<session lang=\"fr\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\">library</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>alÃ©a</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>10</mn><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mi>alÃ©a</mi><mo>(</mo><mo>-</mo><mn>10</mn><mo>.</mo><mo>.</mo><mn>1</mn><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mi>max</mi><mo>(</mo><mi>b</mi><mo>,</mo><mn>0</mn><mo>)</mo></math></input></command></group></library><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></session>]]></wirisCasSession><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>a</mi></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-4)</option><option name=\"relative_tolerance\">false</option><option name=\"precision\">4</option><option name=\"implicit_times_operator\">false</option><option name=\"times_operator\">Â·</option><option name=\"imaginary_unit\">i</option>\x09</options>\x09<localData><data name=\"inputField\">inlineEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"/><data name=\"casSession\"/></localData></question>";
		$b = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
		$q = $b->readQuestion($str);
		$qi = $b->newQuestionInstance($q);
		$qi->setRandomSeed(12345);
		$r = $b->newVariablesRequest("#a", $q, $qi);
		$s = $b->getQuizzesService();
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("bugs1", $this, $q, $qi));
		$q = $b->newQuestion();
		$q->addAssertion("syntax_quantity", 0, 0, new _hx_array(array(null, null, null, null, "true", "", ".", "\\,,\\s", null)));
		$r = $b->newEvalRequest("<math><mn>1234567</mn><mo>.</mo><mn>8</mn></math>", "<math><mn>1</mn><mo>,</mo><mn>234</mn><mo>,</mo><mn>567</mn><mo>.</mo><mn>8</mn></math>", $q, null);
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, null));
		$r = $b->newEvalRequest("<math><mn>1234567</mn><mo>.</mo><mn>8</mn></math>", "<math><mn>1</mn><mo>&#160;</mo><mn>234</mn><mo>&#160;</mo><mn>567</mn><mo>.</mo><mn>8</mn></math>", $q, null);
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, null));
		$r = $b->newEvalRequest("<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfrac><mn>4</mn><mn>9</mn></mfrac><mo>+</mo><mfenced open=\"[\" close=\"]\"><mrow><mfrac><mn>1</mn><mn>4</mn></mfrac><mo>+</mo><mfenced close=\")\" open=\"(\"><mrow><mfrac><mn>1</mn><mn>4</mn></mfrac><mo>+</mo><mfrac><mn>1</mn><mn>5</mn></mfrac></mrow></mfenced><mo>&#183;</mo><mfenced close=\")\" open=\"(\"><mrow><mfrac><mn>4</mn><mn>3</mn></mfrac><mo>+</mo><mn>2</mn></mrow></mfenced></mrow></mfenced><mo>:</mo><mfenced close=\")\" open=\"(\"><mfrac><mn>3</mn><mn>2</mn></mfrac></mfenced></math>", "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mfrac><mn>4</mn><mn>9</mn></mfrac><mo>+</mo><mfenced open=\"[\" close=\"]\"><mrow><mfrac><mn>1</mn><mn>4</mn></mfrac><mo>+</mo><mfenced close=\")\" open=\"(\"><mrow><mfrac><mn>1</mn><mn>4</mn></mfrac><mo>+</mo><mfrac><mn>1</mn><mn>5</mn></mfrac></mrow></mfenced><mo>&#183;</mo><mfenced close=\")\" open=\"(\"><mrow><mfrac><mn>4</mn><mn>3</mn></mfrac><mo>+</mo><mn>2</mn></mrow></mfenced></mrow></mfenced><mo>:</mo><mfenced close=\")\" open=\"(\"><mfrac><mn>3</mn><mn>2</mn></mfrac></mfenced></math>", $q, null);
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, null));
	}
	public function testCache() {
		$qstr = "<question id=\"cachemiss\"><wirisCasSession><![CDATA[<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>random</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>10</mn><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mi>random</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>10</mn><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>c</mi><mo>=</mo><mi>a</mi><mo>+</mo><mi>b</mi></math></input></command></group></library></session>]]></wirisCasSession><correctAnswers><correctAnswer></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-4)</option><option name=\"relative_tolerance\">false</option><option name=\"precision\">4</option><option name=\"implicit_times_operator\">false</option><option name=\"times_operator\">Â·</option><option name=\"imaginary_unit\">i</option></options><localData><data name=\"inputField\">inlineEditor</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"casSession\"/></localData></question>";
		$text = "#a  + #b = #c";
		$q = null;
		$qi = null;
		$s = null;
		$r = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$s = $b->getQuizzesService();
			$q = $b->readQuestion($qstr);
			$qi = $b->newQuestionInstance($q);
			$r = $b->newVariablesRequest($text, $q, $qi);
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$s = $quizzes->getQuizzesService();
			$q = $quizzes->readQuestion($qstr);
			$qi = $quizzes->newQuestionInstance($q);
			$r = $quizzes->newVariablesRequest($text, $qi);
		}
		$s1 = Date::now()->getTime();
		$qi->update($s->execute($r));
		$t1 = Date::now()->getTime() - $s1;
		$str1 = $qi->expandVariables($text);
		if(_hx_index_of($str1, "#", null) !== -1 || _hx_index_of($str1, "<math", null) === -1) {
			throw new HException("Failed test");
		}
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$qi = $b->newQuestionInstance($q);
			$r = $b->newVariablesRequest($text, $q, $qi);
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$qi = $quizzes->newQuestionInstance($q);
			$r = $quizzes->newVariablesRequest($text, $qi);
		}
		$s2 = Date::now()->getTime();
		$qi->update($s->execute($r));
		$t2 = Date::now()->getTime() - $s2;
		$str2 = $qi->expandVariables($text);
		if(_hx_index_of($str2, "#", null) !== -1 || _hx_index_of($str2, "<math", null) === -1) {
			throw new HException("Failed test");
		}
		if($t2 >= $t1) {
			haxe_Log::trace("WARNING: Uncached question was faster than cached one! time miss: " . _hx_string_rec($t1, "") . "ms, time hit: " . _hx_string_rec($t2, "") . "ms.", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 1605, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "testCache")));
		}
	}
	public function testFilter() {
		$text = "Hola <math><mn>3</mn></math> hola <math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msqrt><mn>3</mn></msqrt></math>";
		$filter = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->getMathFilter() : com_wiris_quizzes_api_Quizzes::getInstance()->getMathFilter());
		$filtered = $filter->filter($text);
		$configuration = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->getConfiguration() : com_wiris_quizzes_api_Quizzes::getInstance()->getConfiguration());
		$expected = "Hola <img src=\"" . $configuration->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL) . "?service=cache&amp;name=5ca7d1107389675d32b56ec097464c14.png\" align=\"middle\" /> hola <img src=\"" . com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL) . "?service=cache&amp;name=61eed805aa7caf23565ff147e24a35df.png\" align=\"middle\" />";
		if(!($expected === $filtered)) {
			throw new HException("Failed test filter. \x0AGot: " . $filtered . "\x0AExpected: " . $expected . "\x0A");
		}
	}
	public function testPerformance() {
		$text = "<p><span style=\"font-family: 'times new roman', times, serif; font-size: medium;\">Rounded to the nearest tenth of a foot, a #F foot mountain peak is _____ miles tall.  <br /></span></p>" . "<p><span style=\"text-decoration: underline;\"><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">UNIT</span><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"> CONVERSIONS</span></strong></span></p>\x0A" . "<ul>\x0A" . "<li><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">When converting from a larger to a smaller unit, multiply.</span></li>\x0A" . "<li><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">When converting from a smaller to a larger unit, divide.</span></li>\x0A" . "</ul>\x0A" . "<p><strong><span style=\"text-decoration: underline;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">COMMON MEASUREMENT CONVERSIONS AND FACTS</span></span></strong></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Length</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Customary:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 foot <em>(</em><em>ft</em><em>)</em> = 12 inches <em>(in)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 yard <em>(yd)</em> = 3 feet <em>(ft)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 mile <em>(mi)</em> = 5,280 feet <em>(ft)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Metric:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 centimeter <em>(cm</em><em>)</em> = 10 millimeters <em>(mm)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 meter <em>(m)</em> = 100 centimeters <em>(cm)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 kilometer <em>(km)</em> = 1,000 meters <em>(m)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure length: ruler, yard stick, meter stick, measuring tape</span></p>\x0A" . "<p><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><strong><span style=\"text-decoration: underline;\">Time</span>:</strong><br /></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 day = 24 hours <em>(hrs)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 hour <em>(hr)</em> = 60 minutes <em>(min)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 minute <em>(min)</em> = 60 seconds <em>(sec)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 week = 7 days</span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 year = 365 days</span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 year = 52 weeks</span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure time: clock, calendar</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Mass</span> (Metric):</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 gram <em>(g</em><em>)</em> = 1,000 milligrams <em>(mg)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 kilogram <em>(kg)</em> = 1,000 grams <em>(g)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure mass: scale</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Weight</span> (Customary):</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 pound <em>(lb</em><em>)</em> = 16 ounces <em>(oz)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 ton = 2,000 pounds <em>(lbs)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure weight: scale</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Volume (Capacity)</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Customary:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 cup <em>(c</em><em>)</em> = 8 fluid ounces <em>(oz)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 pint <em>(pt)</em> = 2 cups <em>(c)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 quart <em>(qt)</em> = 2 pints <em>(pt)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 gallon <em>(gal)</em> = 4 quarts <em>(qt)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Metric:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 liter <em>(L</em><em>)</em> = 1,000 milliliters <em>(ml)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure volume: measuring cups</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Angle</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Line = 180Â°</span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Circle = 360Â°</span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure angle: protractor</span></p>" . "<p><strong><span style=\"background-color: #aaffaa;\">#CF</span></strong></p>" . "<p><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Your answer is correct, but it is not rounded to the nearest tenth.</span></p>" . "<p><span style=\"text-decoration: underline;\"><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">UNIT</span><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"> CONVERSIONS</span></strong></span></p>\x0A" . "<ul>\x0A" . "<li><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">When converting from a larger to a smaller unit, multiply.</span></li>\x0A" . "<li><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">When converting from a smaller to a larger unit, divide.</span></li>\x0A" . "</ul>\x0A" . "<p><strong><span style=\"text-decoration: underline;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">COMMON MEASUREMENT CONVERSIONS AND FACTS</span></span></strong></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Length</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Customary:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 foot <em>(</em><em>ft</em><em>)</em> = 12 inches <em>(in)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 yard <em>(yd)</em> = 3 feet <em>(ft)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 mile <em>(mi)</em> = 5,280 feet <em>(ft)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Metric:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 centimeter <em>(cm</em><em>)</em> = 10 millimeters <em>(mm)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 meter <em>(m)</em> = 100 centimeters <em>(cm)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 kilometer <em>(km)</em> = 1,000 meters <em>(m)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure length: ruler, yard stick, meter stick, measuring tape</span></p>\x0A" . "<p><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><strong><span style=\"text-decoration: underline;\">Time</span>:</strong><br /></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 day = 24 hours <em>(hrs)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 hour <em>(hr)</em> = 60 minutes <em>(min)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 minute <em>(min)</em> = 60 seconds <em>(sec)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 week = 7 days</span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 year = 365 days</span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 year = 52 weeks</span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure time: clock, calendar</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Mass</span> (Metric):</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 gram <em>(g</em><em>)</em> = 1,000 milligrams <em>(mg)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 kilogram <em>(kg)</em> = 1,000 grams <em>(g)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure mass: scale</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Weight</span> (Customary):</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 pound <em>(lb</em><em>)</em> = 16 ounces <em>(oz)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 ton = 2,000 pounds <em>(lbs)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure weight: scale</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Volume (Capacity)</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Customary:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 cup <em>(c</em><em>)</em> = 8 fluid ounces <em>(oz)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 pint <em>(pt)</em> = 2 cups <em>(c)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 quart <em>(qt)</em> = 2 pints <em>(pt)</em></span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 gallon <em>(gal)</em> = 4 quarts <em>(qt)</em></span></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><em>Metric:</em></span><br /> <span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">1 liter <em>(L</em><em>)</em> = 1,000 milliliters <em>(ml)</em></span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tools used to measure volume: measuring cups</span></p>\x0A" . "<p><strong><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\"><span style=\"text-decoration: underline;\">Angle</span>:</span></strong></p>\x0A" . "<p style=\"padding-left: 30px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Line = 180Â°</span><br /><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Circle = 360Â°</span></p>\x0A" . "<p style=\"padding-left: 60px;\"><span style=\"background-color: #ffaaaa; font-family: 'times new roman', times, serif;\">Tool used to measure angle: protractor</span></p>";
		$session = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><apply><csymbol definitionURL=\"http://www.wiris.com/XML/csymbol\">repeat</csymbol><mtable><mtr><mtd><mi>f</mi><mo>(</mo><mo>)</mo><mo>=</mo><mi>random</mi><mo>(</mo><mn>10001</mn><mo>,</mo><mn>15999</mn><mo>)</mo></mtd></mtr><mtr><mtd><mi>F</mi><mo>=</mo><mi>f</mi><mo>(</mo><mo>)</mo></mtd></mtr><mtr><mtd><mi>m</mi><mo>=</mo><mfrac><mi>F</mi><mn>528</mn></mfrac></mtd></mtr><mtr><mtd><mi>M</mi><mo>=</mo><mi>round</mi><mo>(</mo><mi>m</mi><mo>)</mo><mo>*</mo><mn>0</mn><mo>.</mo><mn>1</mn></mtd></mtr><mtr><mtd><mi>Ans</mi><mo>=</mo><mi>M</mi></mtd></mtr><mtr><mtd><mi>N</mi><mo>=</mo><mi>round</mi><mo>(</mo><mi>M</mi><mo>)</mo></mtd></mtr><mtr><mtd><mi>P</mi><mo>=</mo><mfrac><mi>F</mi><mn>5280</mn></mfrac></mtd></mtr></mtable><mrow><mi>M</mi><mo>=</mo><mi>N</mi></mrow></apply></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>WATest</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>:=</mo><mo>(</mo><mi>x</mi><mo>&ne;</mo><mi>Ans</mi><mo>)</mo><mo>?</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>u</mi><mo>(</mo><mo>)</mo><mo>=</mo><mi>random</mi><mo>(</mo><mn>1</mn><mo>,</mo><mn>23</mn><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>U</mi><mo>=</mo><mi>u</mi><mo>(</mo><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>1</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Excellent</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>2</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Outstanding</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>3</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>You</mi><mo>&nbsp;</mo><mi>got</mi><mo>&nbsp;</mo><mi>it</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>4</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>That</mi><mo>&apos;</mo><mi>s</mi><mo>&nbsp;</mo><mi>it</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>5</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Correct</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>6</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>You</mi><mo>&nbsp;</mo><mi>rock</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>7</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Perfect</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>8</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Wow</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>9</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Phenomenal</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>10</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Superstar</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>11</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Amazing</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>12</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Nice</mi><mo>&nbsp;</mo><mi>going</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>13</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>You</mi><mo>&apos;</mo><mi>re</mi><mo>&nbsp;</mo><mi>a</mi><mo>&nbsp;</mo><mi>rock</mi><mo>&nbsp;</mo><mi>star</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>14</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Mathlete</mi><mo>&nbsp;</mo><mi>extraordinaire</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>15</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Right</mi><mo>&nbsp;</mo><mi>on</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>16</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Genius</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>17</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Tremendous</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>18</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Stupendous</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>19</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Magnificent</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>20</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Incredible</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>21</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>You</mi><mo>&nbsp;</mo><mi>win</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>22</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>You</mi><mo>&apos;</mo><mi>re</mi><mo>&nbsp;</mo><mi>on</mi><mo>&nbsp;</mo><mi>fire</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>Pos</mi><mo>(</mo><mn>23</mn><mo>)</mo><mo>=</mo><mo>&quot;</mo><mi>Awesome</mi><mo>!</mo><mo>&quot;</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>CF</mi><mo>=</mo><mi>Pos</mi><mo>(</mo><mi>U</mi><mo>)</mo></math></input></command></group></library></session>";
		$qb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
		$q = $qb->newQuestion();
		$qq = $q->getImpl();
		$qq->wirisCasSession = $session;
		$qi = $qb->newQuestionInstance($q);
		$r = $qb->newVariablesRequest($text, $q, $qi);
		$qi->update($qb->getQuizzesService()->execute($r));
		$expanded = $qi->expandVariables($text);
		haxe_Log::trace($expanded, _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 1536, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "testPerformance")));
	}
	public function responseTranslation1($s, $q) {
		$fr = "<session lang=\"fr\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\">library</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>alÃ©a</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>10</mn><mo>)</mo></math></input></command></group></library></session>";
		$qq = $q->getImpl();
		$qq->update($s);
		$tr = $qq->wirisCasSession;
		if(!($fr === trim($tr))) {
			throw new HException(new com_wiris_system_Exception("Expected: \x0A" . $fr . "\x0A Got:\x0A" . $tr . "\x0A", null));
		}
	}
	public function testTranslation() {
		$en = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\">library</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>random</mi><mo>(</mo><mn>1</mn><mo>.</mo><mo>.</mo><mn>10</mn><mo>)</mo></math></input></command></group></library></session>";
		$q = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$q = new com_wiris_quizzes_impl_QuestionImpl();
			$q->wirisCasSession = $en;
			$r = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->newTranslationRequest($q, "fr");
			$s = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getQuizzesService();
		} else {
			$q = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestion();
			$q->setAlgorithm($en);
			$r = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->newTranslationRequest($q->getImpl(), "fr");
			$s = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("translation1", $this, $q, null));
	}
	public function responseEncodings2($s, $q, $qi) {
		$texts = new _hx_array(array(com_wiris_quizzes_test_Tester_0($this, $q, $qi, $s) . " #a", "2 #b 3"));
		$results = new _hx_array(array(com_wiris_quizzes_test_Tester_1($this, $q, $qi, $s, $texts) . " 123", "2 < 3"));
		$qi->update($s);
		$i = null;
		{
			$_g1 = 0; $_g = $texts->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$expanded = $qi->expandVariablesText($texts[$i1]);
				if(!($expanded === $results[$i1])) {
					throw new HException(new com_wiris_system_Exception("Failed Test. Expected:\x0A" . $results[$i1] . ".\x0AGot:\x0A" . $expanded . "\x0A", null));
				}
				unset($i1,$expanded);
			}
		}
	}
	public function responseEncodings1($s, $q, $qi) {
		$text = "Encode #a.";
		$result = "Encode <math><mrow><mi mathvariant=\"normal\">&#960;</mi><mo>&#183;</mo><mfenced><mrow><mo>&#176;</mo><mo>'</mo></mrow></mfenced><mo>+</mo><mfenced><mrow><mi mathvariant=\"normal\">&#8477;</mi><mo>+</mo><mo>-&#8734;</mo><mo>+</mo><mi>x</mi><mo>&#183;</mo><mi>y</mi></mrow></mfenced><mo>+</mo><mi>&#920;</mi></mrow></math>.";
		$qi->update($s);
		$expanded = $qi->expandVariables($text);
		if(!($expanded === $result)) {
			throw new HException(new com_wiris_system_Exception("Failed Test. Expected:\x0A" . $result . ".\x0AGot:\x0A" . $expanded . "\x0A", null));
		}
	}
	public function testEncodings() {
		$session = "<session lang=\"en\" version=\"2.0\"><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>x</mi><mo>*</mo><mi>y</mi><mo>+</mo><reals/><cn>-&infin;</cn><mo>+</mo><pi/><mo>&nbsp;</mo><csymbol definitionURL=\"http://.../units/minute/angular\">&apos;</csymbol><csymbol definitionURL=\"http://.../units/degree/angular\">&deg;</csymbol><mi>&Theta;</mi></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><pi/><mo>*</mo><mfenced><mrow><csymbol definitionURL=\"http://.../units/degree/angular\">&deg;</csymbol><mo>&nbsp;</mo><csymbol definitionURL=\"http://.../units/minute/angular\">&apos;</csymbol></mrow></mfenced><mo>+</mo><mfenced><mrow><reals/><mo>+</mo><cn>-&infin;</cn><mo>+</mo><mi>x</mi><mo>*</mo><mi>y</mi></mrow></mfenced><mo>+</mo><mi>&Theta;</mi></math></output></command></group><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></session>";
		$text = "Encode #a.";
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$q = new com_wiris_quizzes_impl_QuestionImpl();
			$qi = new com_wiris_quizzes_impl_QuestionInstanceImpl();
			$q->wirisCasSession = $session;
			$r = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->newVariablesRequest($text, $q, $qi);
			$s = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getQuizzesService();
		} else {
			$q = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestion();
			$qi = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q);
			$q->setAlgorithm($session);
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newVariablesRequest($text, $qi);
			$s = com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("encodings1", $this, $q, $qi));
		$text2 = "â¢ #a";
		$text3 = "2 #b 3";
		$session2 = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>123</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mo>&quot;</mo><mo>&lt;</mo><mo>&quot;</mo></math></input></command></group></library></session>";
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$q = new com_wiris_quizzes_impl_QuestionImpl();
			$qi = new com_wiris_quizzes_impl_QuestionInstanceImpl();
			$q->wirisCasSession = $session2;
			$r = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->newVariablesRequest($text2 . " " . $text3, $q, $qi);
		} else {
			$q = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestion();
			$qi = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q);
			$q->setAlgorithm($session2);
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newVariablesRequest($text2 . " " . $text3, $qi);
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("encodings2", $this, $q, $qi));
	}
	public function responseRandomQuestion2($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!$qi->isAnswerCorrect(0)) {
				throw new HException(new com_wiris_system_Exception("Failed Test!", null));
			}
		} else {
			$slots = $q->getSlots();
			if(!$qi->isSlotAnswerCorrect($slots[0])) {
				throw new HException(new com_wiris_system_Exception("Failed Test!", null));
			}
		}
	}
	public function responseRandomQuestion1($s, $q, $qi) {
		$text = "Hello! How much is #b - #a?";
		$correctAnswer = "#a";
		$qi->update($s);
		$deliveryText = $qi->expandVariables($text);
		if(!($deliveryText === "Hello! How much is <math><mrow><mn>2</mn></mrow></math> - <math><mrow><mn>1</mn></mrow></math>?")) {
			throw new HException(new com_wiris_system_Exception("Failed Test!", null));
		}
		$userAnswer = "1";
		$ss = null;
		$eqr = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$eqr = $rb->newEvalRequest($correctAnswer, $userAnswer, $q, $qi);
			$ss = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$slots = $q->getSlots();
			$qi->setSlotAnswer($slots[0], $userAnswer);
			$eqr = $quizzes->newGradeRequest($qi);
			$ss = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$ss->executeAsync($eqr, new com_wiris_quizzes_test_TestIdServiceListener("randomquestion2", $this, $q, $qi));
	}
	public function testRandomQuestion() {
		$session = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mn>2</mn></math></input></command></group></library></session>";
		$correctAnswer = "#a";
		$text = "Hello! How much is #b - #a?";
		$q = null;
		$qi = null;
		$vqr = null;
		$quizzes = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$b = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $b->newQuestion();
			$q->setAlgorithm($session);
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION, 0, 0, null);
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, 0, 0, null);
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$CHECK_SIMPLIFIED, 0, 0, null);
			$qi = $b->newQuestionInstance($q);
			$vqr = $b->newVariablesRequest($text . " " . $correctAnswer, $q, $qi);
			$quizzes = $b->getQuizzesService();
		} else {
			$qq = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $qq->newQuestion();
			$q->setAlgorithm($session);
			$q->addNewSlot()->addNewAuthorAnswer($correctAnswer)->addNewValidation(com_wiris_quizzes_api_assertion_ValidationName::$CHECK_SIMPLIFIED);
			$qi = $qq->newQuestionInstance($q);
			$vqr = $qq->newVariablesRequest($text . " " . $correctAnswer, $qi);
			$quizzes = $qq->getQuizzesService();
		}
		$this->numCalls++;
		$quizzes->executeAsync($vqr, new com_wiris_quizzes_test_TestIdServiceListener("randomquestion1", $this, $q, $qi));
	}
	public function responseTolerance1($s, $q, $qi) {
		if($qi === null) {
			$qi = com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newQuestionInstance(com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newQuestion());
		}
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if($qi->isAnswerCorrect(0)) {
				throw new HException("Failed test!");
			}
		} else {
			$slots = $q->getSlots();
			if($qi->isSlotAnswerCorrect($slots[0])) {
				throw new HException("Failed test!");
			}
		}
	}
	public function testTolerance() {
		$correctAnswer = "<math><mfrac><mn>1</mn><mn>2</mn></mfrac></math>";
		$userAnswer = "0.501";
		$q = null;
		$qi = null;
		$req = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion()->getImpl();
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, "10^(-3)");
			$req = $rb->newEvalRequest($correctAnswer, $userAnswer, $q, null);
			$s = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer($correctAnswer)->getComparison()->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$TOLERANCE, "10^(-3)");
			$qi = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q);
			$qi->setSlotAnswer($slot, $userAnswer);
			$req = $quizzes->newGradeRequest($qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("tolerance1", $this, $q, $qi));
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion()->getImpl();
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_TOLERANCE, "10^(-2)");
			$req = $rb->newEvalRequest($correctAnswer, $userAnswer, $q, null);
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer($correctAnswer)->getComparison()->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$TOLERANCE, "10^(-2)");
			$qi = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q);
			$qi->setSlotAnswer($slot, $userAnswer);
			$req = $quizzes->newGradeRequest($qi);
		}
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, $qi));
	}
	public function responseOpenQuestion1($s) {
		$qi = com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newQuestionInstance(com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newQuestion());
		$qi->update($s);
		$correct = $qi->isAnswerCorrect(0);
		if(!$correct) {
			throw new HException("Failed test!");
		}
	}
	public function testOpenQuestionHand() {
		$correctAnswer = "1234";
		$userAnswer = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><semantics><mrow><mn>1234</mn></mrow><annotation encoding=\"application/json\">[[[47,74],[48,78],[50,84],[52,100],[53,119],[54,137],[55,153],[56,164],[58,169],[59,171],[60,172]],[[95,92],[96,92],[96,91],[96,90],[98,89],[101,87],[109,83],[129,77],[140,75],[147,74],[150,74],[151,75],[152,82],[152,94],[149,112],[140,130],[132,143],[128,150],[127,154],[128,154],[129,154],[130,154],[133,154],[143,153],[157,152],[170,152],[178,152],[181,152],[181,151]],[[204,85],[204,84],[206,83],[208,81],[210,81],[218,78],[231,76],[237,76],[241,76],[244,78],[246,81],[248,87],[248,94],[248,104],[242,113],[235,120],[230,123],[228,123],[229,123],[231,123],[234,123],[243,123],[252,124],[259,125],[266,130],[270,134],[272,140],[272,149],[268,153],[262,156],[255,157],[249,157],[242,157],[237,155],[234,153],[233,152],[233,151]],[[354,74],[353,75],[351,79],[346,82],[330,98],[319,110],[309,122],[302,130],[300,135],[299,136],[300,136],[302,136],[306,136],[317,136],[333,136],[365,137],[383,139],[390,141],[391,141]],[[366,165],[366,163],[366,159],[366,147],[366,122],[366,114],[361,62],[361,58],[360,50],[360,48]]]</annotation></semantics></math>";
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$r = $rb->newEvalRequest($correctAnswer, $userAnswer, null, null);
			$s = $rb->getQuizzesService();
		} else {
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newSimpleGradeRequest($correctAnswer, $userAnswer);
			$s = com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, null, null));
	}
	public function testOpenQuestion() {
		$correctAnswer = "1+1";
		$userAnswer = "2";
		$quizzes = null;
		$eqs = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$rb->getConfiguration()->set(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL, "hudson.wiris.info");
			$eqs = $rb->newEvalRequest($correctAnswer, $userAnswer, null, null);
			$quizzes = $rb->getQuizzesService();
		} else {
			$q = com_wiris_quizzes_api_Quizzes::getInstance();
			$q->getConfiguration()->set(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL, "hudson.wiris.info");
			$eqs = $q->newSimpleGradeRequest($correctAnswer, $userAnswer);
			$quizzes = $q->getQuizzesService();
		}
		$this->numCalls++;
		$quizzes->executeAsync($eqs, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, null, null));
	}
	public function testAssertion() {
		$q = null;
		$qi = null;
		$eqs = null;
		$s = null;
		$correct = "x^2=1";
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion();
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$SYNTAX_EXPRESSION, 0, 0, new _hx_array(array(null, null, null, null, null, null, null, null, null, "true")));
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_EQUATIONS, 0, 0, null);
			$eqs = $rb->newEvalRequest($correct, "x=1 or x=-1", $q, null);
			$s = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer($correct)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$EQUIVALENT_EQUATIONS);
			$qi = $quizzes->newQuestionInstance($q);
			$qi->setSlotAnswer($slot, "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><mn>1</mn><mo>&#x2228;</mo><mi>x</mi><mo>=</mo><mo>-</mo><mn>1</mn></math>");
			$eqs = $quizzes->newGradeRequest($qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($eqs, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, $qi));
	}
	public function responseFloatFormat1($r, $q, $qi) {
		$qi->update($r);
		$computed = new _hx_array(array($qi->expandVariablesText("#a"), $qi->expandVariablesText("#b")));
		$expected = new _hx_array(array("123.456.789", "1.234,57"));
		$i = null;
		{
			$_g1 = 0; $_g = $computed->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(!($computed[$i1] === $expected[$i1])) {
					throw new HException("Failed text! Got " . $computed[$i1] . " instead of " . $expected[$i1] . ".");
				}
				unset($i1);
			}
		}
	}
	public function testFloatFormat() {
		$q = null;
		$qi = null;
		$req = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion();
			$qi = $rb->newQuestionInstance($q);
			$q->setAlgorithm("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>123456789</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mn>1234</mn><mo>.</mo><mn>56789</mn></math></input></command></group></library></session>");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DECIMAL_SEPARATOR, ",");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_FLOAT_FORMAT, ",f");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_PRECISION, "2");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DIGIT_GROUP_SEPARATOR, ".");
			$req = $rb->newVariablesRequest("#a #b", $q, $qi);
			$s = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$qi = $quizzes->newQuestionInstance($q);
			$q->setAlgorithm("<wiriscalc version=\"3.2\">\x0A" . "  <title>\x0A" . "    <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "      <mtext>Untitled&#xa0;calc</mtext>\x0A" . "    </math>\x0A" . "  </title>\x0A" . "  <properties>\x0A" . "    <property name=\"decimal_separator\">,</property>\x0A" . "    <property name=\"digit_group_separator\">.</property>\x0A" . "    <property name=\"float_format\">,f</property>\x0A" . "    <property name=\"imaginary_unit\">i</property>\x0A" . "    <property name=\"implicit_times_operator\">false</property>\x0A" . "    <property name=\"item_separator\">Â </property>\x0A" . "    <property name=\"lang\">en</property>\x0A" . "    <property name=\"precision\">2</property>\x0A" . "    <property name=\"quizzes_question_options\">true</property>\x0A" . "    <property name=\"save_settings_in_cookies\">false</property>\x0A" . "    <property name=\"times_operator\">Â·</property>\x0A" . "    <property name=\"use_degrees\">false</property>\x0A" . "  </properties>\x0A" . "  <session version=\"3.0\" lang=\"en\">\x0A" . "    <task>\x0A" . "      <title>\x0A" . "        <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "          <mtext>SheetÂ 1</mtext>\x0A" . "        </math>\x0A" . "      </title>\x0A" . "      <group>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mi mathvariant=\"normal\">a</mi>\x0A" . "              <mo>=</mo>\x0A" . "              <mn>123</mn>\x0A" . "              <mo>.</mo>\x0A" . "              <mn>456</mn>\x0A" . "              <mo>.</mo>\x0A" . "              <mn>789</mn>\x0A" . "            </math>\x0A" . "          </input>\x0A" . "          <output>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mrow>\x0A" . "                <mn>123</mn>\x0A" . "                <mo>.</mo>\x0A" . "                <mn>456</mn>\x0A" . "                <mo>.</mo>\x0A" . "                <mn>789</mn>\x0A" . "              </mrow>\x0A" . "            </math>\x0A" . "          </output>\x0A" . "        </command>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mi mathvariant=\"normal\">b</mi>\x0A" . "              <mo>=</mo>\x0A" . "              <mn>1</mn>\x0A" . "              <mo>.</mo>\x0A" . "              <mn>234</mn>\x0A" . "              <mo>,</mo>\x0A" . "              <mn>56789</mn>\x0A" . "            </math>\x0A" . "          </input>\x0A" . "          <output>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mrow>\x0A" . "                <mn>1</mn>\x0A" . "                <mo>.</mo>\x0A" . "                <mn>234</mn>\x0A" . "                <mo>,</mo>\x0A" . "                <mn>57</mn>\x0A" . "              </mrow>\x0A" . "            </math>\x0A" . "          </output>\x0A" . "        </command>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>\x0A" . "          </input>\x0A" . "        </command>\x0A" . "      </group>\x0A" . "    </task>\x0A" . "  </session>\x0A" . "  <constructions>\x0A" . "    <construction group=\"1\">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}</construction>\x0A" . "  </constructions>\x0A" . "</wiriscalc>");
			$req = $quizzes->newVariablesRequest("#a #b", $qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("floatformat1", $this, $q, $qi));
	}
	public function responseFloatEval1($r, $q, $qi) {
		$qi->update($r);
		$res = $qi->expandVariablesMathML("#a");
		$expanded = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mrow><mrow><mn>1000000</mn></mrow></mrow></math>";
		if(!($res === $expanded)) {
			throw new HException("Failed test! Got: " . $res . " instead of " . $expanded . ".");
		}
		if($this->apiVersion !== com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer("#a");
			$qi->setSlotAnswer($slot, "1000001");
		}
		$req = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newEvalRequest("#a", "1000001", $q, $qi) : com_wiris_quizzes_api_Quizzes::getInstance()->newGradeRequest($qi));
		$this->numCalls++;
		$s = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->getQuizzesService() : com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesService());
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("openquestion1", $this, $q, $qi));
	}
	public function testFloatEval() {
		$q = null;
		$qi = null;
		$req = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion();
			$qi = $rb->newQuestionInstance($q);
			$q->setAlgorithm("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1000000</mn><mo>.</mo></math></input></command></group></library></session>");
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$SYNTAX_QUANTITY, 0, 0, null);
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_DECIMAL_SEPARATOR, ",");
			$q->setOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_FLOAT_FORMAT, "mr");
			$req = $rb->newVariablesRequest("#a", $q, $qi);
			$s = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$qi = $quizzes->newQuestionInstance($q);
			$q->setAlgorithm("<wiriscalc version=\"3.2\">\x0A" . "  <title>\x0A" . "    <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "      <mtext>Untitled&#xa0;calc</mtext>\x0A" . "    </math>\x0A" . "  </title>\x0A" . "  <properties>\x0A" . "    <property name=\"decimal_separator\">.</property>\x0A" . "    <property name=\"digit_group_separator\">\x0A" . "    </property>\x0A" . "    <property name=\"float_format\">mr</property>\x0A" . "    <property name=\"imaginary_unit\">i</property>\x0A" . "    <property name=\"implicit_times_operator\">false</property>\x0A" . "    <property name=\"item_separator\">,</property>\x0A" . "    <property name=\"lang\">en</property>\x0A" . "    <property name=\"precision\">4</property>\x0A" . "    <property name=\"quizzes_question_options\">true</property>\x0A" . "    <property name=\"save_settings_in_cookies\">false</property>\x0A" . "    <property name=\"times_operator\">Â·</property>\x0A" . "    <property name=\"use_degrees\">false</property>\x0A" . "  </properties>\x0A" . "  <session version=\"3.0\" lang=\"en\">\x0A" . "    <task>\x0A" . "      <title>\x0A" . "        <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "          <mtext>SheetÂ 1</mtext>\x0A" . "        </math>\x0A" . "      </title>\x0A" . "      <group>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mi mathvariant=\"normal\">a</mi>\x0A" . "              <mo>=</mo>\x0A" . "              <mn>1000000</mn>\x0A" . "              <mo>.</mo>\x0A" . "            </math>\x0A" . "          </input>\x0A" . "          <output>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "              <mrow>\x0A" . "                <mn>1000000</mn>\x0A" . "                <mo>.</mo>\x0A" . "              </mrow>\x0A" . "            </math>\x0A" . "          </output>\x0A" . "        </command>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>\x0A" . "          </input>\x0A" . "        </command>\x0A" . "      </group>\x0A" . "    </task>\x0A" . "  </session>\x0A" . "  <constructions>\x0A" . "    <construction group=\"1\">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}</construction>\x0A" . "  </constructions>\x0A" . "</wiriscalc>");
			$req = $quizzes->newVariablesRequest("#a", $qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($req, new com_wiris_quizzes_test_TestIdServiceListener("floateval1", $this, $q, $qi));
	}
	public function responseAnyAnswer1($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if($qi->getAnswerGrade(0, 0, $q) !== 0.0) {
				throw new HException("Failed test!");
			}
			if($qi->getAnswerGrade(1, 0, $q) !== 1.0) {
				throw new HException("Failed test!");
			}
		} else {
			$slots = $q->getSlots();
			$authorAnswers = _hx_array_get($slots, 0)->getAuthorAnswers();
			if($qi->getGrade($slots[0], $authorAnswers[0]) !== 0.0) {
				throw new HException("Failed test!");
			}
			if($qi->getGrade($slots[0], $authorAnswers[1]) !== 1.0) {
				throw new HException("Failed test!");
			}
		}
	}
	public function testAnyAnswer() {
		$correctAnswer = "123";
		$correctAnswer2 = "*";
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion();
			$q->setCorrectAnswer(0, $correctAnswer);
			$q->setCorrectAnswer(1, $correctAnswer2);
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, 0, 0, null);
			$q->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_ALL, 1, 0, null);
			$qi = $rb->newQuestionInstance($q);
			$qi->setStudentAnswer(0, "++++");
			$r = $rb->newEvalRequest(null, null, $q, $qi);
			$s = $rb->getQuizzesService();
		} else {
			$b = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $b->newQuestion();
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer($correctAnswer);
			$slot->addNewAuthorAnswer($correctAnswer2)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$ANY_ANSWER);
			$qi = $b->newQuestionInstance($q);
			$qi->setSlotAnswer($slot, "++++");
			$r = $b->newGradeRequest($qi);
			$s = $b->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("anyanswer1", $this, $q, $qi));
	}
	public function responseImages2($s, $q, $qi) {
		$text = "#p";
		$md5s = new _hx_array(array("1d35f135d9d9e9a3596c4143ac6b10bf", "4e0fa2b5e6d8b12ce9fda6541fdb5557", "d12e3d4c916bf89de659e0d53002dc8e", "49f8df1ac28ce771814d9f968ea13e36", "bb1be75495f251c3a3dc7098a0f9d9ef"));
		$qi->update($s);
		$res = $qi->expandVariables($text);
		if(!$this->checkImage($res, $md5s)) {
			throw new HException("Failed test!. Got:\x0A" . $res);
		}
	}
	public function checkImage($res, $md5s) {
		$configuration = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance()->getConfiguration() : com_wiris_quizzes_api_Quizzes::getInstance()->getConfiguration());
		$expandedPre = "<img src=\"" . $configuration->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL) . "?service=cache&amp;name=";
		$expandedPost = ".png\" class=\"wirisplotter\"/>";
		$match = false;
		$i = null;
		{
			$_g1 = 0; $_g = $md5s->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($res === $expandedPre . $md5s[$i1] . $expandedPost) {
					$match = true;
				}
				unset($i1);
			}
		}
		return $match;
	}
	public function responseImages1($s, $q, $qi) {
		$text = "#p";
		$md5s = new _hx_array(array("1d35f135d9d9e9a3596c4143ac6b10bf", "4e0fa2b5e6d8b12ce9fda6541fdb5557", "d12e3d4c916bf89de659e0d53002dc8e", "49f8df1ac28ce771814d9f968ea13e36", "bb1be75495f251c3a3dc7098a0f9d9ef"));
		$qi->update($s);
		$res = $qi->expandVariables($text);
		if(!$this->checkImage($res, $md5s)) {
			throw new HException("Failed test!. Got:\x0A" . $res);
		}
		$r = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newVariablesRequest(null, $q, $qi) : com_wiris_quizzes_api_Quizzes::getInstance()->newVariablesRequest(null, $qi));
		$ss = (($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) ? com_wiris_quizzes_api_QuizzesBuilder::getInstance()->getQuizzesService() : com_wiris_quizzes_api_Quizzes::getInstance()->getQuizzesService());
		$this->numCalls++;
		$ss->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("images2", $this, $q, $qi));
	}
	public function testImages() {
		$algorithm = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>p</mi><mo>&nbsp;</mo><mo>=</mo><mo>&nbsp;</mo><mi>plot</mi><mo>(</mo><mi>sin</mi><mo>(</mo><mi>x</mi><mo>)</mo><mo>)</mo></math></input></command></group></library></session>";
		$text = "#p";
		$qi = null;
		$r = null;
		$s = null;
		$q = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $rb->newQuestion();
			$q->setAlgorithm($algorithm);
			$qi = $rb->newQuestionInstance($q);
			$r = $rb->newVariablesRequest($text, $q, $qi);
			$s = $rb->getQuizzesService();
		} else {
			$qq = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $qq->newQuestion();
			$q->setAlgorithm($algorithm);
			$qi = $qq->newQuestionInstance($q);
			$r = $qq->newVariablesRequest($text, $qi);
			$s = $qq->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("images1", $this, $q, $qi));
	}
	public function responseLang1($s, $q, $qi) {
		$text = "#a #t #f";
		$textexpanded = "sin(x) cert fals";
		$qi->update($s);
		$qqi = $qi;
		$qq = $q->getImpl();
		if(!$qqi->isCacheReady()) {
			throw new HException("Failed test!. Variable cache should be ready immediately after Question Instance update.\x0A");
		}
		$res = $qi->expandVariablesText($text);
		if(!($res === $textexpanded)) {
			throw new HException("Failed test!. Got:\x0A" . $res);
		}
		if(!$qqi->getBooleanVariableValue("#t")) {
			throw new HException("Failed test!. #t was true");
		}
		if($qqi->getBooleanVariableValue("f")) {
			throw new HException("Failed test!. f was false");
		}
	}
	public function testLang() {
		$algorithm = "<session lang=\"ca\" version=\"2.0\"><library closed=\"false\"><mi style=\"color:#ffc800\">variables</mi><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>sin</mi><mo>(</mo><mi>x</mi><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>t</mi><mo>=</mo><mo>(</mo><mn>1</mn><mo>==</mo><mn>1</mn><mo>?</mo><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>f</mi><mo>=</mo><mo>(</mo><mn>1</mn><mo>==</mo><mn>0</mn><mo>?</mo><mo>)</mo></math></input></command></group></library></session>";
		$text = "#a #t #f";
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$rb = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $rb->newQuestion();
			$q->setAlgorithm($algorithm);
			$qi = $rb->newQuestionInstance($q);
			$r = $rb->newVariablesRequest($text, $q, $qi);
			$s = $rb->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$q->setAlgorithm($algorithm);
			$qi = $quizzes->newQuestionInstance($q);
			$r = $quizzes->newVariablesRequest($text, $qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("lang1", $this, $q, $qi));
	}
	public function responseMultianswer2($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!($qi->getAnswerGrade(0, 0, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getAnswerGrade(1, 1, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getAnswerGrade(2, 1, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getAnswerGrade(3, 1, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $q->getSlots();
			$authorAnswers = _hx_array_get($slots, 0)->getAuthorAnswers();
			if(!($qi->getGrade($slots[0], $authorAnswers[0]) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			$authorAnswers = _hx_array_get($slots, 1)->getAuthorAnswers();
			if(!($qi->getGrade($slots[1], $authorAnswers[0]) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getGrade($slots[1], $authorAnswers[1]) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getGrade($slots[1], $authorAnswers[2]) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
	}
	public function responseMultianswer($s, $q, $qi) {
		$correct = new _hx_array(array("#a", "#b", "#b1", "#b2"));
		$user = new _hx_array(array("1", "2"));
		$qi->update($s);
		$ss = null;
		$r = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$builder = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$r = $builder->newEvalMultipleAnswersRequest($correct, $user, $q, $qi);
			$ss = $builder->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$ss = $quizzes->getQuizzesService();
			$slots = $q->getSlots();
			$qi->setSlotAnswer($slots[0], $user[0]);
			$qi->setSlotAnswer($slots[1], $user[1]);
			$r = $quizzes->newGradeRequest($qi);
		}
		$this->numCalls++;
		$ss->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("multianswer2", $this, $q, $qi));
	}
	public function testMultiAnswer() {
		$correct = new _hx_array(array("#a", "#b", "#b1", "#b2"));
		$text = "";
		$i = null;
		{
			$_g1 = 0; $_g = $correct->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$text = $text . " " . $correct[$i1];
				unset($i1);
			}
		}
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$builder = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $builder->newQuestion();
			$q->addAssertion("equivalent_symbolic", 0, 0, null);
			$q->addAssertion("equivalent_symbolic", 1, 1, null);
			$q->addAssertion("equivalent_symbolic", 2, 1, null);
			$q->addAssertion("equivalent_symbolic", 3, 1, null);
			$q->setAlgorithm("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b1</mi><mo>=</mo><mn>2</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b2</mi><mo>=</mo><mn>3</mn></math></input></command></group></library></session>");
			$qi = $builder->newQuestionInstance($q);
			$r = $builder->newVariablesRequest($text, $q, null);
			$s = $builder->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$slot1 = $q->addNewSlot();
			$slot1->addNewAuthorAnswer("#a");
			$slot2 = $q->addNewSlot();
			$slot2->addNewAuthorAnswer("#b");
			$slot2->addNewAuthorAnswer("#b1");
			$slot2->addNewAuthorAnswer("#b2");
			$q->setAlgorithm("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mn>1</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b1</mi><mo>=</mo><mn>2</mn></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b2</mi><mo>=</mo><mn>3</mn></math></input></command></group></library></session>");
			$qi = $quizzes->newQuestionInstance($q);
			$r = $quizzes->newVariablesRequest($text, $qi);
			$s = $quizzes->getQuizzesService();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("multianswer", $this, $q, $qi));
	}
	public function responseCompound5($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$this->checkEqualFloats($qi->getAnswerGrade(0, 0, $q), 0.0);
			$this->checkEqualFloats($qi->getAnswerGrade(1, 0, $q), 1.0);
		} else {
			$slots = $q->getSlots();
			$slot = $slots[0];
			$authorAnswers = $slot->getAuthorAnswers();
			$this->checkEqualFloats($qi->getGrade($slot, $authorAnswers[0]), 0.0);
			$this->checkEqualFloats($qi->getGrade($slot, $authorAnswers[1]), 1.0);
		}
	}
	public function responseCompound4($s, $q, $qi) {
		$qi->update($s);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$this->checkEqualFloats($qi->getAnswerGrade(0, 0, $q), 1.0);
			$this->checkEqualFloats($qi->getAnswerGrade(0, 1, $q), 2.0 / 3.0);
			$this->checkEqualFloats($qi->getAnswerGrade(0, 2, $q), 5.0 / 6.0);
			if(!($qi->getCompoundAnswerGrade(0, 0, 0, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getCompoundAnswerGrade(0, 1, 0, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getCompoundAnswerGrade(0, 2, 2, $q) === 0.5)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $q->getSlots();
			$this->checkEqualFloats($qi->getGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0)), 1.0);
			$this->checkEqualFloats($qi->getGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0)), 2.0 / 3.0);
			$this->checkEqualFloats($qi->getGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0)), 5.0 / 6.0);
			if(!($qi->getCompoundGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0), 0) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 0) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qi->getCompoundGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0), 2) === 0.5)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
	}
	public function checkEqualFloats($a, $b) {
		$d = com_wiris_quizzes_test_Tester_2($this, $a, $b);
		if($d > 0.00000000001) {
			throw new HException(new com_wiris_system_Exception("Failed test: expected " . _hx_string_rec($b, "") . " but got " . _hx_string_rec($a, "") . ".", null));
		}
	}
	public function responseCompound3($s, $q, $qi) {
		$qi->update($s);
		$qqi = $qi;
		$qq = $q->getImpl();
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!($qqi->getAnswerGrade(0, 0, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getAnswerGrade(1, 0, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundAnswerGrade(0, 0, 0, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundAnswerGrade(0, 0, 2, $q) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundAnswerGrade(1, 0, 0, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundAnswerGrade(1, 0, 2, $q) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $qq->getSlots();
			if(!($qqi->getGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0)) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0)) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0), 0) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0), 2) === 1.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 0) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!($qqi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 2) === 0.0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
	}
	public function responseCompound2($s, $q, $qi) {
		$qi->update($s);
		$qqi = $qi;
		if(!$qqi->isAnswerMatching(0, 0)) {
			throw new HException(new com_wiris_system_Exception("Failed test!", null));
		}
	}
	public function responseCompound1($s, $q, $qi) {
		$qi->update($s);
		$qqi = $qi;
		$qq = $q->getImpl();
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(!$qqi->isAnswerMatching(0, 0)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->isAnswerMatching(0, 1)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->isAnswerMatching(0, 2)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!$qqi->isAnswerMatching(0, 3)) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 1, 0, $qq) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 1, 1, $qq) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 1, 2, $qq) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 2, 0, $qq) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 2, 1, $qq) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundAnswerGrade(0, 2, 2, $qq) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $qq->getSlots();
			if(!$qqi->areAllChecksCorrect($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0))) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->areAllChecksCorrect($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0))) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->areAllChecksCorrect($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0))) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(!$qqi->areAllChecksCorrect($slots[3], _hx_array_get(_hx_array_get($slots, 3)->getAuthorAnswers(), 0))) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 0) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 1) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0), 2) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0), 0) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0), 1) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getCompoundGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0), 2) !== 0.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
		$qq->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE);
		$qq->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION, "20% 30% 50%");
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if($qqi->getAnswerGrade(0, 0, $qq) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getAnswerGrade(0, 1, $qq) !== 0.8) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getAnswerGrade(0, 2, $qq) !== 0.2) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getAnswerGrade(0, 3, $qq) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $qq->getSlots();
			if($qqi->getGrade($slots[0], _hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0)) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0)) !== 0.8) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0)) !== 0.2) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if($qqi->getGrade($slots[3], _hx_array_get(_hx_array_get($slots, 3)->getAuthorAnswers(), 0)) !== 1.0) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
		$qq->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE);
		$qq->removeLocalData(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER_GRADE_DISTRIBUTION);
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			if(Math::round($qqi->getAnswerGrade(0, 1, $qq) * 100) / 100.0 !== 0.67) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(Math::round($qqi->getAnswerGrade(0, 2, $qq) * 100) / 100.0 !== 0.33) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		} else {
			$slots = $qq->getSlots();
			if(Math::round($qqi->getGrade($slots[1], _hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0)) * 100) / 100.0 !== 0.67) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
			if(Math::round($qqi->getGrade($slots[2], _hx_array_get(_hx_array_get($slots, 2)->getAuthorAnswers(), 0)) * 100) / 100.0 !== 0.33) {
				throw new HException(new com_wiris_system_Exception("Failed test!", null));
			}
		}
	}
	public function testCompound() {
		$correctAnswer = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><msqrt><mn>2</mn></msqrt><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mi>x</mi><mspace linebreak=\"newline\"/><mi>z</mi><mo>=</mo><mn>0</mn></math>";
		$userCorrectAnswer = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><msqrt><mn>1</mn><mo>+</mo><mn>1</mn></msqrt><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mi>x</mi><mspace linebreak=\"newline\"/><mi>z</mi><mo>=</mo><mn>0</mn></math>";
		$userIncorectAnswer = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><mn>0</mn><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mi>x</mi><mspace linebreak=\"newline\"/><mi>z</mi><mo>=</mo><mn>0</mn></math>";
		$userIncorrectAnswer2 = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><msqrt><mn>2</mn></msqrt></math>";
		$u = new com_wiris_quizzes_impl_UserData();
		$u->setUserCompoundAnswer(3, 0, "<math><msqrt><mn>2</mn></msqrt></math>");
		$u->setUserCompoundAnswer(3, 1, "x");
		$u->setUserCompoundAnswer(3, 2, "0");
		$userCorrectAnswer2 = _hx_array_get($u->answers, 3)->content;
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		$qq = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$builder = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$q = $builder->newQuestion();
			$qi = $builder->newQuestionInstance($q);
			$qq = $q->getImpl();
			$qq->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE);
			$r = $builder->newEvalMultipleAnswersRequest(new _hx_array(array($correctAnswer)), new _hx_array(array($userCorrectAnswer, $userIncorectAnswer, $userIncorrectAnswer2, $userCorrectAnswer2)), $q, $qi);
			$s = $builder->getQuizzesService();
		} else {
			$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $quizzes->newQuestion();
			$slot = $q->addNewSlot();
			$slot->addNewAuthorAnswer($correctAnswer);
			$slot2 = $q->addNewSlot();
			$slot2->addNewAuthorAnswer($correctAnswer);
			$slot3 = $q->addNewSlot();
			$slot3->addNewAuthorAnswer($correctAnswer);
			$slot4 = $q->addNewSlot();
			$slot4->addNewAuthorAnswer($correctAnswer);
			$q->setProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER, com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE);
			$qi = $quizzes->newQuestionInstance($q);
			$qi->setSlotAnswer($slot, $userCorrectAnswer);
			$qi->setSlotAnswer($slot2, $userIncorectAnswer);
			$qi->setSlotAnswer($slot3, $userIncorrectAnswer2);
			$qi->setSlotAnswer($slot4, $userCorrectAnswer2);
			$r = $quizzes->newGradeRequest($qi);
			$s = $quizzes->getQuizzesService();
			$qq = $q->getImpl();
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("compound1", $this, $q, $qi));
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$builder = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$correctAnswer = "<math><mtable columnalign=\"left\" rowspacing=\"0\"><mtr><mtd><mi>x</mi><mo>=</mo><msqrt><mn>2</mn></msqrt></mtd></mtr><mtr><mtd><mi>y</mi><mo>=</mo><mi>x</mi></mtd></mtr><mtr><mtd><mi>z</mi><mo>=</mo><mn>0</mn></mtd></mtr></mtable></math>";
			$r = $builder->newEvalRequest($correctAnswer, $userCorrectAnswer, $q, $qi);
			$this->numCalls++;
			$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("compound2", $this, $q, $qi));
		}
		$qq->wirisCasSession = "<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mi style=\"color:#ffc800\">variables</mi><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>test</mi><mo>(</mo><mi>a</mi><mo>,</mo><mi>b</mi><mo>,</mo><mi>c</mi><mo>)</mo><mo>:</mo><mo>=</mo><mfenced><mrow><mi>a</mi><mo>==</mo><msqrt><mn>2</mn></msqrt><mo>&and;</mo><mi>b</mi><mo>==</mo><mi>x</mi><mo>&and;</mo><mi>c</mi><mo>==</mo><mn>0</mn></mrow></mfenced><mo>&nbsp;</mo><mo>?</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>test2</mi><mo>(</mo><mi>a</mi><mo>,</mo><mi>b</mi><mo>,</mo><mi>c</mi><mo>)</mo><mo>:</mo><mo>=</mo><mo>(</mo><mi>a</mi><mo>==</mo><mn>0</mn><mo>&and;</mo><mi>b</mi><mo>==</mo><mn>0</mn><mo>&and;</mo><mi>c</mi><mo>==</mo><mn>0</mn><mo>)</mo><mo>&nbsp;</mo><mo>?</mo></math></input></command></group></library></session>";
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$builder = com_wiris_quizzes_impl_QuizzesBuilderImpl::getInstance();
			$qq->setParametrizedAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, "0", "0", new _hx_array(array("test")));
			$qq->setParametrizedAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, "1", "0", new _hx_array(array("test2")));
			$r = $builder->newEvalMultipleAnswersRequest(new _hx_array(array($correctAnswer, $correctAnswer)), new _hx_array(array($userCorrectAnswer)), $q, $qi);
		} else {
			$slots = $qq->getSlots();
			$qq->removeSlot($slots[3]);
			$qq->removeSlot($slots[2]);
			_hx_array_get(_hx_array_get($slots, 0)->getAuthorAnswers(), 0)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION)->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME, "test");
			_hx_array_get(_hx_array_get($slots, 1)->getAuthorAnswers(), 0)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION)->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME, "test2");
			$qi->setSlotAnswer($slots[0], $userCorrectAnswer);
			$qi->setSlotAnswer($slots[1], $userCorrectAnswer);
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newGradeRequest($qi);
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("compound3", $this, $q, $qi));
		$q2 = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestion()->getImpl();
		$i2 = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q2);
		$q2->setAlgorithm("<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mi style=\"color:#ffc800\">variables</mi><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>test</mi><mo>(</mo><mi>a</mi><mo>,</mo><mi>b</mi><mo>,</mo><mi>c</mi><mo>)</mo><mo>:</mo><mo>=</mo><mo>[</mo><mi>a</mi><mo>==</mo><msqrt><mn>2</mn></msqrt><mo>&nbsp;</mo><mo>?</mo><mo>,</mo><mi>b</mi><mo>==</mo><mi>x</mi><mo>&nbsp;</mo><mo>?</mo><mo>,</mo><mi>if</mi><mo>&nbsp;</mo><mi>c</mi><mo>==</mo><mn>0</mn><mo>&nbsp;</mo><mi>then</mi><mo>&nbsp;</mo><mn>1</mn><mo>&nbsp;</mo><mi>else_if</mi><mo>&nbsp;</mo><mi>c</mi><mo>&gt;</mo><mn>0</mn><mo>&nbsp;</mo><mi>then</mi><mo>&nbsp;</mo><mn>0</mn><mo>.</mo><mn>5</mn><mo>&nbsp;</mo><mi>else</mi><mo>&nbsp;</mo><mn>0</mn><mo>&nbsp;</mo><mi>end</mi><mo>]</mo></math></input></command></group></library><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></session>");
		$q2->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_TRUE);
		$q2->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE, com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_VALUE_COMPOUND_ANSWER_GRADE_DISTRIBUTE);
		$q2->setLocalData(com_wiris_quizzes_api_QuizzesConstants::$PROPERTY_COMPOUND_ANSWER_GRADE_DISTRIBUTION, "33% 33% 33%");
		$userIncorrectAnswer3 = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>x</mi><mo>=</mo><msqrt><mn>2</mn></msqrt><mspace linebreak=\"newline\"/><mi>y</mi><mo>=</mo><mi>x</mi><mspace linebreak=\"newline\"/><mi>z</mi><mo>=</mo><mn>10</mn></math>";
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$q2->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, 0, 0, new _hx_array(array("test")));
			$q2->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, 0, 1, new _hx_array(array("test")));
			$q2->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION, 0, 2, new _hx_array(array("test")));
			$q2->setCorrectAnswer(0, $correctAnswer);
			$i2->setStudentAnswer(0, $userCorrectAnswer);
			$i2->setStudentAnswer(1, $userIncorectAnswer);
			$i2->setStudentAnswer(2, $userIncorrectAnswer3);
			$r = com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newFeedbackRequest("#answer1 #answer2 #answer3 #answer4", $q2, $i2);
		} else {
			$q2slot = $q2->addNewSlot();
			$q2slot->addNewAuthorAnswer($correctAnswer)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION)->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME, "test");
			$q2slot2 = $q2->addNewSlot();
			$q2slot2->addNewAuthorAnswer($correctAnswer)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION)->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME, "test");
			$q2slot3 = $q2->addNewSlot();
			$q2slot3->addNewAuthorAnswer($correctAnswer)->setComparison(com_wiris_quizzes_api_assertion_ComparisonName::$GRADING_FUNCTION)->setParameter(com_wiris_quizzes_api_assertion_ComparisonParameterName::$FUNCTION_NAME, "test");
			$i2->setSlotAnswer($q2slot, $userCorrectAnswer);
			$i2->setSlotAnswer($q2slot2, $userIncorectAnswer);
			$i2->setSlotAnswer($q2slot3, $userIncorrectAnswer3);
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newFeedbackRequest("#answer1 #answer2 #answer3 #answer4", $i2);
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("compound4", $this, $q2, $i2));
		$q3 = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestion();
		$i3 = com_wiris_quizzes_api_Quizzes::getInstance()->newQuestionInstance($q3);
		$correct = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mi mathvariant=\"normal\">m</mi><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn><mi mathvariant=\"normal\">m</mi></math>";
		$notSoCorrect = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mn>1</mn><mspace linebreak=\"newline\"/><mi>b</mi><mo>=</mo><mn>2</mn></math>";
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES4) {
			$q3slot = $q3->addNewSlot();
			$q3slot->setProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER, "true");
			$q3slot->getSyntax()->setParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$UNITS, "m");
			$q3aa1 = $q3slot->addNewAuthorAnswer($correct);
			$q3aa2 = $q3slot->addNewAuthorAnswer($notSoCorrect);
			$i3->setSlotAnswer($q3slot, $notSoCorrect);
			$r = com_wiris_quizzes_api_Quizzes::getInstance()->newGradeRequest($i3);
		} else {
			$q3->setProperty(com_wiris_quizzes_api_PropertyName::$COMPOUND_ANSWER, "true");
			$q3->setCorrectAnswer(0, $correct);
			$q3->setCorrectAnswer(1, $notSoCorrect);
			$q3->addAssertion(com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH, 0, 0, new _hx_array(array(null, null, null, null, null, null, null, null, null, null, null, null, "m", null, null)));
			$q3->addAssertion(com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH, 1, 0, new _hx_array(array(null, null, null, null, null, null, null, null, null, null, null, null, "m", null, null)));
			$q3->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, 0, 0, null);
			$q3->addAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, 1, 0, null);
			$i3->setStudentAnswer(0, $notSoCorrect);
			$r = com_wiris_quizzes_api_QuizzesBuilder::getInstance()->newEvalRequest(null, null, $q3, $i3);
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("compound5", $this, $q3, $i3));
	}
	public function responseCompoundVariableLabels($s, $q, $qi) {
		$qi->update($s);
		$studentInstance = $qi->getStudentQuestionInstance();
		$mathmlVariables = $studentInstance->getMathMLVariables();
		if(!$mathmlVariables->exists("a") || !$mathmlVariables->exists("b") || $mathmlVariables->exists("r") || $mathmlVariables->exists("s")) {
			throw new HException(new com_wiris_system_Exception("Failed test!", null));
		}
	}
	public function testExpandCompoundAnswerLabel() {
		if($this->apiVersion < com_wiris_quizzes_test_Tester::$QUIZZES4) {
			return;
		}
		$questionDef = "<question><wirisCasSession><![CDATA[<wiriscalc version=\"3.2\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>Untitled&#xa0;calc</mtext></math></title><properties><property name=\"decimal_separator\">.</property><property name=\"digit_group_separator\"></property><property name=\"float_format\">mg</property><property name=\"imaginary_unit\">i</property><property name=\"implicit_times_operator\">false</property><property name=\"item_separator\">,</property><property name=\"lang\">en</property><property name=\"precision\">5</property><property name=\"save_settings_in_cookies\">false</property><property name=\"times_operator\">Â·</property><property name=\"use_degrees\">false</property></properties><session version=\"3.0\" lang=\"en\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>Sheet&#xa0;1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">a</mi><mo>=</mo><mn>1</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">b</mi><mo>=</mo><mn>2</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">r</mi><mo>=</mo><mn>1</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>1</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi mathvariant=\"normal\">s</mi><mo>=</mo><mn>2</mn></math></input><output><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mn>2</mn></math></output></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session><constructions><construction group=\"1\">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;handwriting&quot;:[]}</construction></constructions></wiriscalc>]]></wirisCasSession><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>a</mi><mo>=</mo><mo>#</mo><mi>s</mi><mspace linebreak=\"newline\"/><mo>#</mo><mi>b</mi><mo>=</mo><mo>#</mo><mi>r</mi></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_math\"/><assertion name=\"equivalent_symbolic\"><param name=\"tolerance\">0.001</param></assertion></assertions><slots><slot><localData><data name=\"inputCompound\">true</data><data name=\"inputField\">popupEditor</data></localData><initialContent type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>]]></initialContent></slot></slots></question>";
		$quizzes = com_wiris_quizzes_api_Quizzes::getInstance();
		$question = $quizzes->readQuestion($questionDef);
		$instance = $quizzes->newQuestionInstance($question);
		$slots = $question->getSlots();
		$answers = _hx_array_get($slots, 0)->getAuthorAnswers();
		$request = $quizzes->newVariablesRequest(_hx_array_get($answers, 0)->getValue(), $instance);
		$this->numCalls++;
		$quizzes->getQuizzesService()->executeAsync($request, new com_wiris_quizzes_test_TestIdServiceListener("compoundVariableLabels", $this, $question, $instance));
	}
	public function responseHandwritingConstraints($s, $q, $qi) {
		$qi->update($s);
		$json = com_wiris_util_json_JSon::decode($qi->getProperty(com_wiris_quizzes_api_PropertyName::$HANDWRITING_CONSTRAINTS));
		$symbols = $json->get("symbols");
		if($this->inArray("s", $symbols) || !$this->inArray("z", $symbols) || !$this->inArray("2", $symbols) || $this->inArray("X", $symbols) || $this->inArray("Y", $symbols) || !$this->inArray("cos", $symbols)) {
			throw new HException(new com_wiris_system_Exception("Failed test!", null));
		}
	}
	public function testHandwritingConstraints() {
		$question = "<question><wirisCasSession><![CDATA[<session lang=\"en\" version=\"2.0\"><library closed=\"false\"><mtext style=\"color:#ffc800\" xml:lang=\"en\">variables</mtext><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>a</mi><mo>=</mo><mi>sin</mi><mo>(</mo><mi>x</mi><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>b</mi><mo>=</mo><mi>cos</mi><mo>(</mo><mi>y</mi><mo>)</mo></math></input></command><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mi>c</mi><mo>=</mo><mn>2</mn><mo>*</mo><mi>x</mi><mo>+</mo><mn>3</mn><mo>*</mo><mi>y</mi><mo>+</mo><mfrac><mn>1</mn><mn>2</mn></mfrac><mo>*</mo><mi>z</mi></math></input></command></group></library><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></session>]]></wirisCasSession><correctAnswers><correctAnswer type=\"mathml\"><![CDATA[<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mo>#</mo><mi>a</mi><mo>-</mo><mn>5</mn></math>]]></correctAnswer></correctAnswers><assertions><assertion name=\"syntax_expression\"/><assertion name=\"equivalent_symbolic\"/></assertions><options><option name=\"tolerance\">10^(-3)</option><option name=\"relative_tolerance\">true</option><option name=\"precision\">4</option><option name=\"times_operator\">Â·</option><option name=\"implicit_times_operator\">false</option><option name=\"imaginary_unit\">i</option></options><localData><data name=\"inputField\">inlineHand</data><data name=\"gradeCompound\">and</data><data name=\"gradeCompoundDistribution\"></data><data name=\"casSession\"/></localData></question>";
		$q = null;
		$qi = null;
		$r = null;
		$s = null;
		if($this->apiVersion === com_wiris_quizzes_test_Tester::$QUIZZES3) {
			$qb = com_wiris_quizzes_api_QuizzesBuilder::getInstance();
			$q = $qb->readQuestion($question);
			$qi = $qb->newQuestionInstance($q);
			$r = $qb->newVariablesRequest("#a #b #c", $q, $qi);
			$s = $qb->getQuizzesService();
		} else {
			$qq = com_wiris_quizzes_api_Quizzes::getInstance();
			$q = $qq->readQuestion($question);
			$qi = $qq->newQuestionInstance($q);
			$r = $qq->newVariablesRequest("#a #b #c", $qi);
			$s = $qq->getQuizzesService();
		}
		$json = com_wiris_util_json_JSon::decode($qi->getProperty(com_wiris_quizzes_api_PropertyName::$HANDWRITING_CONSTRAINTS));
		$symbols = $json->get("symbols");
		if($this->inArray("s", $symbols) || !$this->inArray("a", $symbols) || $this->inArray("cos", $symbols)) {
			throw new HException(new com_wiris_system_Exception("Failed test!", null));
		}
		$this->numCalls++;
		$s->executeAsync($r, new com_wiris_quizzes_test_TestIdServiceListener("handwritingConstraints", $this, $q, $qi));
	}
	public function run() {
		$e = new com_wiris_system_Exception("Dummy", null);
		$t = new com_wiris_quizzes_test_TestIdServiceListener(null, null, null, null);
		haxe_Log::trace("Starting generic integration test...", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 69, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "run")));
		$this->numCalls++;
		$this->start = Date::now();
		$h = new com_wiris_quizzes_impl_HTMLToolsUnitTests();
		$h->run();
		haxe_Log::trace("HTML unit test OK!", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 76, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "run")));
		haxe_Log::trace("Service url: " . com_wiris_quizzes_api_Quizzes::getInstance()->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL), _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 77, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "run")));
		{
			$_g = 0; $_g1 = com_wiris_quizzes_test_Tester::$API_VERSIONS;
			while($_g < $_g1->length) {
				$v = $_g1[$_g];
				++$_g;
				$this->apiVersion = $v;
				haxe_Log::trace("Starting tests for Wiris Quizzes API version: " . _hx_string_rec($this->apiVersion, ""), _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 82, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "run")));
				$this->testOpenQuestion();
				$this->testCompatibility();
				$this->testParameters();
				$this->testUserId();
				$this->testHandwritingConstraints();
				$this->testMultiAnswer();
				$this->testBugs();
				$this->testUnicode();
				$this->testCompound();
				$this->testOpenQuestionHand();
				$this->testAnyAnswer();
				if(!com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
					$this->testCache();
					$this->testFilter();
				}
				$this->testEncodings();
				$this->testTranslation();
				$this->testTolerance();
				$this->testRandomQuestion();
				$this->testFloatFormat();
				if(!com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT) {
					$this->testImages();
				}
				$this->testFloatEval();
				$this->testAssertion();
				$this->testDeprecated();
				$this->testLang();
				$this->testExpandCompoundAnswerLabel();
				$this->testConvertEditor2Newlines();
				$this->testEvaluateRandomVariables();
				unset($v);
			}
		}
		$this->endCall();
	}
	public function endCall() {
		$this->numCalls--;
		if($this->numCalls <= 0) {
			$end = Date::now();
			haxe_Log::trace("Successful test!. Time: " . _hx_string_rec(($end->getTime() - $this->start->getTime()), "") . "ms.", _hx_anonymous(array("fileName" => "Tester.hx", "lineNumber" => 58, "className" => "com.wiris.quizzes.test.Tester", "methodName" => "endCall")));
		}
	}
	public $apiVersion;
	public $numCalls;
	public $start;
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
	static $QUIZZES3 = 3;
	static $QUIZZES4 = 4;
	static $API_VERSIONS;
	static function main() {
		$argv = Sys::args();
		try {
			$t = new com_wiris_quizzes_test_Tester();
			$t->run();
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$x = $_ex_;
			{
				throw new HException($x);
			}
		}
	}
	function __toString() { return 'com.wiris.quizzes.test.Tester'; }
}
com_wiris_quizzes_test_Tester::$API_VERSIONS = new _hx_array(array(com_wiris_quizzes_test_Tester::$QUIZZES3, com_wiris_quizzes_test_Tester::$QUIZZES4));
function com_wiris_quizzes_test_Tester_0(&$»this, &$q, &$qi, &$s) {
	{
		$s1 = new haxe_Utf8(null);
		$s1->addChar(8226);
		return $s1->toString();
	}
}
function com_wiris_quizzes_test_Tester_1(&$»this, &$q, &$qi, &$s, &$texts) {
	{
		$s1 = new haxe_Utf8(null);
		$s1->addChar(8226);
		return $s1->toString();
	}
}
function com_wiris_quizzes_test_Tester_2(&$»this, &$a, &$b) {
	if($a < $b) {
		return $b - $a;
	} else {
		return $a - $b;
	}
}

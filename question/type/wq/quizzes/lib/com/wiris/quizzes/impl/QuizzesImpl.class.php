<?php

class com_wiris_quizzes_impl_QuizzesImpl extends com_wiris_quizzes_api_Quizzes {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function mathContentToFilterableValue($value, $initialContent) {
		if($value->type === com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE) {
			return "<img " . "src=\"" . com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getResourceUrl("plotter_loading.png") . "\" " . "alt=\"Plotter\" " . "class=\"wirisconstruction wirisgraphanimate\" " . "data-wirisconstruction=\"" . com_wiris_util_xml_WXmlUtils::htmlEscape($value->content) . "\"" . (com_wiris_quizzes_impl_QuizzesImpl_0($this, $initialContent, $value)) . "/>";
		}
		return $value->content;
	}
	public function answerToFilterableValue($value, $initialContent) {
		$mc = new com_wiris_quizzes_impl_MathContent();
		$mc->set($value);
		return $this->mathContentToFilterableValue($mc, $initialContent);
	}
	public function getElementsToGrade($geometryFile, $assertion) {
		if($assertion->getParam(com_wiris_quizzes_impl_Assertion::$PARAM_ELEMENTS_TO_GRADE) !== null) {
			$ao = com_wiris_util_json_JSon::getArray(com_wiris_util_json_JSon::decode($assertion->getParam(com_wiris_quizzes_impl_Assertion::$PARAM_ELEMENTS_TO_GRADE)));
			$strings = new _hx_array(array());
			{
				$_g = 0;
				while($_g < $ao->length) {
					$o = $ao[$_g];
					++$_g;
					$strings->push(com_wiris_util_json_JSon::getString($o));
					unset($o);
				}
			}
			return $strings;
		} else {
			$g = com_wiris_util_geometry_GeometryFile::readJSON($geometryFile);
			return (($g->getDisplaysLength() >= 1) ? $g->getDisplay(0)->getElementNames() : new _hx_array(array()));
		}
	}
	public function getElementsToGradeFromAuthorAnswer($authorAnswer) {
		return $this->getElementsToGrade($authorAnswer->getValue(), $authorAnswer->getComparison());
	}
	public function getHttpObject($httpl, $serviceUrl, $proxyRoute, $postData, $contentType) {
		$http = null;
		$config = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getConfiguration();
		$clientSide = com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT || com_wiris_settings_PlatformSettings::$IS_FLASH;
		$allowCors = $clientSide && "true" === $config->get(com_wiris_quizzes_api_ConfigurationKeys::$CROSSORIGINCALLS_ENABLED);
		if($clientSide && !$allowCors) {
			$url = $config->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL);
			$http = new com_wiris_quizzes_impl_HttpImpl($url, $httpl);
			$http->setParameter("service", $proxyRoute->service);
			if($proxyRoute->path !== null) {
				$http->setParameter("path", $proxyRoute->path);
			}
			if($postData !== null) {
				$http->setParameter("rawpostdata", "true");
				if($contentType !== null) {
					$http->setParameter("contenttype", $contentType);
				}
				$http->setParameter("postdata", $postData);
				$http->setHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			} else {
				$http->setParameter("url", $serviceUrl);
			}
		} else {
			if($clientSide) {
				$http = new com_wiris_quizzes_impl_HttpImpl($serviceUrl, $httpl);
			} else {
				$http = new com_wiris_quizzes_impl_MaxConnectionsHttpImpl($serviceUrl, $httpl);
				$referrer = $config->get(com_wiris_quizzes_api_ConfigurationKeys::$REFERER_URL);
				if($referrer === null || trim($referrer) === "") {
					com_wiris_system_Logger::log(900, "'quizzes.referer.url' configuration item is not set so requests to the " . "service can not be identified. Unidentified requests may be blocked by the server " . "and will certainly be blocked in future releases." . "\x0A" . "Setup the referrer editing your configuration.ini file or setting it programmatically " . "through the Configuration interface." . "\x0A");
				} else {
					$http->setHeader("Referer", $referrer);
				}
			}
			if($postData !== null) {
				$http->setHeader("Content-Type", $contentType . "; charset=UTF-8");
				$http->setPostData($postData);
			} else {
				$http->setHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			}
		}
		return $http;
	}
	public function newMultipleAnswersGradeRequest($correctAnswers, $studentAnswers) {
		return $this->newEvalMultipleAnswersRequest($correctAnswers, $studentAnswers, null, null);
	}
	public function newSimpleGradeRequest($correctAnswer, $studentAnswer) {
		return $this->newEvalMultipleAnswersRequest(new _hx_array(array($correctAnswer)), new _hx_array(array($studentAnswer)), null, null);
	}
	public function newMultipleResponseFromXml($xml) {
		$s = $this->getSerializer();
		$elem = $s->read($xml);
		$mqr = null;
		$tag = $s->getTagName($elem);
		if($tag === com_wiris_quizzes_impl_QuestionResponseImpl::$tagName) {
			$res = $elem;
			$mqr = new com_wiris_quizzes_impl_MultipleQuestionResponse();
			$mqr->questionResponses = new _hx_array(array());
			$mqr->questionResponses->push($res);
		} else {
			if($tag === com_wiris_quizzes_impl_MultipleQuestionResponse::$tagName) {
				$mqr = $elem;
			} else {
				throw new HException("Unexpected XML root tag " . $tag . ".");
			}
		}
		return $mqr;
	}
	public function newResponseFromXml($xml) {
		$mqr = $this->newMultipleResponseFromXml($xml);
		return $mqr->questionResponses[0];
	}
	public function newRequestFromXml($xml) {
		$s = $this->getSerializer();
		$elem = $s->read($xml);
		$req = null;
		$tag = $s->getTagName($elem);
		if($tag === com_wiris_quizzes_impl_QuestionRequestImpl::$tagName) {
			$req = $elem;
		} else {
			if($tag === com_wiris_quizzes_impl_MultipleQuestionRequest::$tagName) {
				$mqr = $elem;
				$req = $mqr->questionRequests[0];
			} else {
				throw new HException("Unexpected XML root tag " . $tag . ".");
			}
		}
		return $req;
	}
	public function newTranslationRequest($q, $lang) {
		$r = new com_wiris_quizzes_impl_QuestionRequestImpl();
		$r->question = $q;
		$p = new com_wiris_quizzes_impl_ProcessGetTranslation();
		$p->lang = $lang;
		$r->addProcess($p);
		return $r;
	}
	public function replicateCompoundAnswerAssertionsImpl($aa, $aux) {
		$assertions = new _hx_array(array());
		{
			$_g = 0;
			while($_g < $aa->length) {
				$a = $aa[$_g];
				++$_g;
				$n = $aux->get($a->getCorrectAnswer());
				$k = null;
				{
					$_g1 = 0;
					while($_g1 < $n) {
						$k1 = $_g1++;
						$ca = new com_wiris_quizzes_impl_Assertion();
						$ca->name = $a->name;
						$ca->parameters = $a->parameters;
						$assertions->push($ca);
						if($a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_FUNCTION) {
							$caa = new _hx_array(array());
							$uaa = new _hx_array(array());
							$l = null;
							{
								$_g2 = 0;
								while($_g2 < $n) {
									$l1 = $_g2++;
									$caa[$l1] = $a->getCorrectAnswer() . "_c" . _hx_string_rec($l1, "");
									$uaa[$l1] = $a->getAnswer() . "_c" . _hx_string_rec($l1, "");
									unset($l1);
								}
								unset($_g2);
							}
							$ca->setCorrectAnswers($caa);
							$ca->setAnswers($uaa);
							break;
							unset($uaa,$l,$caa);
						} else {
							$ca->setCorrectAnswer($a->getCorrectAnswer() . "_c" . _hx_string_rec($k1, ""));
							$ca->setAnswer($a->getAnswer() . "_c" . _hx_string_rec($k1, ""));
						}
						unset($k1,$ca);
					}
					unset($_g1);
				}
				unset($n,$k,$a);
			}
		}
		return $assertions;
	}
	public function spliceEqualAssertion($a, $assertions) {
		if($assertions === null) {
			return null;
		}
		{
			$_g = 0;
			while($_g < $assertions->length) {
				$ass = $assertions[$_g];
				++$_g;
				if($ass->isEquivalent($a)) {
					$assertions->remove($ass);
					return $ass;
				}
				unset($ass);
			}
		}
		return null;
	}
	public function replicateCompoundAnswerAssertions($qa, $qq, $aux) {
		if($qq->assertions === null) {
			return;
		}
		if($qa->getImpl()->isQuestionCompoundAnswer()) {
			$qq->assertions = $this->replicateCompoundAnswerAssertionsImpl($qq->assertions, $aux);
			return;
		}
		$slots = $qa->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$s = $slots[$_g];
				++$_g;
				if($s->isSlotCompoundAnswer()) {
					$assertions = new _hx_array(array());
					$a = $s->getSyntax();
					$a = $this->spliceEqualAssertion($a, $qq->assertions);
					if($a !== null) {
						$assertions->push($a);
					}
					$authorAnswers = $s->getAuthorAnswers();
					{
						$_g1 = 0;
						while($_g1 < $authorAnswers->length) {
							$aa = $authorAnswers[$_g1];
							++$_g1;
							$a = $aa->getComparison();
							$a = $this->spliceEqualAssertion($a, $qq->assertions);
							if($a !== null) {
								$assertions->push($a);
							}
							$validations = $aa->getValidations();
							{
								$_g2 = 0;
								while($_g2 < $validations->length) {
									$v = $validations[$_g2];
									++$_g2;
									$a = $v;
									$a = $this->spliceEqualAssertion($a, $qq->assertions);
									if($a !== null) {
										$assertions->push($a);
									}
									unset($v);
								}
								unset($_g2);
							}
							unset($validations,$aa);
						}
						unset($_g1);
					}
					$qq->assertions = $qq->assertions->concat($this->replicateCompoundAnswerAssertionsImpl($assertions, $aux));
					unset($authorAnswers,$assertions,$a);
				}
				unset($s);
			}
		}
	}
	public function breakCompoundUserAnswersImpl($answers) {
		$userAns = new _hx_array(array());
		if($answers === null) {
			return $userAns;
		}
		{
			$_g = 0;
			while($_g < $answers->length) {
				$a = $answers[$_g];
				++$_g;
				$parts = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($a);
				$k = null;
				{
					$_g2 = 0; $_g1 = $parts->length;
					while($_g2 < $_g1) {
						$k1 = $_g2++;
						$ca = new com_wiris_quizzes_impl_Answer();
						$ca->id = $a->id . "_c" . _hx_string_rec($k1, "");
						$ca->set($parts[$k1][1]);
						$userAns->push($ca);
						unset($k1,$ca);
					}
					unset($_g2,$_g1);
				}
				unset($parts,$k,$a);
			}
		}
		return $userAns;
	}
	public function breakCompoundUserAnswers($qa, $qq, $uu) {
		if($uu->answers === null) {
			return;
		}
		if($qa->getImpl()->isQuestionCompoundAnswer()) {
			$uu->answers = $this->breakCompoundUserAnswersImpl($uu->answers);
			return;
		}
		$slots = $qa->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$s = $slots[$_g];
				++$_g;
				if($s->isSlotCompoundAnswer()) {
					$compound = new _hx_array(array());
					$i = $uu->answers->length - 1;
					while($i >= 0) {
						$a = $uu->answers[$i--];
						if($s->id === $a->id) {
							$compound->push($a);
							$uu->answers->remove($a);
						}
						unset($a);
					}
					$uu->answers = $uu->answers->concat($this->breakCompoundUserAnswersImpl($compound));
					unset($i,$compound);
				}
				unset($s);
			}
		}
	}
	public function breakCompoundCorrectAnswersImpl($qq, $aux) {
		$correctAnswers = new _hx_array(array());
		if($qq === null) {
			return $correctAnswers;
		}
		{
			$_g = 0;
			while($_g < $qq->length) {
				$c = $qq[$_g];
				++$_g;
				if($c === null) {
					continue;
				}
				$parts = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($c);
				if($aux !== null) {
					$aux->set($c->id, $parts->length);
				}
				$k = null;
				{
					$_g2 = 0; $_g1 = $parts->length;
					while($_g2 < $_g1) {
						$k1 = $_g2++;
						$cc = new com_wiris_quizzes_impl_CorrectAnswer();
						$cc->type = $c->type;
						$cc->id = $c->id . "_c" . _hx_string_rec($k1, "");
						$cc->content = $parts[$k1][1];
						$cc->weight = 1.0 / $parts->length;
						$correctAnswers->push($cc);
						unset($k1,$cc);
					}
					unset($_g2,$_g1);
				}
				unset($parts,$k,$c);
			}
		}
		return $correctAnswers;
	}
	public function breakCompoundCorrectAnswers($qa, $qq, $aux, $instance) {
		if($qq->correctAnswers === null) {
			return;
		}
		if($qa->getImpl()->isQuestionCompoundAnswer()) {
			$qq->correctAnswers = $this->breakCompoundCorrectAnswersImpl($qq->correctAnswers, $aux);
			return;
		}
		$slots = $qa->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$s = $slots[$_g];
				++$_g;
				if($s->isSlotCompoundAnswer()) {
					$authorAnswers = $s->getAuthorAnswers();
					$correctAnswers = new _hx_array(array());
					{
						$_g1 = 0;
						while($_g1 < $authorAnswers->length) {
							$aa = $authorAnswers[$_g1];
							++$_g1;
							$correctAnswers->push($aa->value);
							unset($aa);
						}
						unset($_g1);
					}
					$separated = $this->breakCompoundCorrectAnswersImpl($correctAnswers, $aux);
					{
						$_g1 = 0;
						while($_g1 < $correctAnswers->length) {
							$ca = $correctAnswers[$_g1];
							++$_g1;
							$qq->correctAnswers->remove($ca);
							unset($ca);
						}
						unset($_g1);
					}
					if($instance !== null && $instance->hasVariables()) {
						$_g1 = 0;
						while($_g1 < $separated->length) {
							$sepCA = $separated[$_g1];
							++$_g1;
							$isPlainTextField = $s->getAnswerFieldType() == com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD;
							$isStringSyntax = $s->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$STRING;
							$value = $sepCA->content;
							if($isPlainTextField || $isStringSyntax) {
								$value = $instance->expandVariablesText($value);
							} else {
								$value = $instance->expandVariablesMathMLEval($value);
							}
							$sepCA->set($value);
							unset($value,$sepCA,$isStringSyntax,$isPlainTextField);
						}
						unset($_g1);
					}
					$qq->correctAnswers = $qq->correctAnswers->concat($separated);
					unset($separated,$correctAnswers,$authorAnswers);
				}
				unset($s);
			}
		}
	}
	public function isCompoundGraphicalAssertion($a) {
		return $a->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_GRAPHIC || $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_COLOR || $a->name === com_wiris_quizzes_impl_Assertion::$CHECK_LINESTYLE;
	}
	public function breakCompoundGraphical($qq) {
		$toAdd = new _hx_array(array());
		$toRemove = new _hx_array(array());
		{
			$_g1 = 0; $_g = $qq->getAssertionsLength();
			while($_g1 < $_g) {
				$i = $_g1++;
				if($this->isCompoundGraphicalAssertion($qq->getAssertion($i))) {
					$ass = $qq->getAssertion($i);
					$ca = null;
					{
						$_g3 = 0; $_g2 = $qq->correctAnswers->length;
						while($_g3 < $_g2) {
							$j = $_g3++;
							if(com_wiris_util_type_Arrays::containsArray($ass->getCorrectAnswers(), _hx_array_get($qq->correctAnswers, $j)->id)) {
								$ca = $qq->correctAnswers[$j];
							}
							unset($j);
						}
						unset($_g3,$_g2);
					}
					if($ca === null) {
						return;
					}
					$elements = $this->getElementsToGrade($ca->content, $ass);
					$steppedElements = new _hx_array(array());
					{
						$_g3 = 0; $_g2 = $elements->length;
						while($_g3 < $_g2) {
							$j = $_g3++;
							$elemId = $elements[$j];
							$steppedElements->push($elemId);
							$ca_j = new com_wiris_quizzes_impl_CorrectAnswer();
							$ca_j->id = $ca->id . "_cg" . _hx_string_rec($j, "") . "_" . $elemId;
							$ca_j->reference = $ca->id;
							$ass_j = new com_wiris_quizzes_impl_Assertion();
							$ass_j->name = $ass->name;
							$ass_j->addCorrectAnswer($ca_j->id);
							$ass_j->answer = $ass->answer;
							$ass_j->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_ELEMENTS_TO_GRADE, com_wiris_util_json_JSon::encode($steppedElements));
							if($ass->getParam(com_wiris_quizzes_impl_Assertion::$PARAM_TOLERANCE) !== null) {
								$ass_j->setParam(com_wiris_quizzes_impl_Assertion::$PARAM_TOLERANCE, $ass->getParam(com_wiris_quizzes_impl_Assertion::$PARAM_TOLERANCE));
							}
							$qq->correctAnswers->push($ca_j);
							$toAdd->push($ass_j);
							unset($j,$elemId,$ca_j,$ass_j);
						}
						unset($_g3,$_g2);
					}
					$toRemove->push($ass);
					unset($steppedElements,$elements,$ca,$ass);
				}
				unset($i);
			}
		}
		{
			$_g = 0;
			while($_g < $toRemove->length) {
				$a = $toRemove[$_g];
				++$_g;
				$qq->assertions->remove($a);
				unset($a);
			}
		}
		{
			$_g = 0;
			while($_g < $toAdd->length) {
				$a = $toAdd[$_g];
				++$_g;
				$qq->assertions->push($a);
				unset($a);
			}
		}
	}
	public function isCompoundGraphical($qq) {
		{
			$_g1 = 0; $_g = $qq->getAssertionsLength();
			while($_g1 < $_g) {
				$i = $_g1++;
				if($this->isCompoundGraphicalAssertion($qq->getAssertion($i))) {
					return true;
				}
				unset($i);
			}
		}
		return false;
	}
	public function newEvalMultipleAnswersRequest($correctAnswers, $userAnswers, $question, $instance) {
		$q = null;
		$qi = null;
		if($question !== null) {
			$q = $question->getImpl();
		}
		if($instance !== null) {
			$qi = $instance;
		}
		$qq = new com_wiris_quizzes_impl_QuestionImpl();
		$uu = new com_wiris_quizzes_impl_UserData();
		$uu->answers = new _hx_array(array());
		if($q !== null) {
			$qq->wirisCasSession = $q->wirisCasSession;
			$qq->options = $q->options;
		}
		if($qi !== null && $qi->userData !== null) {
			$uu->randomSeed = $qi->userData->randomSeed;
			$uu->parameters = $qi->userData->parameters;
		} else {
			$qqi = new com_wiris_quizzes_impl_QuestionInstanceImpl();
			$uu->randomSeed = $qqi->userData->randomSeed;
		}
		$i = 0;
		if($correctAnswers !== null) {
			$_g1 = 0; $_g = $correctAnswers->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$value = com_wiris_util_xml_MathMLUtils::removeStrokesAnnotation($correctAnswers[$i1]);
				if($value === null) {
					$value = "";
				}
				$qq->setCorrectAnswer($i1, $value);
				unset($value,$i1);
			}
		}
		if($userAnswers !== null) {
			$_g1 = 0; $_g = $userAnswers->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$uu->setUserAnswer($i1, $this->removeHandAnnotations($userAnswers[$i1]));
				unset($i1);
			}
		}
		$qq->assertions = new _hx_array(array());
		$qa = $q;
		$ua = null;
		if($qi !== null) {
			$ua = $qi->userData;
		}
		$j = null;
		if($correctAnswers === null && $qa !== null) {
			$qq->correctAnswers = new _hx_array(array());
			$cas = $qa->correctAnswers;
			{
				$_g1 = 0; $_g = $cas->length;
				while($_g1 < $_g) {
					$j1 = $_g1++;
					$ca = $cas[$j1];
					if($ca !== null && $ca->content !== null) {
						$newCA = new com_wiris_quizzes_impl_CorrectAnswer();
						$newCA->type = $ca->type;
						$newCA->content = com_wiris_util_xml_MathMLUtils::removeStrokesAnnotation($ca->content);
						$newCA->weight = $ca->weight;
						$newCA->id = $ca->id;
						$qq->correctAnswers->push($newCA);
						unset($newCA);
					}
					unset($j1,$ca);
				}
			}
		}
		if($userAnswers === null && $ua !== null && $ua->answers !== null) {
			$_g1 = 0; $_g = $ua->answers->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$aa = _hx_array_get($ua->answers, $j1)->content;
				if($aa !== null) {
					$aa = $this->removeHandAnnotations($aa);
					$uu->setUserAnswer($j1, $aa);
				}
				unset($j1,$aa);
			}
		}
		$syntax = null;
		if($qa !== null && $qa->assertions !== null) {
			$_g1 = 0; $_g = $qa->assertions->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$ass = _hx_array_get($qa->assertions, $j1)->copy();
				if($ass->isSyntactic()) {
					$syntax = $ass;
				}
				if($correctAnswers !== null) {
					$assCA = $ass->getCorrectAnswers();
					$caCounter = $assCA->length - 1;
					while($caCounter >= 0) {
						$ca = $assCA[$caCounter];
						if(Std::parseInt($ca) >= $correctAnswers->length) {
							$ass->removeCorrectAnswer($ca);
						}
						$caCounter--;
						unset($ca);
					}
					if($ass->getCorrectAnswers()->length === 0) {
						continue;
					}
					unset($caCounter,$assCA);
				}
				$qq->assertions->push($ass);
				unset($j1,$ass);
			}
		}
		if($syntax === null) {
			$syntax = new com_wiris_quizzes_impl_Assertion();
			$syntax->addCorrectAnswer("0");
			$syntax->name = com_wiris_quizzes_impl_Assertion::$SYNTAX_MATH;
			$qq->assertions->push($syntax);
		}
		{
			$_g1 = 0; $_g = $uu->answers->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$foundSyntax = false;
				$k = null;
				{
					$_g3 = 0; $_g2 = $qq->assertions->length;
					while($_g3 < $_g2) {
						$k1 = $_g3++;
						$ass = $qq->assertions[$k1];
						if($ass->isSyntactic() && com_wiris_util_type_Arrays::containsArray($ass->getAnswers(), $j1)) {
							$foundSyntax = true;
						}
						unset($k1,$ass);
					}
					unset($_g3,$_g2);
				}
				if(!$foundSyntax) {
					$syntax->addAnswer(_hx_string_rec($j1, "") . "");
				}
				unset($k,$j1,$foundSyntax);
			}
		}
		if($qi !== null && $qi->hasVariables()) {
			$_g1 = 0; $_g = $qq->getCorrectAnswersLength();
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$ca = $qq->correctAnswers[$j1];
				$value = $ca->content;
				$isPlainTextField = $qa->getAnswerFieldType() == com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD;
				$isStringSyntax = $syntax->name === com_wiris_quizzes_impl_Assertion::$SYNTAX_STRING;
				$slots = $qa->slots;
				if($slots !== null) {
					$_g2 = 0;
					while($_g2 < $slots->length) {
						$slot = $slots[$_g2];
						++$_g2;
						$authorAnswers = $slot->authorAnswers;
						{
							$_g3 = 0;
							while($_g3 < $authorAnswers->length) {
								$authorAnswer = $authorAnswers[$_g3];
								++$_g3;
								if($authorAnswer->id === $ca->id) {
									$isPlainTextField = $slot->getAnswerFieldType() == com_wiris_quizzes_api_ui_AnswerFieldType::$TEXT_FIELD;
									$isStringSyntax = $slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$STRING;
								}
								unset($authorAnswer);
							}
							unset($_g3);
						}
						unset($slot,$authorAnswers);
					}
					unset($_g2);
				}
				$isTextFormat = $ca->type === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
				$hasMultiletterIdentifierInTextFormat = false;
				if($isTextFormat) {
					$splitByRegularSp = _hx_explode(" ", $ca->content);
					$words = new _hx_array(array());
					{
						$_g2 = 0;
						while($_g2 < $splitByRegularSp->length) {
							$word = $splitByRegularSp[$_g2];
							++$_g2;
							$splitByNbsp = _hx_explode(com_wiris_quizzes_impl_QuizzesImpl_1($this, $_g, $_g1, $_g2, $ca, $correctAnswers, $hasMultiletterIdentifierInTextFormat, $i, $instance, $isPlainTextField, $isStringSyntax, $isTextFormat, $j, $j1, $q, $qa, $qi, $qq, $question, $slots, $splitByRegularSp, $syntax, $ua, $userAnswers, $uu, $value, $word, $words), $word);
							$words = $words->concat($splitByNbsp);
							unset($word,$splitByNbsp);
						}
						unset($_g2);
					}
					{
						$_g2 = 0;
						while($_g2 < $words->length) {
							$word = $words[$_g2];
							++$_g2;
							if(!StringTools::startsWith($word, "#") && strlen($word) > 1) {
								$hasMultiletterIdentifierInTextFormat = true;
								break;
							}
							unset($word);
						}
						unset($_g2);
					}
					unset($words,$splitByRegularSp);
				}
				if($isPlainTextField || $isStringSyntax || $hasMultiletterIdentifierInTextFormat) {
					$value = $qi->expandVariablesText($value);
				} else {
					$value = $qi->expandVariablesMathMLEval($value);
				}
				$qq->setCorrectAnswer($j1, $value);
				unset($value,$slots,$j1,$isTextFormat,$isStringSyntax,$isPlainTextField,$hasMultiletterIdentifierInTextFormat,$ca);
			}
		}
		$j = $qq->assertions->length - 1;
		while($j >= 0) {
			if(_hx_array_get($qq->assertions, $j)->name === com_wiris_quizzes_impl_Assertion::$EQUIVALENT_ALL) {
				$correctanswer = _hx_array_get($qq->assertions, $j)->getCorrectAnswer();
				$k = $qq->assertions->length - 1;
				while($k >= 0) {
					if(_hx_array_get($qq->assertions, $k)->isSyntactic()) {
						_hx_array_get($qq->assertions, $k)->removeCorrectAnswer($correctanswer);
						if(_hx_array_get($qq->assertions, $k)->getCorrectAnswers()->length === 0) {
							$qq->assertions->remove($qq->assertions[$k]);
							if($k < $j) {
								$j--;
							}
						}
					}
					$k--;
				}
				unset($k,$correctanswer);
			}
			$j--;
		}
		$usedcorrectanswers = new _hx_array(array());
		{
			$_g1 = 0; $_g = $qq->getCorrectAnswersLength();
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$usedcorrectanswers[$j1] = false;
				unset($j1);
			}
		}
		$usedanswers = new _hx_array(array());
		{
			$_g1 = 0; $_g = $uu->answers->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$usedanswers[$j1] = false;
				unset($j1);
			}
		}
		{
			$_g1 = 0; $_g = $qq->assertions->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$ass = $qq->assertions[$j1];
				$corr = com_wiris_quizzes_impl_QuizzesImpl::getIndex($ass->getCorrectAnswer());
				$ans = com_wiris_quizzes_impl_QuizzesImpl::getIndex($ass->getAnswer());
				if($ass->isEquivalence()) {
					if($corr < $usedcorrectanswers->length) {
						$usedcorrectanswers[$corr] = true;
					}
					if($ans < $usedanswers->length) {
						$usedanswers[$ans] = true;
					}
				} else {
					if($ass->isCheck()) {
						if($ans < $usedanswers->length) {
							$usedanswers[$ans] = true;
						}
					}
				}
				unset($j1,$corr,$ass,$ans);
			}
		}
		$pairs = $this->getPairings($qq->getCorrectAnswersLength(), $uu->answers->length);
		{
			$_g1 = 0; $_g = $usedcorrectanswers->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				if(!$usedcorrectanswers[$j1]) {
					$k = null;
					{
						$_g3 = 0; $_g2 = $pairs->length;
						while($_g3 < $_g2) {
							$k1 = $_g3++;
							if($pairs[$k1][0] === $j1) {
								$user = $pairs[$k1][1];
								$qq->setParametrizedAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, _hx_string_rec($j1, "") . "", _hx_string_rec($user, "") . "", null);
								$usedanswers[$user] = true;
								unset($user);
							}
							unset($k1);
						}
						unset($_g3,$_g2);
					}
					unset($k);
				}
				unset($j1);
			}
		}
		{
			$_g1 = 0; $_g = $usedanswers->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				if(!$usedanswers[$j1]) {
					$k = null;
					{
						$_g3 = 0; $_g2 = $pairs->length;
						while($_g3 < $_g2) {
							$k1 = $_g3++;
							if($pairs[$k1][1] === $j1) {
								$qq->setParametrizedAssertion(com_wiris_quizzes_impl_Assertion::$EQUIVALENT_SYMBOLIC, _hx_string_rec($pairs[$k1][0], "") . "", _hx_string_rec($j1, "") . "", null);
							}
							unset($k1);
						}
						unset($_g3,$_g2);
					}
					unset($k);
				}
				unset($j1);
			}
		}
		if($qa !== null && $qa->getImpl()->isCompoundAnswer()) {
			if($this->isCompoundGraphical($qq)) {
				$this->breakCompoundGraphical($qq);
			} else {
				$aux = new Hash();
				$this->breakCompoundCorrectAnswers($qa, $qq, $aux, $qi);
				$this->breakCompoundUserAnswers($qa, $qq, $uu);
				$this->replicateCompoundAnswerAssertions($qa, $qq, $aux);
			}
		}
		$qr = new com_wiris_quizzes_impl_QuestionRequestImpl();
		$qr->question = $qq;
		$qr->userData = $uu;
		$qr->checkAssertions();
		return $qr;
	}
	public function removeHandAnnotations($mathml) {
		$conf = $this->getConfiguration();
		if(!($conf->get(com_wiris_quizzes_api_ConfigurationKeys::$HAND_LOGTRACES) === "true") || _hx_index_of($conf->get(com_wiris_quizzes_api_ConfigurationKeys::$SERVICE_URL), "www.wiris.net", null) === -1) {
			return com_wiris_util_xml_MathMLUtils::removeStrokesAnnotation($mathml);
		}
		return $mathml;
	}
	public function getPairings($c, $u) {
		$p = new _hx_array(array());
		$reverse = null;
		if($c >= $u) {
			$reverse = false;
		} else {
			$aux = $c;
			$c = $u;
			$u = $aux;
			$reverse = true;
		}
		if($u === 0) {
			return $p;
		}
		$n = intval($c / $u);
		$d = intval(_hx_mod($c, $u));
		$i = null;
		$cc = 0;
		$cu = 0;
		{
			$_g = 0;
			while($_g < $u) {
				$i1 = $_g++;
				$j = null;
				{
					$_g1 = 0;
					while($_g1 < $n) {
						$j1 = $_g1++;
						$p->push(new _hx_array(array((($reverse) ? $cu : $cc), (($reverse) ? $cc : $cu))));
						$cc++;
						unset($j1);
					}
					unset($_g1);
				}
				if($i1 < $d) {
					$p->push(new _hx_array(array((($reverse) ? $cu : $cc), (($reverse) ? $cc : $cu))));
					$cc++;
				}
				$cu++;
				unset($j,$i1);
			}
		}
		return $p;
	}
	public function getSerializer() {
		$s = new com_wiris_util_xml_XmlSerializer();
		$s->register(new com_wiris_quizzes_impl_Answer());
		$s->register(new com_wiris_quizzes_impl_Assertion());
		$s->register(new com_wiris_quizzes_impl_AssertionCheckImpl());
		$s->register(new com_wiris_quizzes_impl_AssertionParam());
		$s->register(new com_wiris_quizzes_impl_CorrectAnswer());
		$s->register(new com_wiris_quizzes_impl_InitialContent());
		$s->register(new com_wiris_quizzes_impl_LocalData());
		$s->register(new com_wiris_quizzes_impl_MathContent());
		$s->register(new com_wiris_quizzes_impl_MultipleQuestionRequest());
		$s->register(new com_wiris_quizzes_impl_MultipleQuestionResponse());
		$s->register(new com_wiris_quizzes_impl_Option());
		$s->register(new com_wiris_quizzes_impl_ProcessGetCheckAssertions());
		$s->register(new com_wiris_quizzes_impl_ProcessGetFeaturedAssertions());
		$s->register(new com_wiris_quizzes_impl_ProcessGetFeaturedSyntaxAssertions());
		$s->register(new com_wiris_quizzes_impl_ProcessGetTranslation());
		$s->register(new com_wiris_quizzes_impl_ProcessGetVariables());
		$s->register(new com_wiris_quizzes_impl_ProcessStoreQuestion());
		$s->register(new com_wiris_quizzes_impl_QuestionImpl());
		$s->register(new com_wiris_quizzes_impl_QuestionRequestImpl());
		$s->register(new com_wiris_quizzes_impl_QuestionResponseImpl());
		$s->register(new com_wiris_quizzes_impl_QuestionInstanceImpl());
		$s->register(new com_wiris_quizzes_impl_ResultError());
		$s->register(new com_wiris_quizzes_impl_ResultErrorLocation());
		$s->register(new com_wiris_quizzes_impl_ResultGetCheckAssertions());
		$s->register(new com_wiris_quizzes_impl_ResultGetTranslation());
		$s->register(new com_wiris_quizzes_impl_ResultGetVariables());
		$s->register(new com_wiris_quizzes_impl_ResultStoreQuestion());
		$s->register(new com_wiris_quizzes_impl_ResultGetFeaturedSyntaxAssertions());
		$s->register(new com_wiris_quizzes_impl_ResultGetFeaturedAssertions());
		$s->register(new com_wiris_quizzes_impl_SlotImpl());
		$s->register(new com_wiris_quizzes_impl_TranslationNameChange());
		$s->register(new com_wiris_quizzes_impl_UserData());
		$s->register(new com_wiris_quizzes_impl_Variable());
		$s->register(new com_wiris_quizzes_impl_Parameter());
		return $s;
	}
	public function cloneAndProcessCompoundAnswers($question) {
		$q = $question;
		if(!$q->getImpl()->isCompoundAnswer()) {
			return $q;
		}
		$qq = new com_wiris_quizzes_impl_QuestionImpl();
		$ca = $q->getImpl()->correctAnswers;
		$qq->importQuestion($q->getImpl());
		$aux = new Hash();
		if($ca !== null) {
			$qq->correctAnswers = $ca->concat(new _hx_array(array()));
			$this->breakCompoundCorrectAnswers($qq, $qq, $aux, null);
		}
		$aa = $q->getImpl()->assertions;
		if($aa !== null) {
			$qq->assertions = $aa->concat(new _hx_array(array()));
			$this->replicateCompoundAnswerAssertions($qq, $qq, $aux);
		}
		return $qq;
	}
	public function newFeaturedAssertionsRequest($question) {
		if($question === null) {
			throw new HException("Question cannot be null.");
		}
		$qr = new com_wiris_quizzes_impl_QuestionRequestImpl();
		$qr->question = $this->cloneAndProcessCompoundAnswers($question);
		$qr->addProcess(new com_wiris_quizzes_impl_ProcessGetFeaturedAssertions());
		return $qr;
	}
	public function newFeaturedSyntaxAssertionsRequest($question) {
		if($question === null) {
			throw new HException("Question cannot be null.");
		}
		$qr = new com_wiris_quizzes_impl_QuestionRequestImpl();
		$qr->question = $question;
		$qr->addProcess(new com_wiris_quizzes_impl_ProcessGetFeaturedSyntaxAssertions());
		return $qr;
	}
	public function getLockProvider() {
		if($this->locker === null) {
			$className = $this->getConfiguration()->get(com_wiris_quizzes_impl_ConfigurationImpl::$LOCKPROVIDER_CLASS);
			if(!($className === "")) {
				$this->locker = Type::createInstance(Type::resolveClass($className), new _hx_array(array()));
			} else {
				$this->locker = new com_wiris_quizzes_impl_FileLockProvider($this->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$CACHE_DIR));
			}
		}
		return $this->locker;
	}
	public function getVariablesCache() {
		if($this->variablesCache === null) {
			$this->variablesCache = $this->createCache(com_wiris_quizzes_impl_ConfigurationImpl::$VARIABLESCACHE_CLASS);
		}
		return $this->variablesCache;
	}
	public function getImagesCache() {
		if($this->imagesCache === null) {
			$this->imagesCache = $this->createCache(com_wiris_quizzes_impl_ConfigurationImpl::$IMAGESCACHE_CLASS);
		}
		return $this->imagesCache;
	}
	public function newStoreCache() {
		return new com_wiris_util_sys_StoreCache($this->getConfiguration()->get(com_wiris_quizzes_api_ConfigurationKeys::$CACHE_DIR));
	}
	public function createCache($configKey) {
		$cache = null;
		$className = $this->getConfiguration()->get($configKey);
		if(!($className === "")) {
			$cache = Type::createInstance(Type::resolveClass($className), new _hx_array(array()));
		} else {
			$cache = $this->newStoreCache();
		}
		return $cache;
	}
	public function getAccessProvider() {
		if($this->accessProvider === null) {
			$classpath = $this->getConfiguration()->get(com_wiris_quizzes_impl_ConfigurationImpl::$ACCESSPROVIDER_CLASSPATH);
			if(!($classpath === "")) {
				com_wiris_quizzes_impl_ClasspathLoader::load($classpath);
			}
			$className = $this->getConfiguration()->get(com_wiris_quizzes_impl_ConfigurationImpl::$ACCESSPROVIDER_CLASS);
			if(!($className === "")) {
				$this->accessProvider = Type::createInstance(Type::resolveClass($className), new _hx_array(array()));
			}
		}
		return $this->accessProvider;
	}
	public function getTracker() {
		return $this->tracker;
	}
	public function getTelemetryService() {
		return $this->telemetryService;
	}
	public function getResourceUrl($name) {
		$c = $this->getConfiguration();
		$version = $c->get(com_wiris_quizzes_api_ConfigurationKeys::$VERSION);
		if("true" === $c->get(com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_STATIC)) {
			return $c->get(com_wiris_quizzes_api_ConfigurationKeys::$RESOURCES_URL) . "/" . $name . "?v=" . $version;
		} else {
			return $c->get(com_wiris_quizzes_api_ConfigurationKeys::$PROXY_URL) . "?service=resource&name=" . $name . "&v=" . $version;
		}
	}
	public function getConfiguration() {
		return com_wiris_quizzes_impl_ConfigurationImpl::getInstance();
	}
	public function getMathFilter() {
		return new com_wiris_quizzes_impl_MathMLFilter();
	}
	public function getQuizzesService() {
		return new com_wiris_quizzes_impl_QuizzesServiceImpl();
	}
	public function newFeedbackRequest($html, $instance) {
		if($instance === null) {
			throw new HException("The question instance cannot be null!");
		}
		$r = $this->newGradeRequest($instance);
		$qr = $r;
		$qi = $instance;
		$qr->question = $this->cloneQuestion($qr->question);
		com_wiris_quizzes_impl_QuizzesImpl::setVariables($html, $qr->question, $qi, $qr);
		return $r;
	}
	public function newGradeRequest($instance) {
		if($instance === null) {
			throw new HException("The question instance cannot be null!");
		}
		return $this->newEvalMultipleAnswersRequest(null, null, $instance->question, $instance);
	}
	public function newVariablesRequestWithQuestionData($html, $instance) {
		if($instance === null) {
			throw new HException("The question instance cannot be null!");
		}
		$qi = $instance;
		$question = $qi->question;
		$sb = new StringBuf();
		if($question !== null) {
			$slots = $question->getSlots();
			{
				$_g = 0;
				while($_g < $slots->length) {
					$slot = $slots[$_g];
					++$_g;
					if($slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$GRAPHIC) {
						continue;
					}
					if($slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
						$sb->add($slot->getSyntax()->getParameter(com_wiris_quizzes_api_assertion_SyntaxParameterName::$TASK_TO_SOLVE) . " ");
					}
					if($slot->getInitialContent() !== null) {
						$sb->add($slot->getInitialContent() . " ");
					}
					$authorAnswers = $slot->getAuthorAnswers();
					{
						$_g1 = 0;
						while($_g1 < $authorAnswers->length) {
							$authorAnswer = $authorAnswers[$_g1];
							++$_g1;
							if($authorAnswer->getValue() !== null) {
								$sb->add($authorAnswer->getValue() . " ");
							}
							unset($authorAnswer);
						}
						unset($_g1);
					}
					unset($slot,$authorAnswers);
				}
			}
		}
		if($html !== null) {
			$sb->add($html);
		}
		return $this->newVariablesRequest($sb->b, $instance);
	}
	public function sanitizeForQuizzesService($question) {
		$slots = $question->getSlots();
		{
			$_g = 0;
			while($_g < $slots->length) {
				$slot = $slots[$_g];
				++$_g;
				if($slot->getSyntax()->getName() == com_wiris_quizzes_api_assertion_SyntaxName::$MATH_MULTISTEP) {
					$slot->setSyntax(com_wiris_quizzes_api_assertion_SyntaxName::$MATH);
				}
				unset($slot);
			}
		}
	}
	public function cloneQuestion($question) {
		$serialized = $question->serialize();
		return $this->readQuestion($serialized);
	}
	public function newVariablesRequest($html, $instance) {
		if($instance === null) {
			throw new HException("The question instance cannot be null!");
		}
		$qi = $instance;
		$question = $qi->question;
		if($question === null) {
			throw new HException("The question must be specified, either as a parameter" . " of this function or as a field of the question instance");
		}
		$question = $this->cloneQuestion($question);
		$qr = new com_wiris_quizzes_impl_QuestionRequestImpl();
		$qr->question = $question;
		$qr->userData = $qi->userData;
		com_wiris_quizzes_impl_QuizzesImpl::setVariables($html, $question, $qi, $qr);
		$this->sanitizeForQuizzesService($question);
		return $qr;
	}
	public function readQuestionInstance($xml, $q) {
		$s = $this->getSerializer();
		$elem = $s->read($xml);
		$tag = $s->getTagName($elem);
		if(!($tag === "questionInstance")) {
			throw new HException("Unexpected root tag " . $tag . ". Expected questionInstance.");
		}
		if($q !== null) {
			$elem->question = $q;
		}
		return $elem;
	}
	public function readQuestion($xml) {
		return new com_wiris_quizzes_impl_QuestionLazy($xml);
	}
	public function newQuestionInstance($q) {
		$qi = new com_wiris_quizzes_impl_QuestionInstanceImpl();
		$qi->question = $q;
		return $qi;
	}
	public function newQuestion() {
		return new com_wiris_quizzes_impl_QuestionImpl();
	}
	public function getQuizzesComponentBuilder() {
		if($this->componentBuilder === null) {
			$this->componentBuilder = new com_wiris_quizzes_impl_QuizzesComponentBuilderImpl();
		}
		return $this->componentBuilder;
	}
	public $tracker;
	public $telemetryService;
	public $accessProvider;
	public $locker;
	public $imagesCache;
	public $variablesCache;
	public $componentBuilder = null;
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
	static $singleton = null;
	static function getInstance() {
		if(com_wiris_quizzes_impl_QuizzesImpl::$singleton === null) {
			com_wiris_quizzes_impl_QuizzesImpl::$singleton = new com_wiris_quizzes_impl_QuizzesImpl();
		}
		return com_wiris_quizzes_impl_QuizzesImpl::$singleton;
	}
	static function getIndex($id) {
		$i = _hx_index_of($id, "_", null) + 1;
		return Std::parseInt(_hx_substr($id, $i, null));
	}
	static function extractQuestionInstanceVariableNames($qi) {
		$vars = new _hx_array(array());
		$i = $qi->variables->keys();
		while($i->hasNext()) {
			$h = $qi->variables->get($i->next());
			$j = $h->keys();
			while($j->hasNext()) {
				com_wiris_quizzes_impl_HTMLTools::insertStringInSortedArray($j->next(), $vars);
			}
			unset($j,$h);
		}
		return com_wiris_quizzes_impl_HTMLTools::toNativeArray($vars);
	}
	static function removeAnswerVariables($variables, $q, $qi) {
		$qq = $q->getImpl();
		if($qq->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER) === "true") {
			$name = $qq->getOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME);
			$defname = $qq->defaultOption(com_wiris_quizzes_api_QuizzesConstants::$OPTION_STUDENT_ANSWER_PARAMETER_NAME);
			if($defname === $name) {
				$lang = com_wiris_quizzes_impl_CalcDocumentTools::casSessionLang($qq->getAlgorithm());
				$name = com_wiris_quizzes_impl_QuizzesTranslator::getInstance($lang)->t($name);
			}
			$n = 0;
			$i = null;
			{
				$_g1 = 0; $_g = $variables->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(StringTools::startsWith($variables[$i1], $name)) {
						$after = _hx_substr($variables[$i1], strlen($name), null);
						if(strlen($after) === 0 || com_wiris_util_type_IntegerTools::isInt($after) && Std::parseInt($after) <= $qi->getStudentAnswersLength()) {
							$variables[$i1] = null;
							$n++;
						} else {
							if($qq->getLocalData(com_wiris_quizzes_impl_LocalData::$KEY_OPENANSWER_COMPOUND_ANSWER) === com_wiris_quizzes_impl_LocalData::$VALUE_OPENANSWER_COMPOUND_ANSWER_TRUE && $q->getCorrectAnswersLength() > 0) {
								$correctAnswer = $qq->correctAnswers[0];
								$parts = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($correctAnswer);
								if(com_wiris_util_type_IntegerTools::isInt($after) && Std::parseInt($after) <= $parts->length) {
									$variables[$i1] = null;
									$n++;
								}
								unset($parts,$correctAnswer);
							} else {
								if(com_wiris_util_type_IntegerTools::isInt($after) && com_wiris_quizzes_impl_QuizzesImpl::containsCorrectAnswerWithCompoundName($qq, Std::parseInt($after))) {
									$variables[$i1] = null;
									$n++;
								}
							}
						}
						unset($after);
					}
					unset($i1);
				}
			}
			if($n > 0) {
				$newvariables = new _hx_array(array());
				$j = 0;
				{
					$_g1 = 0; $_g = $variables->length;
					while($_g1 < $_g) {
						$i1 = $_g1++;
						if($variables[$i1] !== null) {
							$newvariables[$j] = $variables[$i1];
							$j++;
						}
						unset($i1);
					}
				}
				$variables = $newvariables;
			}
		}
		return $variables;
	}
	static function containsCorrectAnswerWithCompoundName($q, $after) {
		$it = $q->correctAnswers->iterator();
		while($it->hasNext()) {
			$id = $it->next()->id;
			if($id !== null && StringTools::endsWith($id, "_c" . _hx_string_rec(($after - 1), ""))) {
				return true;
			}
			unset($id);
		}
		return false;
	}
	static function setVariables($html, $q, $qi, $qr) {
		$variables = null;
		if($html === null) {
			$variables = com_wiris_quizzes_impl_QuizzesImpl::extractQuestionInstanceVariableNames($qi);
		} else {
			$h = new com_wiris_quizzes_impl_HTMLTools();
			$computedVariables = new Hash();
			$html = $h->extractActionExpressions($html, $computedVariables);
			$q->setAlgorithm($h->addComputedVariablesToAlgorithm($q->getAlgorithm(), $computedVariables));
			$variables = $h->extractVariableNames($html);
			$variables = com_wiris_quizzes_impl_QuizzesImpl::removeAnswerVariables($variables, $q, $qi);
		}
		if($variables->length > 0) {
			$qr->variables($variables, com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
			$qr->variables($variables, com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
		}
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesImpl'; }
}
function com_wiris_quizzes_impl_QuizzesImpl_0(&$»this, &$initialContent, &$value) {
	if($initialContent !== null && !($initialContent === "")) {
		return "data-wirisinitialcontent=\"" . com_wiris_util_xml_WXmlUtils::htmlEscape($initialContent) . "\"";
	} else {
		return "";
	}
}
function com_wiris_quizzes_impl_QuizzesImpl_1(&$»this, &$_g, &$_g1, &$_g2, &$ca, &$correctAnswers, &$hasMultiletterIdentifierInTextFormat, &$i, &$instance, &$isPlainTextField, &$isStringSyntax, &$isTextFormat, &$j, &$j1, &$q, &$qa, &$qi, &$qq, &$question, &$slots, &$splitByRegularSp, &$syntax, &$ua, &$userAnswers, &$uu, &$value, &$word, &$words) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}

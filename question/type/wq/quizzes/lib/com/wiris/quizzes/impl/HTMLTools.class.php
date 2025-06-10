<?php

class com_wiris_quizzes_impl_HTMLTools {
	public function __construct() {
		;
	}
	public function isMathMLString($math) {
		$math = trim($math);
		return StringTools::startsWith($math, "<math") && StringTools::endsWith($math, "</math>");
	}
	public function setPlotterLoadingSrc($src) {
		$this->plotterLoadingSrc = $src;
	}
	public function setProxyUrl($proxyUrl) {
		$this->proxyUrl = $proxyUrl;
	}
	public function setAnswerKeyword($keyword) {
		$this->answerKeyword = $keyword;
	}
	public function getAnswerVariables($answers, $compound) {
		$h = new Hash();
		$i = null;
		if(!$compound) {
			{
				$_g1 = 0; $_g = $answers->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$a = $answers[$i1];
					if(!$h->exists($a->type)) {
						$h->set($a->type, new Hash());
					}
					$h->get($a->type)->set($this->answerKeyword . _hx_string_rec(($i1 + 1), ""), $a->content);
					unset($i1,$a);
				}
			}
			if($answers->length === 1) {
				$h->get(_hx_array_get($answers, 0)->type)->set($this->answerKeyword, _hx_array_get($answers, 0)->content);
			}
		} else {
			$answer = $answers[0];
			$a = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($answer);
			{
				$_g1 = 0; $_g = $a->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					$s = $a[$i1][1];
					$type = com_wiris_quizzes_impl_HTMLTools_0($this, $_g, $_g1, $a, $answer, $answers, $compound, $h, $i, $i1, $s);
					if(!$h->exists($type)) {
						$h->set($type, new Hash());
					}
					$h->get($type)->set($this->answerKeyword . _hx_string_rec(($i1 + 1), ""), $s);
					unset($type,$s,$i1);
				}
			}
			if(!$h->exists($answer->type)) {
				$h->set($answer->type, new Hash());
			}
			$h->get($answer->type)->set($this->answerKeyword, $answer->content);
		}
		return $h;
	}
	public function expandAnswersText($text, $answers, $compound) {
		if($answers === null || $answers->length === 0 || $this->answerKeyword === null || _hx_index_of($text, "#" . $this->answerKeyword, null) === -1) {
			return $text;
		}
		$h = $this->getAnswerVariables($answers, $compound);
		$textvariables = $h->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
		return $this->expandVariablesText($text, $textvariables);
	}
	public function expandAnswers($text, $answers, $compound) {
		if($answers === null || $answers->length === 0 || $this->answerKeyword === null || _hx_index_of($text, "#" . $this->answerKeyword, null) === -1) {
			return $text;
		}
		$h = $this->getAnswerVariables($answers, $compound);
		return $this->expandVariables($text, $h);
	}
	public function setItemSeparator($sep) {
		$this->separator = (($sep === null) ? "," : $sep);
	}
	public function isImplicitArgumentFactor($x) {
		if($x->getNodeName() === "mi" || $x->getNodeName() === "mn") {
			return true;
		}
		if($x->getNodeName() === "msup") {
			$c = $x->firstElement();
			if($c !== null && $c->getNodeName() === "mi" || $c->getNodeName() === "mn") {
				return true;
			}
		}
		return false;
	}
	public function fullMathML2TextImpl($e) {
		$sb = new StringBuf();
		if($e->getNodeName() === "mo" || $e->getNodeName() === "mn" || $e->getNodeName() === "mi" || $e->getNodeName() === "mtext") {
			$sb->add(com_wiris_util_xml_WXmlUtils::getNodeValue($e->firstChild()));
		} else {
			if($e->getNodeName() === "mfenced" || $e->getNodeName() === "mtr" || $e->getNodeName() === "mtable") {
				$open = $e->get("open");
				if($open === null) {
					$open = "(";
				}
				$close = $e->get("close");
				if($close === null) {
					$close = ")";
				}
				$separators = $e->get("separators");
				if($separators === null) {
					$separators = ",";
				}
				if($open === "(" && $close === ")" && $e->firstElement() !== null && $e->firstElement()->getNodeName() === "mtable") {
					$open = "";
					$close = "";
				}
				$sb->add($open);
				$it = $e->elements();
				$i = 0;
				$n = haxe_Utf8::length($separators);
				while($it->hasNext()) {
					if($i > 0 && $n > 0) {
						$sb->add(com_wiris_quizzes_impl_HTMLTools_1($this, $close, $e, $i, $it, $n, $open, $sb, $separators));
					}
					$sb->add($this->fullMathML2TextImpl($it->next()));
					$i++;
				}
				$sb->add($close);
			} else {
				if($e->getNodeName() === "mfrac") {
					$it = $e->elements();
					$num = $this->fullMathML2TextImpl($it->next());
					if(strlen($num) > 1) {
						$num = "(" . $num . ")";
					}
					$den = $this->fullMathML2TextImpl($it->next());
					if(strlen($den) > 1) {
						$den = "(" . $den . ")";
					}
					$sb->add($num);
					$sb->add("/");
					$sb->add($den);
				} else {
					if($e->getNodeName() === "msup") {
						$it = $e->elements();
						$bas = $this->fullMathML2TextImpl($it->next());
						if(strlen($bas) > 1) {
							$bas = "(" . $bas . ")";
						}
						$exp = $this->fullMathML2TextImpl($it->next());
						if(strlen($exp) > 1) {
							$exp = "(" . $exp . ")";
						}
						$sb->add($bas);
						$sb->add("^");
						$sb->add($exp);
					} else {
						if($e->getNodeName() === "msqrt") {
							$sb->add("sqrt(");
							$e->setNodeName("math");
							$sb->add($this->fullMathML2TextImpl($e));
							$sb->add(")");
						} else {
							if($e->getNodeName() === "mroot") {
								$it = $e->elements();
								$rad = $this->fullMathML2TextImpl($it->next());
								$ind = $this->fullMathML2TextImpl($it->next());
								$sb->add("root(");
								$sb->add($rad);
								$sb->add(",");
								$sb->add($ind);
								$sb->add(")");
							} else {
								if($e->getNodeName() === "mspace" && "newline" === $e->get("linebreak")) {
									$sb->add("\x0A");
								} else {
									if($e->getNodeName() === "semantics") {
										$it = $e->elements();
										if($it->hasNext()) {
											$mml = $it->next();
											if($it->hasNext()) {
												$ann = $it->next();
												if($ann->getNodeName() === "annotation" && "text/plain" === $ann->get("encoding")) {
													return com_wiris_util_xml_WXmlUtils::getText($ann);
												}
											}
											return $this->fullMathML2TextImpl($mml);
										}
									} else {
										$it = $e->elements();
										while($it->hasNext()) {
											$x = $it->next();
											$sb->add($this->fullMathML2TextImpl($x));
											if($x->getNodeName() === "mi" && $this->isFunctionName(com_wiris_util_xml_WXmlUtils::getNodeValue($x->firstChild())) && $it->hasNext()) {
												$y = $it->next();
												if($y->getNodeName() === "msqrt" || $y->getNodeName() === "mfrac" || $y->getNodeName() === "mroot") {
													$sb->add("(");
													$sb->add($this->fullMathML2TextImpl($y));
													$sb->add(")");
												} else {
													$parentheses = false;
													$argument = new StringBuf();
													while($y !== null && $this->isImplicitArgumentFactor($y)) {
														if($y->getNodeName() === "msup") {
															$parentheses = true;
														}
														$argument->add($this->fullMathML2TextImpl($y));
														$y = (($it->hasNext()) ? $it->next() : null);
													}
													if($parentheses) {
														$sb->add("(");
													}
													$sb->add($argument->b);
													if($parentheses) {
														$sb->add(")");
													}
													if($y !== null) {
														$sb->add($this->fullMathML2TextImpl($y));
													}
													unset($parentheses,$argument);
												}
												unset($y);
											}
											unset($x);
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $sb->b;
	}
	public function mathMLToText($mathml) {
		$root = com_wiris_util_xml_WXmlUtils::parseXML($mathml);
		if($root->nodeType == Xml::$Document) {
			$root = $root->firstElement();
		}
		$this->removeMrows($root);
		return str_replace(com_wiris_quizzes_impl_HTMLTools_2($this, $mathml, $root), " ", $this->fullMathML2TextImpl($root));
	}
	public function isReservedWordPrefix($token, $words) {
		$i = null;
		{
			$_g1 = 0; $_g = $words->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(StringTools::startsWith($words[$i1], $token)) {
					return true;
				}
				unset($i1);
			}
		}
		return false;
	}
	public function reservedWordTokens($elem, $words) {
		$it = $elem->elements();
		while($it->hasNext()) {
			$this->reservedWordTokens($it->next(), $words);
		}
		if(_hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MROWS, "@" . $elem->getNodeName() . "@", null) !== -1) {
			$children = new _hx_array(array());
			$it = $elem->elements();
			while($it->hasNext()) {
				$children->push($it->next());
			}
			$index = 0;
			while($index < $children->length) {
				$c = $children[$index];
				if($c->getNodeName() === "mi") {
					$mis = new _hx_array(array());
					$mitexts = new _hx_array(array());
					while($c !== null && $c->getNodeName() === "mi") {
						$text = com_wiris_util_xml_WXmlUtils::getNodeValue($c->firstChild());
						$mitexts->push($text);
						$mis->push($c);
						$index++;
						$c = com_wiris_quizzes_impl_HTMLTools_3($this, $c, $children, $elem, $index, $it, $mis, $mitexts, $text, $words);
						unset($text);
					}
					$k = 0;
					while($k < $mis->length) {
						$word = $mitexts[$k];
						$lastReservedWord = null;
						$j = 0;
						$l = 0;
						while($this->isReservedWordPrefix($word, $words)) {
							if(com_wiris_system_ArrayEx::contains($words, $word)) {
								$lastReservedWord = $word;
								$l = $j;
							}
							$j++;
							if($j + $k >= $mis->length) {
								break;
							}
							$word .= $mitexts[$k + $j];
						}
						if($lastReservedWord !== null) {
							if($mitexts[$k] === $lastReservedWord) {
								_hx_array_get($mis, $k)->set("mathvariant", "normal");
							} else {
								_hx_array_get($mis, $k)->removeChild(_hx_array_get($mis, $k)->firstChild());
								_hx_array_get($mis, $k)->addChild(com_wiris_util_xml_WXmlUtils::createPCData($elem, $lastReservedWord));
								$m = null;
								{
									$_g = 0;
									while($_g < $l) {
										$m1 = $_g++;
										$k++;
										$mi = $mis[$k];
										$elem->removeChild($mi);
										unset($mi,$m1);
									}
									unset($_g);
								}
								unset($m);
							}
						}
						$k++;
						unset($word,$lastReservedWord,$l,$j);
					}
					unset($mitexts,$mis,$k);
				} else {
					if($c->getNodeName() === "mn") {
						$first = $c;
						$index++;
						$c = com_wiris_quizzes_impl_HTMLTools_4($this, $c, $children, $elem, $first, $index, $it, $words);
						if($c !== null && $c->getNodeName() === "mn") {
							$mns = new _hx_array(array());
							$num = new StringBuf();
							$num->add(com_wiris_util_xml_WXmlUtils::getNodeValue($first->firstChild()));
							while($c !== null && $c->getNodeName() === "mn") {
								$mns->push($c);
								$num->add(com_wiris_util_xml_WXmlUtils::getNodeValue($c->firstChild()));
								$index++;
								$c = com_wiris_quizzes_impl_HTMLTools_5($this, $c, $children, $elem, $first, $index, $it, $mns, $num, $words);
							}
							$first->removeChild($first->firstChild());
							$first->addChild(com_wiris_util_xml_WXmlUtils::createPCData($first, $num->b));
							$m = null;
							{
								$_g1 = 0; $_g = $mns->length;
								while($_g1 < $_g) {
									$m1 = $_g1++;
									$elem->removeChild($mns[$m1]);
									unset($m1);
								}
								unset($_g1,$_g);
							}
							unset($num,$mns,$m);
						}
						unset($first);
					} else {
						$index++;
						$c = com_wiris_quizzes_impl_HTMLTools_6($this, $c, $children, $elem, $index, $it, $words);
					}
				}
				unset($c);
			}
		}
	}
	public function restoreFlatMathML($elem) {
		$it = $elem->elements();
		while($it->hasNext()) {
			$this->restoreFlatMathML($it->next());
		}
		if(_hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MROWS, "@" . $elem->getNodeName() . "@", null) !== -1) {
			$children = $elem->elements();
			$elements = new _hx_array(array());
			while($children->hasNext()) {
				$elements->push($children->next());
			}
			if($elements->length > 0) {
				$current = $elements[0];
				$i = 1;
				while($i < $elements->length) {
					$previous = $current;
					$current = $elements[$i++];
					if(_hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MSUPS, "@" . $current->getNodeName() . "@", null) !== -1) {
						$elem->removeChild($previous);
						$current->insertChild($previous, 0);
					}
					unset($previous);
				}
			}
		}
	}
	public function removeMrows($elem) {
		if($elem->nodeType != Xml::$Element && $elem->nodeType != Xml::$Document) {
			return;
		}
		$children = $elem->iterator();
		while($children->hasNext()) {
			$this->removeMrows($children->next());
		}
		$children = $elem->iterator();
		$i = 0;
		while($children->hasNext()) {
			$c = $children->next();
			if($c->nodeType == Xml::$Element) {
				if($c->getNodeName() === "mrow") {
					$mrowChildren = $c->elements();
					$singlechild = false;
					if($mrowChildren->hasNext()) {
						$mrowChildren->next();
						$singlechild = !$mrowChildren->hasNext();
					}
					if($singlechild || _hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MROWS, $elem->getNodeName(), null) !== -1) {
						$elem->removeChild($c);
						$n = null;
						$count = 0;
						while(($n = $c->firstChild()) !== null) {
							$c->removeChild($n);
							$elem->insertChild($n, $i + $count);
							$count++;
						}
						if($count !== 1) {
							$i = -1;
							$children = $elem->iterator();
						}
						unset($n,$count);
					}
					unset($singlechild,$mrowChildren);
				} else {
					if($c->getNodeName() === "mfenced") {
						if("(" === $c->get("open")) {
							$c->remove("open");
						}
						if(")" === $c->get("close")) {
							$c->remove("close");
						}
					}
				}
			}
			$i++;
			unset($c);
		}
	}
	public function breakMis($elem, $pos) {
		if($elem->nodeType != Xml::$Element && $elem->nodeType != Xml::$Document) {
			return;
		}
		$children = $elem->iterator();
		$i = 0;
		while($children->hasNext()) {
			$this->breakMis($children->next(), $i);
			$i++;
		}
		if($elem->nodeType == Xml::$Element && $elem->getNodeName() === "mi") {
			$text = com_wiris_util_xml_WXmlUtils::getNodeValue($elem->firstChild());
			if(haxe_Utf8::length($text) > 1) {
				$p = $elem->_parent;
				$mrow = Xml::createElement("mrow");
				$p->removeChild($elem);
				$p->insertChild($mrow, $pos);
				while(strlen($text) > 0) {
					$mi = Xml::createElement("mi");
					$chartext = haxe_Utf8::sub($text, 0, 1);
					$mi->addChild(com_wiris_util_xml_WXmlUtils::createPCData($elem, $chartext));
					$text = _hx_substr($text, strlen($chartext), null);
					$mrow->addChild($mi);
					unset($mi,$chartext);
				}
			} else {
				$elem->remove("mathvariant");
			}
		}
	}
	public function flattenMsups($elem, $pos) {
		if($elem->nodeType != Xml::$Element && $elem->nodeType != Xml::$Document) {
			return;
		}
		$children = $elem->iterator();
		$i = 0;
		while($children->hasNext()) {
			$this->flattenMsups($children->next(), $i);
			$i++;
		}
		if($elem->nodeType == Xml::$Element && _hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MSUPS, "@" . $elem->getNodeName() . "@", null) !== -1) {
			$n = $elem->_parent;
			$mrow = Xml::createElement("mrow");
			$c = $elem->firstElement();
			$elem->removeChild($c);
			$mrow->addChild($c);
			$n->removeChild($elem);
			$mrow->addChild($elem);
			$n->insertChild($mrow, $pos);
		}
	}
	public function updateReservedWords($mathml, $words) {
		if($mathml === null || trim($mathml) === "") {
			return "";
		}
		$mathml = com_wiris_util_xml_WXmlUtils::resolveEntities($mathml);
		$doc = Xml::parse($mathml);
		$this->flattenMsups($doc, 0);
		$this->breakMis($doc, 0);
		$this->removeMrows($doc);
		$this->reservedWordTokens($doc->firstElement(), $words);
		$this->restoreFlatMathML($doc->firstElement());
		return com_wiris_util_xml_WXmlUtils::serializeXML($doc);
	}
	public function getParentTag($s, $n) {
		$stack = new _hx_array(array());
		$error = false;
		while(($n = _hx_index_of($s, "<", $n)) !== -1 && !$error) {
			if($this->isQuizzesIdentifierStart(_hx_char_code_at($s, $n + 1))) {
				$close = _hx_index_of($s, ">", $n);
				$space = _hx_index_of($s, " ", $n);
				if($space !== -1 && $space < $close) {
					$close = $space;
				}
				if($close !== -1) {
					$stack->push(_hx_substr($s, $n + 1, $close - $n - 1));
				} else {
					$error = true;
				}
				unset($space,$close);
			} else {
				if(_hx_char_code_at($s, $n + 1) === 47) {
					$close = _hx_index_of($s, ">", $n);
					$tag = _hx_substr($s, $n + 2, $close - $n - 2);
					if($stack->length === 0) {
						return $tag;
					} else {
						if(!($stack->pop() === $tag)) {
							$error = true;
						}
					}
					unset($tag,$close);
				} else {
					if(_hx_substr($s, $n, 4) === "<!--") {
						$n = _hx_index_of($s, "-->", $n);
						if($n === -1) {
							$error = true;
						}
					}
				}
			}
			$n++;
		}
		return null;
	}
	public function isColor($s, $n) {
		$gfColor = "color\":\"";
		$gfFill = "\"fill\":\"";
		$l = strlen($gfColor);
		return $n > $l && (_hx_substr($s, $n - $l, $l) === $gfColor || _hx_substr($s, $n - $l, $l) === $gfFill);
	}
	public function isEntity($s, $n) {
		if($n > 0 && _hx_char_code_at($s, $n - 1) === 38) {
			$n++;
			$end = _hx_index_of($s, ";", $n);
			if($end !== -1) {
				while($this->isQuizzesIdentifierPart(_hx_char_code_at($s, $n))) {
					$n++;
				}
				return $n === $end;
			}
		}
		return false;
	}
	public function variablePosition($s, $n) {
		if($this->insideTag($s, $n) || $this->isEntity($s, $n) || $this->insideComment($s, $n) || $this->isColor($s, $n)) {
			return com_wiris_quizzes_impl_HTMLTools::$POSITION_NONE;
		} else {
			$parent = $this->getParentTag($s, $n);
			if($parent === null) {
				return com_wiris_quizzes_impl_HTMLTools::$POSITION_ALL;
			}
			if($parent === "script" || $parent === "option") {
				return com_wiris_quizzes_impl_HTMLTools::$POSITION_ONLY_TEXT;
			} else {
				if($parent === "style") {
					return com_wiris_quizzes_impl_HTMLTools::$POSITION_NONE;
				} else {
					if($parent === "mi" || $parent === "mo" || $parent === "mtext" || $parent === "ms") {
						return com_wiris_quizzes_impl_HTMLTools::$POSITION_ONLY_MATHML;
					} else {
						if($parent === "td") {
							return com_wiris_quizzes_impl_HTMLTools::$POSITION_TABLE;
						} else {
							return com_wiris_quizzes_impl_HTMLTools::$POSITION_ALL;
						}
					}
				}
			}
		}
	}
	public function extractTextFromMathML($formula) {
		if(_hx_index_of($formula, "<mtext", null) === -1) {
			return $formula;
		}
		$allowedTags = new _hx_array(array("math", "mrow"));
		$stack = new _hx_array(array());
		$omittedcontent = false;
		$lasttag = null;
		$beginformula = _hx_index_of($formula, "<", null);
		$start = null;
		$end = 0;
		while($end < strlen($formula) && ($start = _hx_index_of($formula, "<", $end)) !== -1) {
			$end = _hx_index_of($formula, ">", $start);
			$tag = _hx_substr($formula, $start, $end - $start + 1);
			$trimmedTag = _hx_substr($formula, $start + 1, $end - $start - 1);
			if(_hx_substr($trimmedTag, strlen($trimmedTag) - 1, null) === "/") {
				continue;
			}
			$spacepos = _hx_index_of($tag, " ", null);
			if($spacepos !== -1) {
				$trimmedTag = _hx_substr($tag, 1, $spacepos - 1);
			}
			if($this->inArray($trimmedTag, $allowedTags)) {
				$stack->push(new _hx_array(array($trimmedTag, $tag)));
				$lasttag = $trimmedTag;
			} else {
				if($trimmedTag === "/" . $lasttag) {
					$stack->pop();
					if($stack->length > 0) {
						$lastpair = $stack[$stack->length - 1];
						$lasttag = $lastpair[0];
						unset($lastpair);
					} else {
						$lasttag = null;
					}
					if($stack->length === 0 && !$omittedcontent) {
						$formula1 = _hx_substr($formula, 0, $beginformula);
						if($end < strlen($formula) - 1) {
							$formula2 = _hx_substr($formula, $end + 1, null);
							$formula = $formula1 . $formula2;
							unset($formula2);
						} else {
							$formula = $formula1;
						}
						unset($formula1);
					}
				} else {
					if($trimmedTag === "mtext") {
						$pos2 = _hx_index_of($formula, "</mtext>", $start);
						$text = _hx_substr($formula, $start + 7, $pos2 - $start - 7);
						$text = com_wiris_util_xml_WXmlUtils::resolveEntities($text);
						$nbsp = com_wiris_quizzes_impl_HTMLTools_7($this, $allowedTags, $beginformula, $end, $formula, $lasttag, $omittedcontent, $pos2, $spacepos, $stack, $start, $tag, $text, $trimmedTag);
						$nbspLength = strlen($nbsp);
						if(strlen($text) >= $nbspLength) {
							if(_hx_substr($text, 0, $nbspLength) === $nbsp) {
								$text = " " . _hx_substr($text, $nbspLength, null);
							}
							if(strlen($text) >= $nbspLength && _hx_substr($text, strlen($text) - $nbspLength, null) === $nbsp) {
								$text = _hx_substr($text, 0, strlen($text) - $nbspLength) . " ";
							}
						}
						$formula1 = _hx_substr($formula, 0, $start);
						$formula2 = _hx_substr($formula, $pos2 + 8, null);
						if($omittedcontent) {
							$tail1 = "";
							$head2 = "";
							$i = $stack->length - 1;
							while($i >= 0) {
								$pair = $stack[$i];
								$tail1 = $tail1 . "</" . $pair[0] . ">";
								$head2 = $pair[1] . $head2;
								$i--;
								unset($pair);
							}
							$formula1 = $formula1 . $tail1;
							$formula2 = $head2 . $formula2;
							if(com_wiris_quizzes_impl_MathContent::isEmpty($formula2)) {
								$formula2 = "";
							}
							$formula = $formula1 . $text . $formula2;
							$beginformula = $start + strlen($tail1) + strlen($text);
							$end = $beginformula + strlen($head2);
							unset($tail1,$i,$head2);
						} else {
							$head = _hx_substr($formula1, 0, $beginformula);
							$head2 = _hx_substr($formula1, $beginformula, null);
							$formula2 = $head2 . $formula2;
							if(com_wiris_quizzes_impl_MathContent::isEmpty($formula2)) {
								$formula2 = "";
							}
							$formula = $head . $text . $formula2;
							$beginformula += strlen($text);
							$end = $beginformula + strlen($formula1);
							unset($head2,$head);
						}
						$omittedcontent = false;
						unset($text,$pos2,$nbspLength,$nbsp,$formula2,$formula1);
					} else {
						$num = 1;
						$pos = $start + strlen($tag);
						while($num > 0) {
							$end = _hx_index_of($formula, "</" . $trimmedTag . ">", $pos);
							$mid = _hx_index_of($formula, "<" . $trimmedTag, $pos);
							if($end === -1) {
								return $formula;
							} else {
								if($mid === -1 || $end < $mid) {
									$num--;
									$pos = $end + strlen(("</" . $trimmedTag . ">"));
								} else {
									$pos = $mid + strlen(("<" . $trimmedTag));
									$num++;
								}
							}
							unset($mid);
						}
						$end += strlen(("</" . $trimmedTag . ">"));
						$omittedcontent = true;
						unset($pos,$num);
					}
				}
			}
			unset($trimmedTag,$tag,$spacepos);
		}
		return $formula;
	}
	public function ImageB64Url($b64) {
		return "data:image/png;base64," . $b64;
	}
	public function addPlotterImageB64Tag($value) {
		$h = new com_wiris_quizzes_impl_HTML();
		$h->imageClass($this->ImageB64Url($value), null, "wirisplotter");
		return $h->getString();
	}
	public function addConstructionImageTag($value, $width, $height) {
		$h = new com_wiris_quizzes_impl_HTML();
		$h->openclose("img", new _hx_array(array(new _hx_array(array("src", $this->plotterLoadingSrc)), new _hx_array(array("alt", "Plotter")), new _hx_array(array("title", "Plotter")), new _hx_array(array("class", "wirisconstruction")), new _hx_array(array("data-wirisconstruction", $value)), new _hx_array(array("data-wiriswidth", _hx_string_rec($width, "") . "")), new _hx_array(array("data-wirisheight", _hx_string_rec($height, "") . "")))));
		return $h->getString();
	}
	public function addPlotterImageTag($filename) {
		$url = null;
		if(com_wiris_settings_PlatformSettings::$IS_JAVASCRIPT && StringTools::endsWith($filename, ".b64")) {
			$s = com_wiris_system_Storage::newStorage($filename);
			$url = $this->ImageB64Url($s->read());
		} else {
			$url = $this->proxyUrl . "?service=cache&name=" . $filename;
		}
		$h = new com_wiris_quizzes_impl_HTML();
		$h->imageClass($url, null, "wirisplotter");
		return $h->getString();
	}
	public function isTokensMathML($mathml) {
		return com_wiris_util_xml_MathMLUtils::isTokensMathML($mathml);
	}
	public function textToMathMLImpl($text) {
		$n = haxe_Utf8::length($text);
		if($n === 0) {
			return $text;
		}
		$mathml = new StringBuf();
		$token = null;
		$i = 0;
		$c = haxe_Utf8::charCodeAt($text, $i);
		while($i < $n) {
			if(com_wiris_util_xml_WCharacterBase::isDigit($c)) {
				$token = new StringBuf();
				while($i < $n && com_wiris_util_xml_WCharacterBase::isDigit($c)) {
					$token->b .= chr($c);
					if(++$i < $n) {
						$c = haxe_Utf8::charCodeAt($text, $i);
					}
				}
				$mathml->add("<mn>");
				$mathml->add($token->b);
				$mathml->add("</mn>");
			} else {
				if(com_wiris_util_xml_WCharacterBase::isLetter($c)) {
					$token = new StringBuf();
					while($i < $n && com_wiris_util_xml_WCharacterBase::isLetter($c)) {
						$token->add(com_wiris_quizzes_impl_HTMLTools_8($this, $c, $i, $mathml, $n, $text, $token));
						if(++$i < $n) {
							$c = haxe_Utf8::charCodeAt($text, $i);
						}
					}
					$tok = $token->b;
					$tokens = null;
					if($this->isReservedWord($tok)) {
						$tokens = new _hx_array(array($tok));
					} else {
						$m = haxe_Utf8::length($tok);
						$tokens = new _hx_array(array());
						$j = null;
						{
							$_g = 0;
							while($_g < $m) {
								$j1 = $_g++;
								$tokens[$j1] = com_wiris_quizzes_impl_HTMLTools_9($this, $_g, $c, $i, $j, $j1, $m, $mathml, $n, $text, $tok, $token, $tokens);
								unset($j1);
							}
							unset($_g);
						}
						unset($m,$j);
					}
					$k = null;
					{
						$_g1 = 0; $_g = $tokens->length;
						while($_g1 < $_g) {
							$k1 = $_g1++;
							$mathml->add("<mi>");
							$mathml->add($tokens[$k1]);
							$mathml->add("</mi>");
							unset($k1);
						}
						unset($_g1,$_g);
					}
					unset($tokens,$tok,$k);
				} else {
					if($c === 10) {
						$mathml->add("<mspace linebreak=\"newline\"/>");
					} else {
						$mathml->add("<mo>");
						if($c === 160 || $c === 32) {
							$mathml->add("&#xA0;");
						} else {
							$mathml->add(com_wiris_util_xml_WXmlUtils::htmlEscape(com_wiris_quizzes_impl_HTMLTools_10($this, $c, $i, $mathml, $n, $text, $token)));
						}
						$mathml->add("</mo>");
					}
					if(++$i < $n) {
						$c = haxe_Utf8::charCodeAt($text, $i);
					}
				}
			}
		}
		return $mathml->b;
	}
	public function textToMathMLWithSemantics($text) {
		$mathml = $this->textToMathMLImpl($text);
		$mathml = "<semantics><mrow>" . $mathml . "</mrow><annotation encoding=\"text/plain\">" . $text . "</annotation></semantics>";
		$result = com_wiris_quizzes_impl_HTMLTools::addMathTag($mathml);
		return $result;
	}
	public function textToMathML($text) {
		$mathml = $this->textToMathMLImpl($text);
		$result = com_wiris_quizzes_impl_HTMLTools::addMathTag($mathml);
		return $result;
	}
	public function isReservedWord($word) {
		return $this->isFunctionName($word);
	}
	public function isFunctionName($word) {
		$functionNames = new _hx_array(array("exp", "ln", "log", "sin", "sen", "cos", "tan", "tg", "asin", "arcsin", "asen", "arcsen", "acos", "arccos", "atan", "arctan", "cosec", "csc", "sec", "cotan", "acosec", "acsc", "asec", "acotan", "sinh", "senh", "cosh", "tanh", "asinh", "arcsinh", "asenh", "arcsenh", "acosh", "arccosh", "atanh", "arctanh", "cosech", "csch", "sech", "cotanh", "acosech", "acsch", "asech", "acotanh", "sign"));
		return $this->inArray($word, $functionNames) || $this->inArray($word, com_wiris_quizzes_impl_ActionCommands::$COMMANDS);
	}
	public function toSubFormula($mathml) {
		$mathml = com_wiris_util_xml_MathMLUtils::stripRootTag($mathml, "math");
		return "<mrow>" . $mathml . "</mrow>";
	}
	public function inArray($value, $array) {
		$i = null;
		{
			$_g1 = 0; $_g = $array->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($array[$i1] === $value) {
					return true;
				}
				unset($i1);
			}
		}
		return false;
	}
	public function prepareFormulas($text) {
		$start = 0;
		while(($start = _hx_index_of($text, "<math", $start)) !== -1) {
			if(_hx_index_of($text, "/>", $start) !== -1 && _hx_index_of($text, "/>", $start) < _hx_index_of($text, ">", $start)) {
				$start = _hx_index_of($text, ">", $start) + 1;
				continue;
			}
			$length = _hx_index_of($text, "</math>", $start) - $start + strlen("</math>");
			$formula = _hx_substr($text, $start, $length);
			$pos = 0;
			while(($pos = _hx_index_of($formula, "#", $pos)) !== -1) {
				$initag = $pos;
				while($initag >= 0 && _hx_char_code_at($formula, $initag) !== 60) {
					$initag--;
				}
				$parentpos = $initag;
				$parenttag = null;
				$parenttagname = null;
				while($parenttag === null) {
					while($parentpos >= 2 && _hx_char_code_at($formula, $parentpos - 2) === 47 && _hx_char_code_at($formula, $parentpos - 1) === 62) {
						$parentpos -= 2;
						while($parentpos >= 0 && _hx_char_code_at($formula, $parentpos) !== 60) {
							$parentpos--;
						}
					}
					$parentpos--;
					while($parentpos >= 0 && _hx_char_code_at($formula, $parentpos) !== 60) {
						$parentpos--;
					}
					if(_hx_char_code_at($formula, $parentpos) === 60 && _hx_char_code_at($formula, $parentpos + 1) === 47) {
						$namepos = $parentpos + strlen("</");
						$character = _hx_char_code_at($formula, $namepos);
						$nameBuf = new StringBuf();
						while($this->isQuizzesIdentifierPart($character)) {
							$nameBuf->b .= chr($character);
							$namepos++;
							$character = _hx_char_code_at($formula, $namepos);
						}
						$name = $nameBuf->b;
						$depth = 1;
						$namelength = strlen($name);
						while($depth > 0 && $parentpos >= 0) {
							$currentTagName = _hx_substr($formula, $parentpos, $namelength);
							if($name === $currentTagName) {
								$currentStartTag = _hx_substr($formula, $parentpos - strlen("<"), $namelength + strlen("<"));
								if("<" . $name === $currentStartTag && _hx_index_of($formula, ">", $parentpos) < _hx_index_of($formula, "/", $parentpos)) {
									$depth--;
								} else {
									$currentOpenCloseTag = _hx_substr($formula, $parentpos - strlen("</"), $namelength + strlen("</"));
									if("</" . $name === $currentOpenCloseTag) {
										$depth++;
									}
									unset($currentOpenCloseTag);
								}
								unset($currentStartTag);
							}
							if($depth > 0) {
								$parentpos--;
							} else {
								$parentpos -= strlen("<");
							}
							unset($currentTagName);
						}
						if($depth > 0) {
							return $text;
						}
						unset($namepos,$namelength,$nameBuf,$name,$depth,$character);
					} else {
						$parenttag = _hx_substr($formula, $parentpos, _hx_index_of($formula, ">", $parentpos) - $parentpos + 1);
						$parenttagname = _hx_substr($parenttag, 1, strlen($parenttag) - 2);
						if(_hx_index_of($parenttagname, " ", null) !== -1) {
							$parenttagname = _hx_substr($parenttagname, 0, _hx_index_of($parenttagname, " ", null));
						}
					}
				}
				if(_hx_index_of(com_wiris_quizzes_impl_HTMLTools::$MROWS, "@" . $parenttagname . "@", null) !== -1) {
					$firstchar = true;
					$appendpos = $pos + 1;
					$character = com_wiris_util_xml_WXmlUtils::getUtf8Char($formula, $appendpos);
					while($this->isQuizzesIdentifierStart($character) || $this->isQuizzesIdentifierPart($character) && !$firstchar) {
						$appendpos += strlen((com_wiris_quizzes_impl_HTMLTools_11($this, $appendpos, $character, $firstchar, $formula, $initag, $length, $parentpos, $parenttag, $parenttagname, $pos, $start, $text)));
						$character = com_wiris_util_xml_WXmlUtils::getUtf8Char($formula, $appendpos);
						$firstchar = false;
					}
					if(_hx_char_code_at($formula, $appendpos) !== 60) {
						$pos++;
						continue;
					}
					$nextpos = _hx_index_of($formula, ">", $pos);
					$end = false;
					while(!$end && $nextpos !== -1 && $pos + strlen(">") < strlen($formula)) {
						$nextpos += strlen(">");
						$nexttaglength = _hx_index_of($formula, ">", $nextpos) - $nextpos + strlen(">");
						$nexttag = _hx_substr($formula, $nextpos, $nexttaglength);
						$nexttagname = _hx_substr($nexttag, 1, strlen($nexttag) - 2);
						if(_hx_index_of($nexttagname, " ", null) !== -1) {
							$nexttagname = _hx_substr($nexttagname, 0, _hx_index_of($nexttagname, " ", null));
						}
						$specialtag = null;
						$speciallength = 0;
						if($nexttagname === "msup" || $nexttagname === "msub" || $nexttagname === "msubsup") {
							$specialtag = $nexttag;
							$speciallength = $nexttaglength;
							$nextpos = $nextpos + $nexttaglength;
							$nexttaglength = _hx_index_of($formula, ">", $nextpos) - $nextpos + strlen(">");
							$nexttag = _hx_substr($formula, $nextpos, $nexttaglength);
							$nexttagname = _hx_substr($nexttag, 1, strlen($nexttag) - 2);
							if(_hx_index_of($nexttagname, " ", null) !== -1) {
								$nexttagname = _hx_substr($nexttagname, 0, _hx_index_of($nexttagname, " ", null));
							}
						}
						if($nexttagname === "mi" || $nexttagname === "mn" || $nexttagname === "mo") {
							$contentpos = $nextpos + $nexttaglength;
							$toappend = new StringBuf();
							$character = com_wiris_util_xml_WXmlUtils::getUtf8Char($formula, $contentpos);
							while($this->isQuizzesIdentifierStart($character) || $this->isQuizzesIdentifierPart($character) && !$firstchar) {
								$charstr = com_wiris_quizzes_impl_HTMLTools_12($this, $appendpos, $character, $contentpos, $end, $firstchar, $formula, $initag, $length, $nextpos, $nexttag, $nexttaglength, $nexttagname, $parentpos, $parenttag, $parenttagname, $pos, $speciallength, $specialtag, $start, $text, $toappend);
								$contentpos += strlen($charstr);
								$toappend->add($charstr);
								$character = com_wiris_util_xml_WXmlUtils::getUtf8Char($formula, $contentpos);
								$firstchar = false;
								unset($charstr);
							}
							$toAppendStr = $toappend->b;
							$nextclosepos = _hx_index_of($formula, "<", $contentpos);
							$nextcloseend = _hx_index_of($formula, ">", $nextclosepos) + strlen(">");
							if(strlen($toAppendStr) === 0) {
								$end = true;
							} else {
								if($nextclosepos !== $contentpos) {
									$content = _hx_substr($formula, $contentpos, $nextclosepos - $contentpos);
									$nextclosetag = _hx_substr($formula, $nextclosepos, $nextcloseend - $nextclosepos);
									$newnexttag = $nexttag . $content . $nextclosetag;
									$formula = _hx_substr($formula, 0, $nextpos) . $newnexttag . _hx_substr($formula, $nextcloseend, null);
									$formula = _hx_substr($formula, 0, $appendpos) . $toAppendStr . _hx_substr($formula, $appendpos, null);
									$end = true;
									unset($nextclosetag,$newnexttag,$content);
								} else {
									$formula = _hx_substr($formula, 0, $nextpos) . _hx_substr($formula, $nextcloseend, null);
									$formula = _hx_substr($formula, 0, $appendpos) . $toAppendStr . _hx_substr($formula, $appendpos, null);
									if($specialtag !== null) {
										$fulltaglength = _hx_index_of($formula, ">", $appendpos) + strlen(">") - $initag;
										$formula = _hx_substr($formula, 0, $initag) . $specialtag . _hx_substr($formula, $initag, $fulltaglength) . _hx_substr($formula, $initag + $fulltaglength + $speciallength, null);
										$end = true;
										unset($fulltaglength);
									}
								}
							}
							$appendpos += strlen($toAppendStr);
							unset($toappend,$toAppendStr,$nextclosepos,$nextcloseend,$contentpos);
						} else {
							$end = true;
						}
						if(!$end) {
							$nextpos = _hx_index_of($formula, ">", $pos);
						}
						unset($specialtag,$speciallength,$nexttagname,$nexttaglength,$nexttag);
					}
					unset($nextpos,$firstchar,$end,$character,$appendpos);
				}
				$pos++;
				unset($parenttagname,$parenttag,$parentpos,$initag);
			}
			$text = _hx_substr($text, 0, $start) . $formula . _hx_substr($text, $start + $length, null);
			$start = $start + strlen($formula);
			unset($pos,$length,$formula);
		}
		return $text;
	}
	public function sortIterator($it) {
		$sorted = new _hx_array(array());
		while($it->hasNext()) {
			$a = $it->next();
			$j = 0;
			while($j < $sorted->length) {
				if(com_wiris_quizzes_impl_HTMLTools::compareStrings($sorted[$j], $a) > 0) {
					break;
				}
				$j++;
			}
			$sorted->insert($j, $a);
			unset($j,$a);
		}
		return $sorted;
	}
	public function getPlaceHolder($name) {
		return "#" . $name;
	}
	public function insideComment($html, $pos) {
		$beginComment = $this->lastIndexOf($html, "<!--", $pos);
		if($beginComment !== -1) {
			$endComment = $this->lastIndexOf($html, "-->", $pos);
			return $endComment < $beginComment;
		}
		return false;
	}
	public function lastIndexOf($src, $str, $pos) {
		return _hx_last_index_of(_hx_substr($src, 0, $pos), $str, null);
	}
	public function insideTag($html, $pos) {
		$beginTag = $this->lastIndexOf($html, "<", $pos);
		while($beginTag !== -1 && !$this->isQuizzesIdentifierStart(_hx_char_code_at($html, $beginTag + 1))) {
			if($beginTag === 0) {
				return false;
			}
			$beginTag = $this->lastIndexOf($html, "<", $beginTag - 1);
		}
		if($beginTag === -1) {
			return false;
		}
		$endTag = _hx_index_of($html, ">", $beginTag);
		return $endTag > $pos;
	}
	public function isQuizzesIdentifierPart($c) {
		return $this->isQuizzesIdentifierStart($c) || com_wiris_util_xml_WCharacterBase::isDigit($c);
	}
	public function isQuizzesIdentifierStart($c) {
		return com_wiris_util_xml_WCharacterBase::isLetter($c) || $c === 95;
	}
	public function isQuizzesIdentifier($s) {
		if($s === null) {
			return false;
		}
		$i = com_wiris_system_Utf8::getIterator($s);
		if(!$i->hasNext()) {
			return false;
		}
		if(!$this->isQuizzesIdentifierStart($i->next())) {
			return false;
		}
		while($i->hasNext()) {
			if(!$this->isQuizzesIdentifierPart($i->next())) {
				return false;
			}
		}
		return true;
	}
	public function getVariableName($html, $pos) {
		$name = null;
		if(_hx_char_code_at($html, $pos) === 35) {
			$end = $pos + 1;
			if($end < strlen($html)) {
				$c = com_wiris_util_xml_WXmlUtils::getUtf8Char($html, $end);
				if($this->isQuizzesIdentifierStart($c)) {
					$end += strlen((com_wiris_quizzes_impl_HTMLTools_13($this, $c, $end, $html, $name, $pos)));
					if($end < strlen($html)) {
						$c = com_wiris_util_xml_WXmlUtils::getUtf8Char($html, $end);
						while($c > 0 && $this->isQuizzesIdentifierPart($c)) {
							$end += strlen((com_wiris_quizzes_impl_HTMLTools_14($this, $c, $end, $html, $name, $pos)));
							$c = (($end < strlen($html)) ? com_wiris_util_xml_WXmlUtils::getUtf8Char($html, $end) : -1);
						}
					}
					$name = _hx_substr($html, $pos + 1, $end - ($pos + 1));
				}
			}
		}
		return $name;
	}
	public function replaceVariablesInsideHTMLTables($html, $variables) {
		$h = new com_wiris_quizzes_impl_HTMLTableTools($this->separator);
		return $h->replaceVariablesInsideHTMLTables($html, $variables);
	}
	public function replaceVariablesInsideHTML($token, $variables, $type, $escapeText) {
		$mathml = $type === com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
		$text = $type === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
		$imageRef = $type === com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF;
		$imageData = $type === com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE;
		$construction = $type === com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE;
		$keys = $this->sortIterator($variables->keys());
		$j = $keys->length - 1;
		while($j >= 0) {
			$name = $keys[$j];
			$placeholder = $this->getPlaceHolder($name);
			$formula = null;
			$posFormula = 0;
			$oldName = null;
			$oldValue = null;
			if(_hx_index_of($name, "_", null) > 0 && _hx_index_of($token, $name, null) !== -1 && _hx_index_of($variables->get($name), $name, null) !== -1) {
				$oldName = $name;
				$name = _hx_substr($name, 0, _hx_index_of($name, "_", null));
				if($this->assertTextSyntax($oldName)) {
					$formula = $this->replaceTextWithMathml($oldName);
				}
				$keys->remove($oldName);
			}
			$pos = 0;
			while(($pos = _hx_index_of($token, $placeholder, $pos)) !== -1) {
				$v = $this->variablePosition($token, $pos);
				if(($v === com_wiris_quizzes_impl_HTMLTools::$POSITION_ALL || $v === com_wiris_quizzes_impl_HTMLTools::$POSITION_TABLE || $text && $v === com_wiris_quizzes_impl_HTMLTools::$POSITION_ONLY_TEXT || $mathml && $v === com_wiris_quizzes_impl_HTMLTools::$POSITION_ONLY_MATHML) && ($name === $this->getVariableName($token, $pos) || $oldName !== null && $oldName === $this->getVariableName($token, $pos))) {
					$value = $variables->get($name);
					if($text && $escapeText) {
						$value = com_wiris_util_xml_WXmlUtils::htmlEscape($value);
					} else {
						if($mathml) {
							if($oldName !== null) {
								if($formula !== null) {
									$posFormula = _hx_index_of($formula, $this->getPlaceHolder($name), $posFormula);
									$itemSelector = $this->isPartOfMatrixVectorOrList($formula, $value, $posFormula);
									try {
										$value = $this->selectElementOfArray($value, $itemSelector, $formula, $posFormula);
									}catch(Exception $»e) {
										$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
										$t = $_ex_;
										{
											$itemSelector = com_wiris_quizzes_impl_HTMLTools::$NOT_A_SELECTOR;
										}
									}
									$value = "<mrow>" . _hx_substr($value, strlen("<math>"), strlen($value) - strlen("<math></math>")) . "</mrow>";
									$array = $this->returnFormula($formula, $value, $this->getPlaceHolder($name), $itemSelector, $posFormula);
									$value = $array[0];
									$value = $this->extractTextFromMathML($value);
									unset($t,$itemSelector,$array);
								} else {
									$value = (($variables->get($oldName) !== null) ? $variables->get($oldName) : $oldValue);
									$oldValue = $value;
									$value = com_wiris_quizzes_impl_HTMLTools::addMathTag($value);
									$value = $this->extractTextFromMathML($value);
								}
							} else {
								$value = com_wiris_quizzes_impl_HTMLTools::addMathTag($value);
								$value = $this->extractTextFromMathML($value);
							}
						} else {
							if($imageRef) {
								$value = $this->addPlotterImageTag($value);
							} else {
								if($imageData) {
									$value = $this->addPlotterImageB64Tag($value);
								} else {
									if($construction) {
										$width = 450;
										$height = 450;
										if($oldName !== null && $formula !== null) {
											$parts = _hx_explode("_", $oldName);
											if($parts->length === 2) {
												$square = $parts[1];
												$squareInt = Std::parseInt($square);
												if($squareInt > 0) {
													$width = $squareInt;
													$height = $squareInt;
												}
												unset($squareInt,$square);
											} else {
												if($parts->length === 3) {
													$fst = $parts[1];
													$snd = $parts[2];
													$fstInt = Std::parseInt($fst);
													$sndInt = Std::parseInt($snd);
													if($fstInt > 0 && $sndInt > 0) {
														$width = $fstInt;
														$height = $sndInt;
													}
													unset($sndInt,$snd,$fstInt,$fst);
												}
											}
											unset($parts);
										}
										$value = $this->addConstructionImageTag($value, $width, $height);
										unset($width,$height);
									}
								}
							}
						}
					}
					$token = _hx_substr($token, 0, $pos) . $value . _hx_substr($token, $pos + strlen($placeholder), null);
					if($value !== null) {
						$pos += strlen($value);
					}
					unset($value);
				} else {
					$pos++;
				}
				unset($v);
			}
			$j--;
			unset($posFormula,$pos,$placeholder,$oldValue,$oldName,$name,$formula);
		}
		return $token;
	}
	public function checkSubstringParameterAnswer($formula, $name, $lastPos, $pos) {
		if($this->answerKeyword === null) {
			return true;
		}
		if(_hx_index_of($formula, $this->getPlaceHolder($this->answerKeyword), $lastPos) !== $pos) {
			return true;
		}
		return _hx_index_of($name, $this->answerKeyword, null) !== -1;
	}
	public function failOnOutOfBounds($outOfBounds) {
		if($outOfBounds) {
			throw new HException("Out of bounds");
		}
	}
	public function selectElementOfArray($value, $itemSelector, $formula, $pos) {
		if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$NOT_A_SELECTOR) {
			return $value;
		}
		$positionToBeWritten = $this->position($formula, $pos);
		if($positionToBeWritten[0] === -1) {
			throw new HException("Positions are not integers!");
		}
		if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
			if($this->isPosition($formula, $pos)) {
				return $this->returnPositionOfElementOfArray(_hx_index_of($value, "<mtr>", null), $value, $itemSelector, $positionToBeWritten);
			} else {
				return $this->returnRowOfElementOfArray(_hx_index_of($value, "<mtr>", null), $value, $itemSelector, $positionToBeWritten);
			}
		} else {
			$iniWant = _hx_index_of($value, "<mrow>", _hx_index_of($value, "open", null));
			if(_hx_index_of($value, "<mrow>", $iniWant + 1) !== -1) {
				$iniWant = _hx_index_of($value, "<mrow>", $iniWant + 1) + strlen("<mrow>");
				if($this->isPosition($formula, $pos)) {
					return $this->returnPositionOfElementOfArray($iniWant, $value, $itemSelector, $positionToBeWritten);
				} else {
					return $this->returnRowOfElementOfArray($iniWant, $value, $itemSelector, $positionToBeWritten);
				}
			} else {
				$outOfBounds = $iniWant === -1;
				$iniWant += strlen("<mrow>");
				$k = 0;
				while($k < $positionToBeWritten->»a[0] - 1 && !$outOfBounds) {
					$iniWant = _hx_index_of($value, $this->separator, $iniWant + 1);
					$outOfBounds = $iniWant === -1;
					$k++;
				}
				if($k !== 0) {
					$iniWant += strlen($this->separator) + strlen("</mo>");
				}
				$endWant = _hx_index_of($value, $this->separator, $iniWant) - strlen("<mo>");
				if($endWant < 0) {
					$endWant = _hx_index_of($value, "</mrow></mfenced>", null);
				}
				$outOfBounds = $outOfBounds || $endWant === -1;
				$this->failOnOutOfBounds($outOfBounds);
				$block = _hx_substr($value, $iniWant, $endWant - $iniWant);
				return "<mrow>" . $block . "</mrow>";
			}
		}
	}
	public function returnRowOfElementOfArray($iniWant, $value, $itemSelector, $positionToBeWritten) {
		$outOfBounds = false;
		$rowStart = (($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) ? "<mtr>" : "<mrow>");
		$rowFinish = (($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) ? "</mtr>" : "</mrow>");
		$endWant = _hx_index_of($value, $rowFinish, null);
		$k = 0;
		while($k < $positionToBeWritten->»a[0] - 1 && !$outOfBounds) {
			$iniWant = _hx_index_of($value, $rowStart, $iniWant + 1);
			$endWant = _hx_index_of($value, $rowFinish, $endWant + 1);
			$outOfBounds = $iniWant === -1 || $endWant === -1;
			$k++;
		}
		$this->failOnOutOfBounds($outOfBounds);
		if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
			$endWant += strlen($rowFinish);
		}
		$blockFromStartToEndWant = _hx_substr($value, 0, $endWant);
		$isVector = _hx_last_index_of($blockFromStartToEndWant, "]", null) !== -1 && _hx_last_index_of($blockFromStartToEndWant, "]", null) === $this->maxValue(_hx_last_index_of($blockFromStartToEndWant, "]", null), _hx_last_index_of($blockFromStartToEndWant, "}", null));
		$block = _hx_substr($value, $iniWant, $endWant - $iniWant);
		return com_wiris_quizzes_impl_HTMLTools_15($this, $block, $blockFromStartToEndWant, $endWant, $iniWant, $isVector, $itemSelector, $k, $outOfBounds, $positionToBeWritten, $rowFinish, $rowStart, $value);
	}
	public function returnPositionOfElementOfArray($iniWant, $value, $itemSelector, $positionToBeWritten) {
		$outOfBounds = false;
		$rowStart = (($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) ? "<mtr>" : "<mrow>");
		$rowFinish = (($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) ? "</mtr>" : "</mrow>");
		$elementStart = com_wiris_quizzes_impl_HTMLTools_16($this, $iniWant, $itemSelector, $outOfBounds, $positionToBeWritten, $rowFinish, $rowStart, $value);
		$elementFinish = com_wiris_quizzes_impl_HTMLTools_17($this, $elementStart, $iniWant, $itemSelector, $outOfBounds, $positionToBeWritten, $rowFinish, $rowStart, $value);
		$k = 0;
		while($k < $positionToBeWritten->»a[0] - 1 && !$outOfBounds) {
			$iniWant = _hx_index_of($value, $rowStart, $iniWant + 1);
			$outOfBounds = $iniWant === -1;
			$k++;
		}
		$this->failOnOutOfBounds($outOfBounds);
		$value = _hx_substr($value, 0, _hx_index_of($value, $rowFinish, $iniWant));
		$iniWant = (($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) ? _hx_index_of($value, $elementStart, $iniWant) : $this->minValue(_hx_index_of($value, $elementStart, $iniWant), $this->minValue(_hx_index_of($value, "<mn>", $iniWant), _hx_index_of($value, "<mi>", $iniWant))));
		$outOfBounds = $iniWant === -1;
		$k = 0;
		while($k < $positionToBeWritten->»a[1] - 1 && !$outOfBounds) {
			$iniWant = _hx_index_of($value, $elementStart, $iniWant + 1);
			$outOfBounds = $iniWant === -1;
			$k++;
		}
		if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
			$iniWant += strlen($elementStart);
		} else {
			if($k !== 0) {
				$iniWant += strlen($this->separator) + strlen("</mo>");
			}
		}
		$endWant = _hx_index_of($value, $elementFinish, $iniWant);
		if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_1D) {
			$endWant = _hx_index_of($value, $this->separator, $iniWant) - strlen("<mo>");
			if($endWant < 0) {
				$endWant = strlen($value);
			}
		}
		$outOfBounds = $outOfBounds || $endWant === -1;
		$this->failOnOutOfBounds($outOfBounds);
		$block = _hx_substr($value, $iniWant, $endWant - $iniWant);
		return "<mrow>" . $block . "</mrow>";
	}
	public function returnFormula($formula, $value, $placeholder, $itemSelector, $pos) {
		$splittag = false;
		$formula1 = _hx_substr($formula, 0, $pos);
		$formula2 = _hx_substr($formula, $pos + strlen($placeholder), null);
		if($itemSelector !== com_wiris_quizzes_impl_HTMLTools::$NOT_A_SELECTOR) {
			$newPosition = _hx_last_index_of($formula1, "<msub>", null);
			$target = _hx_substr($formula1, $newPosition, strlen("<msub>"));
			if($newPosition !== -1) {
				$formula1 = com_wiris_util_type_StringUtils::replaceLastOccurrence($formula1, $target, "");
			}
			$targetPosition1 = _hx_index_of($formula2, "<mn>", null);
			$targetPosition2 = _hx_index_of($formula2, "</msub>", null) + strlen("</msub>");
			$target = _hx_substr($formula2, $targetPosition1, $targetPosition2 - $targetPosition1);
			$formula2 = com_wiris_util_type_StringUtils::replaceFirstOccurrence($formula2, $target, "");
			if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D && $this->isPosition($formula, $pos)) {
				$newPosition = _hx_index_of($formula2, "<mrow>", null);
				$target = _hx_substr($formula2, $newPosition, strlen("<mrow>"));
				if($newPosition !== -1) {
					$formula2 = com_wiris_util_type_StringUtils::replaceFirstOccurrence($formula2, $target, "");
				}
			}
		}
		$openTag1 = _hx_last_index_of($formula1, "<", null);
		$closeTag1 = _hx_last_index_of($formula1, ">", null);
		$openTag2 = _hx_index_of($formula2, "<", null);
		$closeTag2 = _hx_index_of($formula2, ">", null);
		$after = "";
		$before = "";
		if($closeTag1 + 1 < strlen($formula1)) {
			$splittag = true;
			$closeTag = _hx_substr($formula2, $openTag2, $closeTag2 - $openTag2 + 1);
			$before = _hx_substr($formula1, $openTag1, null) . $closeTag;
		}
		if($openTag2 > 0) {
			$splittag = true;
			$openTag = _hx_substr($formula1, $openTag1, $closeTag1 - $openTag1 + 1);
			$after = $openTag . _hx_substr($formula2, 0, $closeTag2 + 1);
		}
		$tag1 = _hx_substr($formula1, $openTag1, $closeTag1 + 1 - $openTag1);
		$isAnnotation = StringTools::startsWith($tag1, "<annotation");
		$space = _hx_index_of($tag1, " ", null);
		if($space !== -1 || $isAnnotation) {
			$attribs = com_wiris_quizzes_impl_HTMLTools_18($this, $after, $before, $closeTag1, $closeTag2, $formula, $formula1, $formula2, $isAnnotation, $itemSelector, $openTag1, $openTag2, $placeholder, $pos, $space, $splittag, $tag1, $value);
			$replaceTag = (($isAnnotation) ? "annotation" : "mstyle");
			if($attribs === " encoding=\"text/plain\"") {
				$value = $this->mathMLToText($value);
			}
			$value = "<" . $replaceTag . $attribs . ">" . $value . "</" . $replaceTag . ">";
		}
		$formula1 = _hx_substr($formula1, 0, $openTag1);
		$formula2 = _hx_substr($formula2, $closeTag2 + 1, null);
		if($splittag) {
			$formula = $formula1 . "<mrow>" . $before . $value . $after . "</mrow>" . $formula2;
		} else {
			$formula = $formula1 . $value . $formula2;
		}
		$array = new _hx_array(array());
		$array->push($formula);
		$array->push($value);
		return $array;
	}
	public function replaceMathMLVariablesInsideMathML($formula, $variables) {
		$keys = $this->sortIterator($variables->keys());
		$j = $keys->length - 1;
		while($j >= 0) {
			$name = $keys[$j];
			$placeholder = $this->getPlaceHolder($name);
			$pos = 0;
			$lastPos = 0;
			while(($pos = _hx_index_of($formula, $placeholder, $pos)) !== -1) {
				if($this->variablePosition($formula, $pos) >= 2 && $this->checkSubstringParameterAnswer($formula, $name, $lastPos, $pos)) {
					$value = $this->toSubFormula($variables->get($name));
					$itemSelector = $this->isPartOfMatrixVectorOrList($formula, $value, $pos);
					try {
						$value = $this->selectElementOfArray($value, $itemSelector, $formula, $pos);
					}catch(Exception $»e) {
						$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
						$t = $_ex_;
						{
							$itemSelector = com_wiris_quizzes_impl_HTMLTools::$NOT_A_SELECTOR;
						}
					}
					$array = $this->returnFormula($formula, $value, $placeholder, $itemSelector, $pos);
					$formula = $array[0];
					unset($value,$t,$itemSelector,$array);
				}
				$pos++;
				$lastPos = $pos;
			}
			$j--;
			unset($pos,$placeholder,$name,$lastPos);
		}
		return $formula;
	}
	public function fixPlottersWithDimensions($variables) {
		$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE);
		$plotters = $v->keys();
		while($plotters->hasNext()) {
			$plotter = $plotters->next();
			$mathmlV = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
			if($mathmlV !== null) {
				$mathmls = $mathmlV->keys();
				while($mathmls->hasNext()) {
					$mathml = $mathmls->next();
					if(StringTools::startsWith($mathml, $plotter . "_") && _hx_index_of($mathmlV->get($mathml), $mathml, null) !== -1) {
						$parts = _hx_explode("_", $mathml);
						if($parts->length === 2 && Std::parseInt($parts[1]) > 0 || $parts->length === 3 && Std::parseInt($parts[1]) > 0 && Std::parseInt($parts[2]) > 0) {
							$v->set($mathml, $mathmlV->get($mathml));
							$mathmlV->remove($mathml);
						}
						unset($parts);
					}
					unset($mathml);
				}
				unset($mathmls);
			}
			unset($plotter,$mathmlV);
		}
		return $v;
	}
	public function maxValue($a, $b) {
		if($a === -1 && $b !== -1) {
			return $b;
		}
		if($a !== -1 && $b === -1) {
			return $a;
		}
		if($a === -1 && $b === -1) {
			return -1;
		}
		return intval(Math::max($a, $b));
	}
	public function minValue($a, $b) {
		if($a === -1 && $b !== -1) {
			return $b;
		}
		if($a !== -1 && $b === -1) {
			return $a;
		}
		if($a === -1 && $b === -1) {
			return -1;
		}
		return intval(Math::min($a, $b));
	}
	public function position($formula, $pos) {
		$x = null;
		$y = -1;
		$posX = _hx_index_of($formula, "<mn>", $pos) + strlen("<mn>");
		if($this->isPosition($formula, $pos)) {
			$posY = _hx_index_of($formula, "<mn>", $posX) + strlen("<mn>");
			$valueStringX = _hx_substr($formula, $posX, _hx_index_of($formula, "</mn>", $posX) - $posX);
			$x = ((com_wiris_util_type_IntegerTools::isInt($valueStringX)) ? Std::parseInt($valueStringX) : -1);
			$valueStringY = _hx_substr($formula, $posY, _hx_index_of($formula, "</mn>", $posY) - $posY);
			$y = ((com_wiris_util_type_IntegerTools::isInt($valueStringY)) ? Std::parseInt($valueStringY) : -1);
			$start = _hx_index_of($formula, "</mn>", $posX) + strlen("</mn><mo>");
			$finish = $posY - strlen("</mo><mn>");
			if($start > $finish || _hx_index_of(_hx_substr($formula, $start, $finish - $start), $this->separator, null) === -1) {
				$x = -1;
				$y = -1;
			}
		} else {
			$valueStringX = _hx_substr($formula, $posX, _hx_index_of($formula, "</mn>", $posX) - $posX);
			$x = ((com_wiris_util_type_IntegerTools::isInt($valueStringX)) ? Std::parseInt($valueStringX) : -1);
		}
		return new _hx_array(array($x, $y));
	}
	public function isPosition($formula, $pos) {
		return (_hx_index_of($formula, "<mo>,</mo>", null) !== -1 || _hx_index_of($formula, "<mo>;</mo>", null) !== -1) && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mn>", $pos) + strlen("</mn>"), null), "<mo>,</mo>") || StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mn>", $pos) + strlen("</mn>"), null), "<mo>;</mo>");
	}
	public function isPartOfMatrixVectorOrList($formula, $value, $pos) {
		if(_hx_index_of($formula, "<msub>", null) !== -1 && StringTools::startsWith(_hx_substr($formula, $pos - strlen("<mo>") - strlen("<msub>"), null), "<msub>")) {
			if(_hx_index_of($value, "<mtable><mtr>", null) !== -1 && $this->isPosition($formula, $pos) && StringTools::startsWith(_hx_substr($formula, $pos - strlen("<mo>") - strlen("<msub>"), null), "<msub>") && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mo>", $pos) + strlen("</mo>"), null), "<mrow><mn>") && (_hx_index_of($formula, "<mo>,</mo>", null) !== -1 || _hx_index_of($formula, "<mo>;</mo>", null) !== -1) && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mn>", $pos) + strlen("</mn>"), null), "<mo>,</mo>") && StringTools::startsWith(_hx_substr($formula, $this->minValue(_hx_index_of($formula, "<mo>,</mo>", $pos), _hx_index_of($formula, "<mo>;</mo>", $pos)) + strlen("<mo>,</mo>"), null), "<mn>") && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mn>", $this->minValue(_hx_index_of($formula, "<mo>,</mo>", $pos), _hx_index_of($formula, "<mo>;</mo>", $pos))) + strlen("</mn>"), null), "</mrow></msub>")) {
				return com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D;
			}
			if(_hx_index_of($value, "<mtable><mtr>", null) !== -1 && !$this->isPosition($formula, $pos) && (StringTools::startsWith(_hx_substr($formula, $pos - strlen("<mo>") - strlen("<msub>"), null), "<msub>") && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mo>", $pos) + strlen("</mo>"), null), "<mn>") && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mn>", $pos) + strlen("</mn>"), null), "</msub>"))) {
				return com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D;
			}
			if((_hx_index_of($value, "[", null) !== -1 || _hx_index_of($value, "{", null) !== -1) && (StringTools::startsWith(_hx_substr($formula, $pos - strlen("<mo>") - strlen("<msub>"), null), "<msub>") && StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mo>", $pos) + strlen("</mo>"), null), "<mn>") || StringTools::startsWith(_hx_substr($formula, _hx_index_of($formula, "</mo>", $pos) + strlen("</mo>"), null), "<mrow>"))) {
				return com_wiris_quizzes_impl_HTMLTools::$SELECTOR_1D;
			}
		}
		return com_wiris_quizzes_impl_HTMLTools::$NOT_A_SELECTOR;
	}
	public function splitHTMLbyMathML($html) {
		$tokens = new _hx_array(array());
		$start = 0;
		$end = 0;
		while(($start = _hx_index_of($html, "<math", $end)) !== -1) {
			if($start - $end > 0) {
				$tokens->push(_hx_substr($html, $end, $start - $end));
			}
			$firstClose = _hx_index_of($html, ">", $start);
			if($firstClose !== -1 && _hx_substr($html, $firstClose - 1, 1) === "/") {
				$end = $firstClose + 1;
			} else {
				$end = _hx_index_of($html, "</math>", $start) + strlen("</math>");
			}
			$tokens->push(_hx_substr($html, $start, $end - $start));
			if($end + strlen("</p><p>") <= strlen($html) && !StringTools::startsWith(_hx_substr($html, $end + strlen("</p><p>"), null), "<math") && !StringTools::startsWith(_hx_substr($html, $end, null), "<math") && $end + strlen("</p>") >= strlen($html) + 1) {
				if(StringTools::startsWith(_hx_substr($html, $end, null), "</p>")) {
					$tokens->push("</p>");
					$start = _hx_index_of($html, "<p>", $end);
					$end = _hx_index_of($html, "</p>", $start);
					$tokens->push(_hx_substr($html, $start, $end - $start));
					$start = _hx_index_of($html, "<p>", $end);
					$end = _hx_index_of($html, "</p>", $start);
				} else {
					$start = $end;
					$end = ((_hx_index_of(_hx_substr($html, $start, null), "<math", null) !== -1) ? $this->minValue(_hx_index_of($html, "<math", $start), _hx_index_of($html, "</p>", $start)) : _hx_index_of($html, "</p>", $start));
					$tokens->push(_hx_substr($html, $start, $end - $start));
					$start = $end;
					$end = ((_hx_index_of(_hx_substr($html, $start, null), "<math", null) !== -1) ? $this->minValue(_hx_index_of($html, "<math", $start), _hx_index_of($html, "</p>", $start)) : _hx_index_of($html, "</p>", $start));
				}
			}
			unset($firstClose);
		}
		if($end < strlen($html)) {
			$tokens->push(_hx_substr($html, $end, null));
		}
		return $tokens;
	}
	public function assertTextSyntax($name) {
		$first = _hx_index_of($name, "_", null);
		$second = _hx_last_index_of($name, "_", null);
		if(StringTools::startsWith(_hx_substr($name, $first + 1, null), "_") || $first + 1 === strlen($name) || StringTools::startsWith(_hx_substr($name, $second - 1, null), "_") || $second + 1 === strlen($name)) {
			return false;
		}
		return true;
	}
	public function replaceTextWithMathml($name) {
		$previous = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\"><msub><mo>#";
		$end = "</msub></math>";
		$variableName = _hx_substr($name, 0, _hx_index_of($name, "_", null));
		if(_hx_index_of($name, "_", _hx_index_of($name, "_", null) + 1) !== -1) {
			$first = _hx_index_of($name, "_", null);
			$second = _hx_index_of($name, "_", _hx_index_of($name, "_", null) + 1);
			$subscript1 = _hx_substr($name, $first + 1, $second - ($first + 1));
			$openTag1 = ((com_wiris_util_type_IntegerTools::isInt($subscript1)) ? "<mn>" : "<mi>");
			$closeTag1 = ((com_wiris_util_type_IntegerTools::isInt($subscript1)) ? "</mn>" : "</mi>");
			$subscript2 = _hx_substr($name, $second + 1, null);
			$openTag2 = ((com_wiris_util_type_IntegerTools::isInt($subscript2)) ? "<mn>" : "<mi>");
			$closeTag2 = ((com_wiris_util_type_IntegerTools::isInt($subscript2)) ? "</mn>" : "</mi>");
			return $previous . $variableName . "</mo><mrow>" . $openTag1 . $subscript1 . $closeTag1 . "<mo>,</mo>" . $openTag2 . $subscript2 . $closeTag2 . "</mrow>" . $end;
		} else {
			$subscript = _hx_substr($name, _hx_index_of($name, "_", null) + 1, null);
			$openTag = ((com_wiris_util_type_IntegerTools::isInt($subscript)) ? "<mn>" : "<mi>");
			$closeTag = ((com_wiris_util_type_IntegerTools::isInt($subscript)) ? "</mn>" : "</mi>");
			return $previous . $variableName . "</mo>" . $openTag . $subscript . $closeTag . $end;
		}
	}
	public function expandVariables($html, $variables) {
		if($variables === null || _hx_index_of($html, "#", null) === -1) {
			return $html;
		}
		$html = $this->extractActionExpressions($html, null);
		$encoded = $this->isMathMLEncoded($html);
		if($encoded) {
			$html = $this->decodeMathML($html);
		}
		$html = com_wiris_util_xml_WXmlUtils::resolveEntities($html);
		$html = $this->prepareFormulas($html);
		$html = $this->replaceVariablesInsideHTMLTables($html, $variables);
		$tokens = $this->splitHTMLbyMathML($html);
		$sb = new StringBuf();
		{
			$_g1 = 0; $_g = $tokens->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$token = $tokens[$i];
				$v = null;
				if(StringTools::startsWith($token, "<math")) {
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
					if($v !== null) {
						$token = $this->replaceMathMLVariablesInsideMathML($token, $v);
					}
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
					if($v !== null) {
						$token = $this->replaceMathMLVariablesInsideMathML($token, $v);
					}
				} else {
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF);
					if($v !== null) {
						$token = $this->replaceVariablesInsideHTML($token, $v, com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE_REF, true);
					}
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE);
					if($v !== null) {
						$token = $this->replaceVariablesInsideHTML($token, $v, com_wiris_quizzes_impl_MathContent::$TYPE_IMAGE, true);
					}
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE);
					if($v !== null) {
						$v = $this->fixPlottersWithDimensions($variables);
						$token = $this->replaceVariablesInsideHTML($token, $v, com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE, true);
					}
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_MATHML);
					if($v !== null) {
						$token = $this->replaceVariablesInsideHTML($token, $v, com_wiris_quizzes_impl_MathContent::$TYPE_MATHML, true);
					}
					$v = $variables->get(com_wiris_quizzes_impl_MathContent::$TYPE_TEXT);
					if($v !== null) {
						$token = $this->replaceVariablesInsideHTML($token, $v, com_wiris_quizzes_impl_MathContent::$TYPE_TEXT, true);
					}
				}
				$sb->add($token);
				unset($v,$token,$i);
			}
		}
		$result = $sb->b;
		if($encoded) {
			$result = $this->encodeMathML($result);
		}
		return $result;
	}
	public function expandVariablesText($text, $textvariables) {
		return $this->replaceVariablesInsideHTML($text, $textvariables, com_wiris_quizzes_impl_MathContent::$TYPE_TEXT, false);
	}
	public function encodeMathML($html) {
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($html, "<math", $end)) !== -1) {
			$closemath = "</math>";
			$end = _hx_index_of($html, $closemath, $start) + strlen($closemath);
			$formula = _hx_substr($html, $start, $end - $start);
			$formula = str_replace("<", com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT, $formula);
			$formula = str_replace(">", com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_GT, $formula);
			$formula = str_replace("\"", com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_QUOT, $formula);
			$formula = str_replace("&", com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_AMP, $formula);
			$html = _hx_substr($html, 0, $start) . $formula . _hx_substr($html, $end, null);
			$end = $start + strlen($formula);
			unset($formula,$closemath);
		}
		return $html;
	}
	public function decodeMathML($html) {
		$closemath = com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT . "/math" . com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_GT;
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($html, com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT . "math", $end)) !== -1) {
			$end = _hx_index_of($html, $closemath, $start) + strlen($closemath);
			$formula = _hx_substr($html, $start, $end - $start);
			$formula = com_wiris_util_xml_WXmlUtils::htmlUnescape($formula);
			$formula = str_replace(com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT, "<", $formula);
			$formula = str_replace(com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_GT, ">", $formula);
			$formula = str_replace(com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_QUOT, "\"", $formula);
			$formula = str_replace(com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_AMP, "&", $formula);
			$html = _hx_substr($html, 0, $start) . $formula . _hx_substr($html, $end, null);
			$end = $start + strlen($formula);
			unset($formula);
		}
		return $html;
	}
	public function isMathMLEncoded($html) {
		return _hx_index_of($html, com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT . "math", null) !== -1;
	}
	public function extractVariableNames($html) {
		if($this->isMathMLEncoded($html)) {
			$html = $this->decodeMathML($html);
		}
		$html = com_wiris_util_xml_WXmlUtils::resolveEntities($html);
		$html = $this->prepareFormulas($html);
		$names = new _hx_array(array());
		$start = 0;
		while(($start = _hx_index_of($html, "#", $start)) !== -1) {
			if($this->variablePosition($html, $start) > 0) {
				$name = $this->getVariableName($html, $start);
				com_wiris_quizzes_impl_HTMLTools::insertStringInSortedArray($name, $names);
				if($name !== null && _hx_index_of($name, "_", null) !== -1 && $this->assertTextSyntax($name)) {
					$name = _hx_substr($name, 0, _hx_index_of($name, "_", null));
					com_wiris_quizzes_impl_HTMLTools::insertStringInSortedArray($name, $names);
				}
				unset($name);
			}
			$start++;
		}
		return com_wiris_quizzes_impl_HTMLTools::toNativeArray($names);
	}
	public function addComputedVariablesToAlgorithm($algorithm, $computedVariables) {
		$it = $computedVariables->keys();
		if($it->hasNext() && $algorithm === null) {
			$algorithm = com_wiris_quizzes_impl_HTMLTools::$EMPTY_CALCME_SESSION;
		}
		while($it->hasNext()) {
			$name = $it->next();
			$value = $computedVariables->get($name);
			$isMathML = _hx_index_of($value, "<mi>", null) !== -1;
			if($isMathML) {
				$value = str_replace("<mo>#</mo>", "<mi>#</mi>", $value);
				$value = $this->prepareFormulas("<math>" . $value . "</math>");
				$value = _hx_substr($value, strlen("<math>"), null);
				$value = _hx_substr($value, 0, strlen($value) - strlen("</math>"));
			}
			$value = str_replace("#", "", $value);
			if($isMathML) {
				$auxiliarString = new _hx_array(array(_hx_substr($algorithm, 0, _hx_last_index_of($algorithm, "</group>", null)), _hx_substr($algorithm, _hx_last_index_of($algorithm, "</group>", null), null)));
				$algorithm = $auxiliarString[0] . "<command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\">" . "<mi>" . $name . "</mi>" . "<mo>=</mo><mrow>" . $value . "</mrow></math></input></command>" . $auxiliarString[1];
				unset($auxiliarString);
			} else {
				$auxiliarString = new _hx_array(array(_hx_substr($algorithm, 0, _hx_last_index_of($algorithm, "</group>", null)), _hx_substr($algorithm, _hx_last_index_of($algorithm, "</group>", null), null)));
				$algorithm = $auxiliarString[0] . "<algorithm>" . $name . "=" . $value . "</algorithm>" . $auxiliarString[1];
				unset($auxiliarString);
			}
			unset($value,$name,$isMathML);
		}
		return $algorithm;
	}
	public function extractActionExpressions($html, $variables) {
		$originalHtml = $html;
		$html = com_wiris_util_xml_WXmlUtils::resolveEntities($html);
		$actions = com_wiris_quizzes_impl_ActionCommands::$COMMANDS;
		{
			$_g = 0;
			while($_g < $actions->length) {
				$action = $actions[$_g];
				++$_g;
				$bracketMathMLActionString = "<mi>" . $action . "</mi><mo>(</mo>";
				$mfencedMathMLActioNString = "<mi>" . $action . "</mi><mfenced>";
				$paragraphActionString = $action . "(";
				while(_hx_index_of($html, $bracketMathMLActionString, null) !== -1 || _hx_index_of($html, $mfencedMathMLActioNString, null) !== -1 || _hx_index_of($html, $paragraphActionString, null) !== -1) {
					$numberOfOpenBrackets = 0;
					$indexOfLastClosingBracket = 0;
					if(_hx_index_of($html, $bracketMathMLActionString, null) !== -1) {
						$numberOfClosedBrackets = 0;
						{
							$_g2 = _hx_index_of($html, $bracketMathMLActionString, null); $_g1 = _hx_last_index_of($html, "<mo>)</mo>", null) + strlen("<mo>)</mo>");
							while($_g2 < $_g1) {
								$i = $_g2++;
								if(_hx_char_at($html, $i) === "(") {
									$numberOfOpenBrackets++;
								} else {
									if(_hx_char_at($html, $i) === ")") {
										$numberOfClosedBrackets++;
									}
								}
								if($numberOfOpenBrackets === $numberOfClosedBrackets && $numberOfOpenBrackets !== 0) {
									$indexOfLastClosingBracket = $i + strlen("</mo>");
									break;
								}
								unset($i);
							}
							unset($_g2,$_g1);
						}
						if($numberOfOpenBrackets !== $numberOfClosedBrackets && $numberOfOpenBrackets !== 0 && $numberOfClosedBrackets !== 0) {
							return $originalHtml;
						}
						if(_hx_index_of($html, $bracketMathMLActionString, _hx_index_of($html, $bracketMathMLActionString, null) + 1) < $indexOfLastClosingBracket && _hx_index_of($html, $bracketMathMLActionString, _hx_index_of($html, $bracketMathMLActionString, null) + 1) !== -1) {
							return $originalHtml;
						}
						$evaluateFullString = _hx_substr($html, _hx_index_of($html, $bracketMathMLActionString, null), $indexOfLastClosingBracket - _hx_index_of($html, $bracketMathMLActionString, null) + 1);
						$valueInsideEvaluate = _hx_substr($evaluateFullString, strlen($bracketMathMLActionString), _hx_last_index_of($evaluateFullString, "<mo>)</mo>", null) - strlen($bracketMathMLActionString));
						$html = str_replace($evaluateFullString, "<mi>#_computed_variable_" . haxe_Md5::encode($evaluateFullString) . "</mi>", $html);
						$key = "_computed_variable_" . haxe_Md5::encode($evaluateFullString);
						if($variables !== null) {
							$variables->set($key, $valueInsideEvaluate);
						}
						unset($valueInsideEvaluate,$numberOfClosedBrackets,$key,$evaluateFullString);
					} else {
						if(_hx_index_of($html, $mfencedMathMLActioNString, null) !== -1) {
							$auxiliarIndexOpenBracket = _hx_index_of($html, $mfencedMathMLActioNString, null);
							$auxiliarIndexClosedBracket = _hx_index_of($html, "</mfenced>", _hx_index_of($html, $mfencedMathMLActioNString, null));
							while($numberOfOpenBrackets !== $indexOfLastClosingBracket || $numberOfOpenBrackets === 0) {
								if(_hx_index_of($html, "<mfenced>", $auxiliarIndexOpenBracket) < _hx_index_of($html, "</mfenced>", $auxiliarIndexClosedBracket) && _hx_index_of($html, "<mfenced>", $auxiliarIndexOpenBracket) !== -1) {
									$numberOfOpenBrackets++;
									$auxiliarIndexOpenBracket = _hx_index_of($html, "<mfenced>", $auxiliarIndexOpenBracket) + 1;
								}
								if(_hx_index_of($html, "<mfenced>", $auxiliarIndexOpenBracket) > _hx_index_of($html, "</mfenced>", $auxiliarIndexClosedBracket) || _hx_index_of($html, "<mfenced>", $auxiliarIndexOpenBracket) === -1) {
									$indexOfLastClosingBracket++;
									$auxiliarIndexClosedBracket = _hx_index_of($html, "</mfenced>", $auxiliarIndexClosedBracket) + 1;
								}
							}
							if(_hx_index_of($html, $mfencedMathMLActioNString, _hx_index_of($html, $mfencedMathMLActioNString, null) + 1) < $auxiliarIndexClosedBracket + 9 && _hx_index_of($html, $mfencedMathMLActioNString, _hx_index_of($html, $mfencedMathMLActioNString, null) + 1) !== -1) {
								return $originalHtml;
							}
							$evaluateFullString = _hx_substr($html, _hx_index_of($html, $mfencedMathMLActioNString, null), $auxiliarIndexClosedBracket + strlen("</mfenced>") - 1 - _hx_index_of($html, $mfencedMathMLActioNString, null));
							$valueInsideEvaluate = _hx_substr($evaluateFullString, strlen($mfencedMathMLActioNString), _hx_last_index_of($evaluateFullString, "</mfenced>", null) - strlen($mfencedMathMLActioNString));
							$html = str_replace($evaluateFullString, "<mi>#_computed_variable_" . haxe_Md5::encode($evaluateFullString) . "</mi>", $html);
							$key = "_computed_variable_" . haxe_Md5::encode($evaluateFullString);
							if($variables !== null) {
								$variables->set($key, $valueInsideEvaluate);
							}
							unset($valueInsideEvaluate,$key,$evaluateFullString,$auxiliarIndexOpenBracket,$auxiliarIndexClosedBracket);
						} else {
							if(_hx_index_of($html, $paragraphActionString, null) !== -1) {
								$numberOfClosedBrackets = 0;
								{
									$_g2 = _hx_index_of($html, $paragraphActionString, null); $_g1 = _hx_last_index_of($html, ")", null) + 1;
									while($_g2 < $_g1) {
										$i = $_g2++;
										if(_hx_char_at($html, $i) === "(") {
											$numberOfOpenBrackets++;
										} else {
											if(_hx_char_at($html, $i) === ")") {
												$numberOfClosedBrackets++;
											}
										}
										if($numberOfOpenBrackets === $numberOfClosedBrackets && $numberOfOpenBrackets !== 0) {
											$indexOfLastClosingBracket = $i;
											break;
										}
										unset($i);
									}
									unset($_g2,$_g1);
								}
								if($numberOfOpenBrackets !== $numberOfClosedBrackets && $numberOfOpenBrackets !== 0 && $numberOfClosedBrackets !== 0) {
									return $originalHtml;
								}
								if(_hx_index_of($html, $paragraphActionString, _hx_index_of($html, $paragraphActionString, null) + 1) < $indexOfLastClosingBracket && _hx_index_of($html, $paragraphActionString, _hx_index_of($html, $paragraphActionString, null) + 1) !== -1) {
									return $originalHtml;
								}
								$evaluateFullString = _hx_substr($html, _hx_index_of($html, $paragraphActionString, null), $indexOfLastClosingBracket + 1 - _hx_index_of($html, $paragraphActionString, null));
								$valueInsideEvaluate = _hx_substr($evaluateFullString, strlen($paragraphActionString), _hx_last_index_of($evaluateFullString, ")", null) - strlen($paragraphActionString));
								$html = str_replace($evaluateFullString, "#_computed_variable_" . haxe_Md5::encode($evaluateFullString), $html);
								$key = "_computed_variable_" . haxe_Md5::encode($evaluateFullString);
								if($variables !== null) {
									$variables->set($key, $valueInsideEvaluate);
								}
								unset($valueInsideEvaluate,$numberOfClosedBrackets,$key,$evaluateFullString);
							}
						}
					}
					unset($numberOfOpenBrackets,$indexOfLastClosingBracket);
				}
				unset($paragraphActionString,$mfencedMathMLActioNString,$bracketMathMLActionString,$action);
			}
		}
		return $html;
	}
	public $plotterLoadingSrc;
	public $proxyUrl;
	public $answerKeyword;
	public $separator = ",";
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
	static $POSITION_NONE = -1;
	static $POSITION_ONLY_TEXT = 1;
	static $POSITION_ONLY_MATHML = 2;
	static $POSITION_ALL = 3;
	static $POSITION_TABLE = 4;
	static $SELECTOR_2D = 2;
	static $SELECTOR_1D = 1;
	static $NOT_A_SELECTOR = 0;
	static $MROWS = "@math@mrow@msqrt@mstyle@merror@mpadded@mphantom@mtd@menclose@mscarry@msrow@";
	static $MSUPS = "@msub@msup@msubsup@";
	static $EMPTY_CALCME_SESSION;
	static function toNativeArray($a) {
		$n = new _hx_array(array());
		$k = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$k1 = $_g1++;
				$n[$k1] = $a[$k1];
				unset($k1);
			}
		}
		return $n;
	}
	static function insertStringInSortedArray($s, $a) {
		if($s !== null) {
			$i = 0;
			while($i < $a->length) {
				if(com_wiris_quizzes_impl_HTMLTools::compareStrings($a[$i], $s) >= 0) {
					break;
				}
				$i++;
			}
			if($i < $a->length) {
				if(!($a[$i] === $s)) {
					$a->insert($i, $s);
				}
			} else {
				$a->push($s);
			}
		}
	}
	static $SAFE_MATHML_LT;
	static $SAFE_MATHML_GT;
	static $SAFE_MATHML_QUOT;
	static $SAFE_MATHML_AMP;
	static function encodeUnicodeChars($mathml) {
		$sb = new StringBuf();
		$i = null;
		{
			$_g1 = 0; $_g = strlen($mathml);
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$c = _hx_char_code_at($mathml, $i1);
				if($c > 127) {
					$sb->add("&#");
					$sb->add($c);
					$sb->add(";");
				} else {
					$sb->b .= chr($c);
				}
				unset($i1,$c);
			}
		}
		return $sb->b;
	}
	static function compareStrings($a, $b) {
		$i = null;
		$an = strlen($a);
		$bn = strlen($b);
		$n = (($an > $bn) ? $bn : $an);
		{
			$_g = 0;
			while($_g < $n) {
				$i1 = $_g++;
				$c = _hx_char_code_at($a, $i1) - _hx_char_code_at($b, $i1);
				if($c !== 0) {
					return $c;
				}
				unset($i1,$c);
			}
		}
		return strlen($a) - strlen($b);
	}
	static function addMathTag($mathml) {
		if(!StringTools::startsWith($mathml, "<math")) {
			$mathml = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\">" . $mathml . "</math>";
		}
		return $mathml;
	}
	static function emptyCasSession($value) {
		return $value === null || _hx_index_of($value, "<mo", null) === -1 && _hx_index_of($value, "<mi", null) === -1 && _hx_index_of($value, "<mn", null) === -1 && _hx_index_of($value, "<csymbol", null) === -1 && _hx_index_of($value, "algorithm", null) === -1;
	}
	static function hasCasSessionParameter($session, $parameter, $name) {
		$session = com_wiris_util_xml_WXmlUtils::resolveEntities($session);
		$expr = com_wiris_quizzes_impl_HTMLTools::getParameterEReg($parameter, $name);
		$exprAL = com_wiris_quizzes_impl_HTMLTools::getParameterFromAlgorithmLine($parameter, $name);
		if($expr->match($session) || $exprAL->match($session)) {
			return true;
		} else {
			$noaccents = com_wiris_util_type_StringUtils::stripAccents($parameter);
			if(!($noaccents === $parameter)) {
				$expr = com_wiris_quizzes_impl_HTMLTools::getParameterEReg($noaccents, $name);
				$exprAL = com_wiris_quizzes_impl_HTMLTools::getParameterFromAlgorithmLine($noaccents, $name);
				return $expr->match($session) || $exprAL->match($session);
			}
			return false;
		}
	}
	static function getParameterEReg($parameter, $name) {
		return new EReg(".*<input>\\s*<math[^>]*>\\s*<mi>" . $parameter . "</mi>\\s*<mo>\\s*(" . com_wiris_quizzes_impl_HTMLTools_19($name, $parameter) . "|\\s)\\s*</mo><mi>" . $name . "\\d*</mi>.*", "gmi");
	}
	static function getParameterFromAlgorithmLine($parameter, $name) {
		return new EReg(".*" . $parameter . "\\s*" . $name . ".*", "gmi");
	}
	static function stripConstructionsFromCalcSession($calcSession) {
		if(com_wiris_quizzes_impl_CalcDocumentTools::isCalc($calcSession)) {
			$start = _hx_index_of($calcSession, "<wiriscalc", null);
			$end = _hx_index_of($calcSession, "</wiriscalc>", $start);
			$start = _hx_index_of($calcSession, "<constructions", $start);
			if($start > -1 && $start < $end) {
				$end = _hx_index_of($calcSession, "</constructions>", $start);
				$sb = new StringBuf();
				$sb->add(_hx_substr($calcSession, 0, $start));
				$sb->add(_hx_substr($calcSession, $end + strlen("</constructions>"), null));
				$calcSession = $sb->b;
			}
		}
		return $calcSession;
	}
	static function mathMLImgSrc($mathml, $centerBaseline, $zoom, $editorUrl, $proxyUrl, $crossOriginEnabled) {
		$src = com_wiris_quizzes_impl_HTMLTools_20($centerBaseline, $crossOriginEnabled, $editorUrl, $mathml, $proxyUrl, $zoom);
		$src .= "stats-app=quizzes&";
		if(!$centerBaseline) {
			$src .= "centerbaseline=false&";
		}
		if($zoom !== 1.0) {
			$src .= "zoom=" . _hx_string_rec($zoom, "") . "&";
		}
		$mathml = com_wiris_util_xml_MathMLUtils::removeStrokesAnnotation($mathml);
		$mathml = rawurlencode(com_wiris_quizzes_impl_HTMLTools::encodeUnicodeChars($mathml));
		$src .= "mml=" . $mathml;
		return $src;
	}
	static function getEmptyCalcMeSession() {
		return "<wiriscalc version=\"3.1\"><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>UntitledÂ calc</mtext></math></title><properties><property name=\"lang\">en</property><property name=\"precision\">4</property><property name=\"use_degrees\">false</property></properties><session version=\"3.0\" lang=\"en\"><task><title><math xmlns=\"http://www.w3.org/1998/Math/MathML\"><mtext>SheetÂ 1</mtext></math></title><group><command><input><math xmlns=\"http://www.w3.org/1998/Math/MathML\"/></input></command></group></task></session></wiriscalc>";
	}
	function __toString() { return 'com.wiris.quizzes.impl.HTMLTools'; }
}
com_wiris_quizzes_impl_HTMLTools::$EMPTY_CALCME_SESSION = "<wiriscalc version=\"3.2\">\x0A" . "  <title>\x0A" . "    <math xmlns=\"http://www.w3.org/1998/Math/MathML\">\x0A" . "      <mtext></mtext>\x0A" . "    </math>\x0A" . "  </title>\x0A" . "  <session version=\"3.0\">\x0A" . "      <group>\x0A" . "        <command>\x0A" . "          <input>\x0A" . "            <math xmlns=\"http://www.w3.org/1998/Math/MathML\"/>\x0A" . "          </input>\x0A" . "        </command>\x0A" . "      </group>\x0A" . "  </session>\x0A" . "</wiriscalc>";
com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_LT = com_wiris_quizzes_impl_HTMLTools_21();
com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_GT = com_wiris_quizzes_impl_HTMLTools_22();
com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_QUOT = com_wiris_quizzes_impl_HTMLTools_23();
com_wiris_quizzes_impl_HTMLTools::$SAFE_MATHML_AMP = com_wiris_quizzes_impl_HTMLTools_24();
function com_wiris_quizzes_impl_HTMLTools_0(&$»this, &$_g, &$_g1, &$a, &$answer, &$answers, &$compound, &$h, &$i, &$i1, &$s) {
	if($»this->isMathMLString($s)) {
		return com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
	} else {
		return com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
	}
}
function com_wiris_quizzes_impl_HTMLTools_1(&$»this, &$close, &$e, &$i, &$it, &$n, &$open, &$sb, &$separators) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(haxe_Utf8::charCodeAt($separators, com_wiris_quizzes_impl_HTMLTools_25($close, $e, $i, $it, $n, $open, $s, $sb, $separators)));
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_2(&$»this, &$mathml, &$root) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_3(&$»this, &$c, &$children, &$elem, &$index, &$it, &$mis, &$mitexts, &$text, &$words) {
	if($index < $children->length) {
		return $children[$index];
	}
}
function com_wiris_quizzes_impl_HTMLTools_4(&$»this, &$c, &$children, &$elem, &$first, &$index, &$it, &$words) {
	if($index < $children->length) {
		return $children[$index];
	}
}
function com_wiris_quizzes_impl_HTMLTools_5(&$»this, &$c, &$children, &$elem, &$first, &$index, &$it, &$mns, &$num, &$words) {
	if($index < $children->length) {
		return $children[$index];
	}
}
function com_wiris_quizzes_impl_HTMLTools_6(&$»this, &$c, &$children, &$elem, &$index, &$it, &$words) {
	if($index < $children->length) {
		return $children[$index];
	}
}
function com_wiris_quizzes_impl_HTMLTools_7(&$»this, &$allowedTags, &$beginformula, &$end, &$formula, &$lasttag, &$omittedcontent, &$pos2, &$spacepos, &$stack, &$start, &$tag, &$text, &$trimmedTag) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_8(&$»this, &$c, &$i, &$mathml, &$n, &$text, &$token) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_9(&$»this, &$_g, &$c, &$i, &$j, &$j1, &$m, &$mathml, &$n, &$text, &$tok, &$token, &$tokens) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(haxe_Utf8::charCodeAt($tok, $j1));
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_10(&$»this, &$c, &$i, &$mathml, &$n, &$text, &$token) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_11(&$»this, &$appendpos, &$character, &$firstchar, &$formula, &$initag, &$length, &$parentpos, &$parenttag, &$parenttagname, &$pos, &$start, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($character);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_12(&$»this, &$appendpos, &$character, &$contentpos, &$end, &$firstchar, &$formula, &$initag, &$length, &$nextpos, &$nexttag, &$nexttaglength, &$nexttagname, &$parentpos, &$parenttag, &$parenttagname, &$pos, &$speciallength, &$specialtag, &$start, &$text, &$toappend) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($character);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_13(&$»this, &$c, &$end, &$html, &$name, &$pos) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_14(&$»this, &$c, &$end, &$html, &$name, &$pos) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_15(&$»this, &$block, &$blockFromStartToEndWant, &$endWant, &$iniWant, &$isVector, &$itemSelector, &$k, &$outOfBounds, &$positionToBeWritten, &$rowFinish, &$rowStart, &$value) {
	if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
		return "<mrow><mfenced><mtable>" . $block . "</mtable></mfenced></mrow>";
	} else {
		if($isVector) {
			return "<mrow><mrow><mfenced close=\"]\" open=\"[\"><mrow>" . $block . "</mrow></mfenced></mrow></mrow>";
		} else {
			return "<mrow><mrow><mfenced close=\"}\" open=\"{\"><mrow>" . $block . "</mrow></mfenced></mrow></mrow>";
		}
	}
}
function com_wiris_quizzes_impl_HTMLTools_16(&$»this, &$iniWant, &$itemSelector, &$outOfBounds, &$positionToBeWritten, &$rowFinish, &$rowStart, &$value) {
	if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
		return "<mtd>";
	} else {
		return $»this->separator;
	}
}
function com_wiris_quizzes_impl_HTMLTools_17(&$»this, &$elementStart, &$iniWant, &$itemSelector, &$outOfBounds, &$positionToBeWritten, &$rowFinish, &$rowStart, &$value) {
	if($itemSelector === com_wiris_quizzes_impl_HTMLTools::$SELECTOR_2D) {
		return "</mtd>";
	} else {
		return $»this->separator;
	}
}
function com_wiris_quizzes_impl_HTMLTools_18(&$»this, &$after, &$before, &$closeTag1, &$closeTag2, &$formula, &$formula1, &$formula2, &$isAnnotation, &$itemSelector, &$openTag1, &$openTag2, &$placeholder, &$pos, &$space, &$splittag, &$tag1, &$value) {
	if($space !== -1) {
		return " " . _hx_substr($tag1, $space + 1, strlen($tag1) - 1 - ($space + 1));
	} else {
		return "";
	}
}
function com_wiris_quizzes_impl_HTMLTools_19(&$name, &$parameter) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(160);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_20(&$centerBaseline, &$crossOriginEnabled, &$editorUrl, &$mathml, &$proxyUrl, &$zoom) {
	if($crossOriginEnabled) {
		return $editorUrl . "/render?";
	} else {
		return $proxyUrl . "?service=render&";
	}
}
function com_wiris_quizzes_impl_HTMLTools_21() {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(171);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_22() {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(187);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_23() {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(168);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_24() {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(167);
		return $s->toString();
	}
}
function com_wiris_quizzes_impl_HTMLTools_25(&$close, &$e, &$i, &$it, &$n, &$open, &$s, &$sb, &$separators) {
	if($i < $n) {
		return $i;
	} else {
		return $n - 1;
	}
}

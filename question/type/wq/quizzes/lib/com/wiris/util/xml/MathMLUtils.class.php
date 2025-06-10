<?php

class com_wiris_util_xml_MathMLUtils {
	public function __construct() { 
	}
	static $contentTagsString = "ci@cn@apply@integers@reals@rationals@naturalnumbers@complexes@primes@exponentiale@imaginaryi@notanumber@true@false@emptyset@pi@eulergamma@infinity";
	static $contentTags;
	static $presentationTagsString = "mrow@mn@mi@mo@mfrac@mfenced@mroot@maction@mphantom@msqrt@mstyle@msub@msup@msubsup@munder@mover@munderover@menclose@mspace@mtext@ms";
	static $presentationTags;
	static $MROWS = "@math@mrow@msqrt@mstyle@merror@mpadded@mphantom@mtd@menclose@mscarry@msrow@";
	static $strokesAnnotationEncondings;
	static function isPresentationMathML($mathml) {
		if(com_wiris_util_xml_MathMLUtils::$presentationTags === null) {
			com_wiris_util_xml_MathMLUtils::$presentationTags = _hx_explode("@", com_wiris_util_xml_MathMLUtils::$presentationTagsString);
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLType($mathml, false, com_wiris_util_xml_MathMLUtils::$presentationTags);
	}
	static function isContentMathML($mathml) {
		if(com_wiris_util_xml_MathMLUtils::$contentTags === null) {
			com_wiris_util_xml_MathMLUtils::$contentTags = _hx_explode("@", com_wiris_util_xml_MathMLUtils::$contentTagsString);
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLType($mathml, true, com_wiris_util_xml_MathMLUtils::$contentTags);
	}
	static function isMathMLType($mathml, $content, $tags) {
		$node = com_wiris_util_xml_WXmlUtils::parseXML($mathml);
		if($node->nodeType == Xml::$Document) {
			$node = $node->firstElement();
		}
		if($node->getNodeName() === "math") {
			$elements = $node->elements();
			if($elements->hasNext() && $elements->next() !== null && $elements->hasNext()) {
				return !$content;
			}
		}
		return com_wiris_util_xml_MathMLUtils::isMathMLTypeImpl($node, $tags);
	}
	static function isMathMLTypeImpl($node, $contentTags) {
		if($node->nodeType == Xml::$Element) {
			if($node->getNodeName() === "annotation-xml" || $node->getNodeName() === "annotation") {
				return false;
			}
			$i = $contentTags->iterator();
			while($i->hasNext()) {
				if($node->getNodeName() === $i->next()) {
					return true;
				}
			}
		}
		$j = $node->elements();
		while($j->hasNext()) {
			if(com_wiris_util_xml_MathMLUtils::isMathMLTypeImpl($j->next(), $contentTags)) {
				return true;
			}
		}
		return false;
	}
	static function isContentMathMLTag($tag) {
		return _hx_index_of(com_wiris_util_xml_MathMLUtils::$contentTagsString, $tag, null) !== -1;
	}
	static function removeStrokesAnnotation($mathml) {
		$start = null;
		$end = 0;
		while(($start = _hx_index_of($mathml, "<semantics>", $end)) !== -1) {
			$end = _hx_index_of($mathml, "</semantics>", $start);
			if($end === -1) {
				throw new HException("Error parsing semantics tag in MathML.");
			}
			$a = com_wiris_util_xml_MathMLUtils::strokesAnnotationStart($mathml, $start, $end);
			if($a !== -1) {
				$b = _hx_index_of($mathml, "</annotation>", $a);
				if($b === -1 || $b >= $end) {
					throw new HException("Error parsing annotation tag in MathML.");
				}
				$b += 13;
				$mathml = _hx_substr($mathml, 0, $a) . _hx_substr($mathml, $b, null);
				$end -= $b - $a;
				$x = _hx_index_of($mathml, "<annotation", $start);
				if($x === -1 || $x > $end) {
					$mathml = _hx_substr($mathml, 0, $start) . _hx_substr($mathml, $start + 11, $end - ($start + 11)) . _hx_substr($mathml, $end + 12, null);
					$end -= 11;
				}
				unset($x,$b);
			}
			unset($a);
		}
		return $mathml;
	}
	static function strokesAnnotationStart($mathml, $start, $end) {
		{
			$_g1 = 0; $_g = com_wiris_util_xml_MathMLUtils::$strokesAnnotationEncondings->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$a = _hx_index_of($mathml, "<annotation encoding=\"" . com_wiris_util_xml_MathMLUtils::$strokesAnnotationEncondings[$i] . "\">", $start);
				if($a !== -1 && $a < $end) {
					return $a;
				}
				unset($i,$a);
			}
		}
		return -1;
	}
	static function isEmptyMathML($math) {
		$empty = true;
		if($math->nodeType == Xml::$Document) {
			$empty = com_wiris_util_xml_MathMLUtils::isEmptyMathML($math->firstElement());
		} else {
			if($math->nodeType == Xml::$Element) {
				$name = $math->getNodeName();
				if($name === "mtext") {
					if($math->iterator()->hasNext()) {
						$child = $math->firstChild();
						$value = com_wiris_util_xml_WXmlUtils::getNodeValue($child);
						if($value !== null && !($value === "")) {
							$empty = false;
						}
					}
				} else {
					if(!($name === "math" || $name === "mrow")) {
						$empty = false;
					} else {
						$children = $math->elements();
						while($children->hasNext()) {
							$next = $children->next();
							$empty = $empty && com_wiris_util_xml_MathMLUtils::isEmptyMathML($next);
							unset($next);
						}
					}
				}
			}
		}
		return $empty;
	}
	static function isTokensMathML($mathml) {
		if($mathml === null) {
			return false;
		}
		$mathml = com_wiris_util_xml_MathMLUtils::stripRootTag($mathml, "math");
		$allowedTags = new _hx_array(array("mrow", "mn", "mi", "mo", "mtext", "mfenced"));
		$start = 0;
		while(($start = _hx_index_of($mathml, "<", $start)) !== -1) {
			$sb = new StringBuf();
			$c = _hx_char_code_at($mathml, ++$start);
			if($c === 47) {
				continue;
			}
			while($c !== 32 && $c !== 47 && $c !== 62) {
				$sb->b .= chr($c);
				$c = _hx_char_code_at($mathml, ++$start);
			}
			$tagName = $sb->b;
			if($c === 32 || $c === 47) {
				return false;
			}
			if(!com_wiris_util_type_Arrays::containsArray($allowedTags, $tagName)) {
				return false;
			}
			$end = _hx_index_of($mathml, "<", ++$start);
			$content = _hx_substr($mathml, $start, $end - $start);
			$content = com_wiris_util_xml_WXmlUtils::resolveEntities($content);
			$content = str_replace("&lt;", "<", $content);
			$content = str_replace("&gt;", ">", $content);
			$content = str_replace("&quot;", "\"", $content);
			$content = str_replace("&apos;", "'", $content);
			$content = str_replace("&amp;", "&", $content);
			$i = com_wiris_system_Utf8::getIterator($content);
			while($i->hasNext()) {
				$c = $i->next();
				if(!com_wiris_util_xml_MathMLUtils::isKeyboardChar($c) && !com_wiris_util_xml_WCharacterBase::isLetter($c) && !com_wiris_util_xml_WCharacterBase::isDigit($c) && $c !== com_wiris_util_xml_WCharacterBase::$NO_BREAK_SPACE && $c !== com_wiris_util_xml_WCharacterBase::$THIN_SPACE && $c !== com_wiris_util_xml_WCharacterBase::$NUMBER_SIGN) {
					return false;
				}
			}
			unset($tagName,$sb,$i,$end,$content,$c);
		}
		return true;
	}
	static function isKeyboardChar($c) {
		return $c >= 32 && $c <= 126 || $c >= 161 && $c <= 191 || $c === 8364;
	}
	static function stripRootTag($xml, $tag) {
		$s = com_wiris_util_xml_MathMLUtils::splitRootTag($xml, $tag);
		return $s[1];
	}
	static function splitRootTag($xml, $tag) {
		$xml = trim($xml);
		$r = new _hx_array(array("", null, ""));
		if(StringTools::startsWith($xml, "<" . $tag)) {
			$depth = 1;
			$lastOpen = _hx_last_index_of($xml, "<", null);
			$lastClose = _hx_last_index_of($xml, ">", null);
			$j1 = _hx_index_of($xml, "<" . $tag, 1);
			$j2 = _hx_index_of($xml, "</" . $tag, 1);
			$j3 = _hx_index_of($xml, "/>", null);
			if(_hx_index_of($xml, ">", null) - $j3 !== 1) {
				$j3 = -1;
			}
			while($depth > 0) {
				if(($j1 === -1 || $j2 < $j1) && ($j3 === -1 || $j2 < $j3)) {
					$depth--;
					if($depth > 0) {
						$j2 = _hx_index_of($xml, "</" . $tag, $j2 + 1);
					}
				} else {
					if($j1 !== -1 && ($j3 === -1 || $j1 < $j3)) {
						$depth++;
						$j3 = _hx_index_of($xml, "/>", $j1);
						if(_hx_index_of($xml, ">", $j1) - $j3 !== 1) {
							$j3 = -1;
						}
						$j1 = _hx_index_of($xml, "<" . $tag, $j1 + 1);
					} else {
						$depth--;
						$j3 = -1;
					}
				}
			}
			if($j2 === $lastOpen) {
				$ini = _hx_index_of($xml, ">", null) + 1;
				$r[0] = _hx_substr($xml, 0, $ini);
				$r[1] = _hx_substr($xml, $ini, $lastOpen - $ini);
				$r[2] = _hx_substr($xml, $lastOpen, null);
			} else {
				if($j3 + 1 === $lastClose) {
					$r[0] = _hx_substr($xml, 0, strlen($xml) - 2) . ">";
					$r[1] = "";
					$r[2] = "</" . $tag . ">";
				} else {
					$r[1] = $xml;
				}
			}
		} else {
			$r[1] = $xml;
		}
		return $r;
	}
	static function mathMLToText($mathml) {
		$root = com_wiris_util_xml_WXmlUtils::parseXML($mathml);
		if($root->nodeType == Xml::$Document) {
			$root = $root->firstElement();
		}
		com_wiris_util_xml_MathMLUtils::removeMrows($root);
		return com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($root);
	}
	static function fullMathML2TextImpl($e) {
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
				if($open === "(" && $close === ")" && $e->firstElement()->getNodeName() === "mtable") {
					$open = "";
					$close = "";
				}
				$sb->add($open);
				$it = $e->elements();
				$i = 0;
				$n = haxe_Utf8::length($separators);
				while($it->hasNext()) {
					if($i > 0 && $n > 0) {
						$sb->add(com_wiris_util_xml_MathMLUtils_0($close, $e, $i, $it, $n, $open, $sb, $separators));
					}
					$sb->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next()));
					$i++;
				}
				$sb->add($close);
			} else {
				if($e->getNodeName() === "mfrac") {
					$it = $e->elements();
					$num = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
					if(strlen($num) > 1) {
						$num = "(" . $num . ")";
					}
					$den = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
					if(strlen($den) > 1) {
						$den = "(" . $den . ")";
					}
					$sb->add($num);
					$sb->add("/");
					$sb->add($den);
				} else {
					if($e->getNodeName() === "msup") {
						$it = $e->elements();
						$bas = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
						if(strlen($bas) > 1) {
							$bas = "(" . $bas . ")";
						}
						$exp = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
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
							$sb->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($e));
							$sb->add(")");
						} else {
							if($e->getNodeName() === "mroot") {
								$it = $e->elements();
								$rad = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
								$ind = com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($it->next());
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
											return com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($mml);
										}
									} else {
										$it = $e->elements();
										while($it->hasNext()) {
											$x = $it->next();
											$sb->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($x));
											if($x->getNodeName() === "mi" && com_wiris_util_xml_MathMLUtils::isFunctionName(com_wiris_util_xml_WXmlUtils::getNodeValue($x->firstChild())) && $it->hasNext()) {
												$y = $it->next();
												if($y->getNodeName() === "msqrt" || $y->getNodeName() === "mfrac" || $y->getNodeName() === "mroot") {
													$sb->add("(");
													$sb->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($y));
													$sb->add(")");
												} else {
													$parentheses = false;
													$argument = new StringBuf();
													while($y !== null && com_wiris_util_xml_MathMLUtils::isImplicitArgumentFactor($y)) {
														if($y->getNodeName() === "msup") {
															$parentheses = true;
														}
														$argument->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($y));
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
														$sb->add(com_wiris_util_xml_MathMLUtils::fullMathML2TextImpl($y));
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
	static function removeMrows($elem) {
		if($elem->nodeType != Xml::$Element && $elem->nodeType != Xml::$Document) {
			return;
		}
		$children = $elem->iterator();
		while($children->hasNext()) {
			com_wiris_util_xml_MathMLUtils::removeMrows($children->next());
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
					if($singlechild || _hx_index_of(com_wiris_util_xml_MathMLUtils::$MROWS, $elem->getNodeName(), null) !== -1) {
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
	static function isFunctionName($word) {
		$functionNames = new _hx_array(array("exp", "ln", "log", "sin", "sen", "cos", "tan", "tg", "asin", "arcsin", "asen", "arcsen", "acos", "arccos", "atan", "arctan", "cosec", "csc", "sec", "cotan", "acosec", "acsc", "asec", "acotan", "sinh", "senh", "cosh", "tanh", "asinh", "arcsinh", "asenh", "arcsenh", "acosh", "arccosh", "atanh", "arctanh", "cosech", "csch", "sech", "cotanh", "acosech", "acsch", "asech", "acotanh", "sign"));
		return com_wiris_util_type_Arrays::containsArray($functionNames, $word);
	}
	static function isImplicitArgumentFactor($x) {
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
	static function convertEditor2Newlines($mml) {
		$head = "<mtable columnalign=\"left\" rowspacing=\"0\">";
		$start = null;
		if(($start = _hx_index_of($mml, $head, null)) !== -1) {
			$start += strlen($head);
			$end = _hx_last_index_of($mml, "</mtable>", null);
			$mml = _hx_substr($mml, $start, $end - $start);
			$start = 0;
			$sb = new StringBuf();
			$lines = 0;
			while(($start = _hx_index_of($mml, "<mtd>", $start)) !== -1) {
				if($lines !== 0) {
					$sb->add("<mspace linebreak=\"newline\"/>");
				}
				$end = com_wiris_util_xml_MathMLUtils::endTag($mml, $start);
				$start += 5;
				$end -= 6;
				$sb->add(_hx_substr($mml, $start, $end - $start));
				$start = $end + 6;
				$lines++;
			}
			$mml = $sb->b;
			$mml = com_wiris_util_xml_MathMLUtils::ensureRootTag($mml, "math");
		}
		return $mml;
	}
	static function endTag($xml, $n) {
		$name = com_wiris_util_xml_MathMLUtils::tagName($xml, $n);
		$depth = 1;
		$pos = $n + 1;
		while($depth > 0) {
			$pos = _hx_index_of($xml, "<", $pos);
			if($pos === -1) {
				return strlen($xml);
			} else {
				if(_hx_substr($xml, _hx_index_of($xml, ">", $pos) - 1, 1) === "/") {
				} else {
					if(_hx_substr($xml, $pos + 1, 1) === "/") {
						if(com_wiris_util_xml_MathMLUtils::tagName($xml, $pos + 1) === $name) {
							$depth--;
						}
					} else {
						if(com_wiris_util_xml_MathMLUtils::tagName($xml, $pos) === $name) {
							$depth++;
						}
					}
				}
			}
			$pos = $pos + 1;
		}
		$pos = _hx_index_of($xml, ">", $pos) + 1;
		return $pos;
	}
	static function tagName($xml, $n) {
		$endtag = _hx_index_of($xml, ">", $n);
		$tag = _hx_substr($xml, $n + 1, $endtag - ($n + 1));
		$aux = null;
		if(($aux = _hx_index_of($tag, " ", null)) !== -1) {
			$tag = _hx_substr($tag, 0, $aux);
		}
		return $tag;
	}
	static function ensureRootTag($xml, $tag) {
		$xml = trim($xml);
		if(!StringTools::startsWith($xml, "<" . $tag)) {
			$xml = "<" . $tag . ">" . $xml . "</" . $tag . ">";
		}
		return $xml;
	}
	function __toString() { return 'com.wiris.util.xml.MathMLUtils'; }
}
com_wiris_util_xml_MathMLUtils::$strokesAnnotationEncondings = new _hx_array(array(com_wiris_util_net_MimeTypes::$JSON, com_wiris_util_net_MimeTypes::$HAND_STROKES));
function com_wiris_util_xml_MathMLUtils_0(&$close, &$e, &$i, &$it, &$n, &$open, &$sb, &$separators) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(haxe_Utf8::charCodeAt($separators, com_wiris_util_xml_MathMLUtils_1($close, $e, $i, $it, $n, $open, $s, $sb, $separators)));
		return $s->toString();
	}
}
function com_wiris_util_xml_MathMLUtils_1(&$close, &$e, &$i, &$it, &$n, &$open, &$s, &$sb, &$separators) {
	if($i < $n) {
		return $i;
	} else {
		return $n - 1;
	}
}

<?php

class com_wiris_util_xml_WXmlUtils {
	public function __construct(){}
	static $WHITESPACE_COLLAPSE_REGEX;
	static function getElementContent($element) {
		$sb = new StringBuf();
		if($element->nodeType == Xml::$Document || $element->nodeType == Xml::$Element) {
			$i = $element->iterator();
			while($i->hasNext()) {
				$sb->add($i->next()->toString());
			}
		}
		return $sb->b;
	}
	static function hasSameAttributes($a, $b) {
		if($a === null && $b === null) {
			return true;
		} else {
			if($a === null || $b === null) {
				return false;
			}
		}
		$iteratorA = $a->attributes();
		$iteratorB = $b->attributes();
		while($iteratorA->hasNext()) {
			if(!$iteratorB->hasNext()) {
				return false;
			}
			$iteratorB->next();
			$attr = $iteratorA->next();
			if(!(com_wiris_util_xml_WXmlUtils::getAttribute($a, $attr) === com_wiris_util_xml_WXmlUtils::getAttribute($b, $attr))) {
				return false;
			}
			unset($attr);
		}
		return !$iteratorB->hasNext();
	}
	static function getElementsByAttributeValue($nodeList, $attributeName, $attributeValue) {
		$nodes = new _hx_array(array());
		while($nodeList->hasNext()) {
			$node = $nodeList->next();
			if($node->nodeType == Xml::$Element && $attributeValue === com_wiris_util_xml_WXmlUtils::getAttribute($node, $attributeName)) {
				$nodes->push($node);
			}
			unset($node);
		}
		return $nodes;
	}
	static function getElementsByTagName($nodeList, $tagName) {
		$nodes = new _hx_array(array());
		while($nodeList->hasNext()) {
			$node = $nodeList->next();
			if($node->nodeType == Xml::$Element && $node->getNodeName() === $tagName) {
				$nodes->push($node);
			}
			unset($node);
		}
		return $nodes;
	}
	static function getElements($node) {
		$nodes = new _hx_array(array());
		$nodeList = $node->iterator();
		while($nodeList->hasNext()) {
			$item = $nodeList->next();
			if($item->nodeType == Xml::$Element) {
				$nodes->push($item);
			}
			unset($item);
		}
		return $nodes;
	}
	static function getDocumentElement($doc) {
		$nodeList = $doc->iterator();
		while($nodeList->hasNext()) {
			$node = $nodeList->next();
			if($node->nodeType == Xml::$Element) {
				return $node;
			}
			unset($node);
		}
		return null;
	}
	static function getAttribute($node, $attributeName) {
		$value = $node->get($attributeName);
		if($value === null) {
			return null;
		}
		if(com_wiris_settings_PlatformSettings::$PARSE_XML_ENTITIES) {
			return com_wiris_util_xml_WXmlUtils::htmlUnescape($value);
		}
		return $value;
	}
	static function setAttribute($node, $name, $value) {
		if($value !== null && com_wiris_settings_PlatformSettings::$PARSE_XML_ENTITIES) {
			$value = com_wiris_util_xml_WXmlUtils::htmlEscape($value);
		}
		$node->set($name, $value);
	}
	static function getNodeValue($node) {
		$value = $node->getNodeValue();
		if($value === null) {
			return null;
		}
		if(com_wiris_settings_PlatformSettings::$PARSE_XML_ENTITIES && $node->nodeType == Xml::$PCData) {
			return com_wiris_util_xml_WXmlUtils::htmlUnescape($value);
		}
		return $value;
	}
	static function createPCData($node, $text) {
		if(com_wiris_settings_PlatformSettings::$PARSE_XML_ENTITIES) {
			$text = com_wiris_util_xml_WXmlUtils::htmlEscape($text);
		}
		return Xml::createPCData($text);
	}
	static function escapeXmlEntities($s) {
		$s = str_replace("&", "&amp;", $s);
		$s = str_replace("<", "&lt;", $s);
		$s = str_replace(">", "&gt;", $s);
		$s = str_replace("\"", "&quot;", $s);
		$s = str_replace("'", "&apos;", $s);
		return $s;
	}
	static function htmlEscape($input) {
		$output = str_replace("&", "&amp;", $input);
		$output = str_replace("<", "&lt;", $output);
		$output = str_replace(">", "&gt;", $output);
		$output = str_replace("\"", "&quot;", $output);
		$output = str_replace("&apos;", "'", $output);
		return $output;
	}
	static function htmlUnescape($input) {
		$output = "";
		$start = 0;
		$position = _hx_index_of($input, "&", $start);
		while($position !== -1) {
			$output .= _hx_substr($input, $start, $position - $start);
			if(_hx_char_at($input, $position + 1) === "#") {
				$startPosition = $position + 2;
				$endPosition = _hx_index_of($input, ";", $startPosition);
				if($endPosition !== -1) {
					$number = _hx_substr($input, $startPosition, $endPosition - $startPosition);
					if(StringTools::startsWith($number, "x")) {
						$number = "0" . $number;
					}
					$charCode = Std::parseInt($number);
					$output .= com_wiris_util_xml_WXmlUtils_0($charCode, $endPosition, $input, $number, $output, $position, $start, $startPosition);
					$start = $endPosition + 1;
					unset($number,$charCode);
				} else {
					$output .= "&";
					$start = $position + 1;
				}
				unset($startPosition,$endPosition);
			} else {
				$output .= "&";
				$start = $position + 1;
			}
			$position = _hx_index_of($input, "&", $start);
		}
		$output .= _hx_substr($input, $start, strlen($input) - $start);
		$output = str_replace("&lt;", "<", $output);
		$output = str_replace("&gt;", ">", $output);
		$output = str_replace("&quot;", "\"", $output);
		$output = str_replace("&apos;", "'", $output);
		$output = str_replace("&amp;", "&", $output);
		return $output;
	}
	static $entities = null;
	static function parseXML($xml) {
		$xml = com_wiris_util_xml_WXmlUtils::filterMathMLEntities($xml);
		$x = Xml::parse($xml);
		return $x;
	}
	static function safeParseXML($xml) {
		try {
			return com_wiris_util_xml_WXmlUtils::parseXML($xml);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				return Xml::createDocument();
			}
		}
	}
	static function serializeXML($xml) {
		$s = $xml->toString();
		$s = com_wiris_util_xml_WXmlUtils::filterMathMLEntities($s);
		return $s;
	}
	static function resolveEntities($text) {
		com_wiris_util_xml_WXmlUtils::initEntities();
		$sb = new StringBuf();
		$i = 0;
		$n = strlen($text);
		while($i < $n) {
			$c = com_wiris_util_xml_WXmlUtils::getUtf8Char($text, $i);
			if($c === 60 && $i + 12 < $n && _hx_char_code_at($text, $i + 1) === 33) {
				if(_hx_substr($text, $i, 9) === "<![CDATA[") {
					$e = _hx_index_of($text, "]]>", $i);
					if($e !== -1) {
						$sb->add(_hx_substr($text, $i, $e - $i + 3));
						$i = $e + 3;
						continue;
					}
					unset($e);
				}
			}
			if($c > 127) {
				$special = com_wiris_util_xml_WXmlUtils_1($c, $i, $n, $sb, $text);
				$sb->add($special);
				$i += strlen($special) - 1;
				unset($special);
			} else {
				if($c === 38) {
					$i++;
					$c = _hx_char_code_at($text, $i);
					if(com_wiris_util_xml_WXmlUtils::isNameStart($c)) {
						$name = new StringBuf();
						$name->b .= chr($c);
						$i++;
						$c = _hx_char_code_at($text, $i);
						while(com_wiris_util_xml_WXmlUtils::isNameChar($c)) {
							$name->b .= chr($c);
							$i++;
							$c = _hx_char_code_at($text, $i);
						}
						$ent = $name->b;
						if($c === 59 && com_wiris_util_xml_WXmlUtils::$entities->exists($ent) && !com_wiris_util_xml_WXmlUtils::isXmlEntity($ent)) {
							$val = com_wiris_util_xml_WXmlUtils::$entities->get($ent);
							$sb->add(com_wiris_util_xml_WXmlUtils_2($c, $ent, $i, $n, $name, $sb, $text, $val));
							unset($val);
						} else {
							$sb->add("&");
							$sb->add($name);
							$sb->b .= chr($c);
						}
						unset($name,$ent);
					} else {
						if($c === 35) {
							$i++;
							$c = _hx_char_code_at($text, $i);
							if($c === 120) {
								$hex = new StringBuf();
								$i++;
								$c = _hx_char_code_at($text, $i);
								while(com_wiris_util_xml_WXmlUtils::isHexDigit($c)) {
									$hex->b .= chr($c);
									$i++;
									$c = _hx_char_code_at($text, $i);
								}
								$hent = $hex->b;
								if($c === 59 && !com_wiris_util_xml_WXmlUtils::isXmlEntity("#x" . $hent)) {
									$dec = Std::parseInt("0x" . $hent);
									$sb->add(com_wiris_util_xml_WXmlUtils_3($c, $dec, $hent, $hex, $i, $n, $sb, $text));
									unset($dec);
								} else {
									$sb->add("&#x");
									$sb->add($hent);
									$sb->b .= chr($c);
								}
								unset($hex,$hent);
							} else {
								if(48 <= $c && $c <= 57) {
									$dec = new StringBuf();
									while(48 <= $c && $c <= 57) {
										$dec->b .= chr($c);
										$i++;
										$c = _hx_char_code_at($text, $i);
									}
									if($c === 59 && !com_wiris_util_xml_WXmlUtils::isXmlEntity("#" . Std::string($dec))) {
										$sb->add(com_wiris_util_xml_WXmlUtils_4($c, $dec, $i, $n, $sb, $text));
									} else {
										$sb->add("&#" . $dec->b);
										$sb->b .= chr($c);
									}
									unset($dec);
								}
							}
						}
					}
				} else {
					$sb->b .= chr($c);
				}
			}
			$i++;
			unset($c);
		}
		return $sb->b;
	}
	static function filterMathMLEntities($text) {
		$text = com_wiris_util_xml_WXmlUtils::resolveEntities($text);
		$text = com_wiris_util_xml_WXmlUtils::nonAsciiToEntities($text);
		return $text;
	}
	static function getUtf8Char($text, $i) {
		$c = _hx_char_code_at($text, $i);
		$d = $c;
		if(com_wiris_settings_PlatformSettings::$UTF8_CONVERSION) {
			if($d > 127) {
				$j = 0;
				$c = 128;
				do {
					$c = $c >> 1;
					$j++;
				} while(($d & $c) !== 0);
				$d = $c - 1 & $d;
				while(--$j > 0) {
					$i++;
					$c = _hx_char_code_at($text, $i);
					$d = ($d << 6) + ($c & 63);
				}
			}
		} else {
			if($d >= 55296 && $d <= 56319) {
				$c = _hx_char_code_at($text, $i + 1);
				$d = ($d - 55296 << 10) + ($c - 56320) + 65536;
			}
		}
		return $d;
	}
	static function nonAsciiToEntities($s) {
		$sb = new StringBuf();
		$i = 0;
		$n = strlen($s);
		while($i < $n) {
			$c = com_wiris_util_xml_WXmlUtils::getUtf8Char($s, $i);
			if($c > 127) {
				$hex = com_wiris_common_WInteger::toHex($c, 5);
				$j = 0;
				while($j < strlen($hex)) {
					if(!(_hx_substr($hex, $j, 1) === "0")) {
						$hex = _hx_substr($hex, $j, null);
						break;
					}
					++$j;
				}
				$sb->add("&#x" . $hex . ";");
				$i += strlen((com_wiris_util_xml_WXmlUtils_5($c, $hex, $i, $j, $n, $s, $sb)));
				unset($j,$hex);
			} else {
				$sb->b .= chr($c);
				$i++;
			}
			unset($c);
		}
		return $sb->b;
	}
	static function isNameStart($c) {
		if(65 <= $c && $c <= 90) {
			return true;
		}
		if(97 <= $c && $c <= 122) {
			return true;
		}
		if($c === 95 || $c === 58) {
			return true;
		}
		return false;
	}
	static function isNameChar($c) {
		if(com_wiris_util_xml_WXmlUtils::isNameStart($c)) {
			return true;
		}
		if(48 <= $c && $c <= 57) {
			return true;
		}
		if($c === 46 || $c === 45) {
			return true;
		}
		return false;
	}
	static function isHexDigit($c) {
		if($c >= 48 && $c <= 57) {
			return true;
		}
		if($c >= 65 && $c <= 70) {
			return true;
		}
		if($c >= 97 && $c <= 102) {
			return true;
		}
		return false;
	}
	static function resolveMathMLEntity($name) {
		com_wiris_util_xml_WXmlUtils::initEntities();
		if(com_wiris_util_xml_WXmlUtils::$entities->exists($name)) {
			$code = com_wiris_util_xml_WXmlUtils::$entities->get($name);
			return Std::parseInt($code);
		}
		return -1;
	}
	static function initEntities() {
		if(com_wiris_util_xml_WXmlUtils::$entities === null) {
			$e = com_wiris_util_xml_WEntities::$MATHML_ENTITIES;
			com_wiris_util_xml_WXmlUtils::$entities = new Hash();
			$start = 0;
			$mid = null;
			while(($mid = _hx_index_of($e, "@", $start)) !== -1) {
				$name = _hx_substr($e, $start, $mid - $start);
				$mid++;
				$start = _hx_index_of($e, "@", $mid);
				if($start === -1) {
					break;
				}
				$value = _hx_substr($e, $mid, $start - $mid);
				$num = Std::parseInt("0x" . $value);
				com_wiris_util_xml_WXmlUtils::$entities->set($name, "" . _hx_string_rec($num, ""));
				$start++;
				unset($value,$num,$name);
			}
		}
	}
	static function getText($xml) {
		if($xml->nodeType == Xml::$PCData) {
			return $xml->getNodeValue();
		}
		$r = "";
		$iter = $xml->iterator();
		while($iter->hasNext()) {
			$r .= com_wiris_util_xml_WXmlUtils::getText($iter->next());
		}
		return $r;
	}
	static function getInnerText($xml) {
		if($xml->nodeType == Xml::$PCData || $xml->nodeType == Xml::$CData) {
			return com_wiris_util_xml_WXmlUtils::getNodeValue($xml);
		}
		$r = "";
		$iter = $xml->iterator();
		while($iter->hasNext()) {
			$r .= com_wiris_util_xml_WXmlUtils::getInnerText($iter->next());
		}
		return $r;
	}
	static function setText($xml, $text) {
		if($xml->nodeType != Xml::$Element) {
			return;
		}
		$it = $xml->iterator();
		if($it->hasNext()) {
			$child = $it->next();
			if($child->nodeType == Xml::$PCData) {
				$xml->removeChild($child);
			}
		}
		$xml->addChild(Xml::createPCData($text));
	}
	static function copyXml($elem) {
		return com_wiris_util_xml_WXmlUtils::importXml($elem, $elem);
	}
	static function copyChildren($from, $to) {
		$children = $from->iterator();
		while($children->hasNext()) {
			$child = $children->next();
			$to->addChild(com_wiris_util_xml_WXmlUtils::importXml($child, $to));
			unset($child);
		}
	}
	static function copyElements($from, $to) {
		$it = $from->iterator();
		while($it->hasNext()) {
			$child = $it->next();
			if($child->nodeType == Xml::$Element) {
				$to->addChild(com_wiris_util_xml_WXmlUtils::importXml($child, $to));
			}
			unset($child);
		}
	}
	static function removeChildren($element) {
		while($element->firstChild() !== null) {
			$element->removeChild($element->firstChild());
		}
	}
	static function removeAttributeFromChildren($parent, $attribute) {
		if($parent->nodeType != Xml::$Element) {
			return;
		}
		$it = $parent->iterator();
		while($it->hasNext()) {
			$child = $it->next();
			if($child->nodeType == Xml::$Element && $child->get($attribute) !== null) {
				$child->remove($attribute);
			}
			unset($child);
		}
	}
	static function getChildPosition($parent, $node) {
		$childIndex = 0;
		$it = $parent->iterator();
		while($it->hasNext()) {
			$child = $it->next();
			if($child === $node) {
				return $childIndex;
			}
			++$childIndex;
			unset($child);
		}
		return -1;
	}
	static function getChildElementCount($parent) {
		if($parent->nodeType != Xml::$Element && $parent->nodeType != Xml::$Document) {
			return 0;
		}
		$it = $parent->elements();
		$count = 0;
		while($it->hasNext()) {
			$it->next();
			++$count;
		}
		return $count;
	}
	static function replaceChild($parent, $childToReplace, $replacement) {
		$childIndex = com_wiris_util_xml_WXmlUtils::getChildPosition($parent, $childToReplace);
		if($childIndex !== -1) {
			com_wiris_util_xml_WXmlUtils::replaceIndexSub($parent, $childIndex, $childToReplace, $replacement);
		}
	}
	static function replaceIndexSub($parent, $index, $childToReplace, $replacement) {
		$parent->insertChild($replacement, $index);
		$parent->removeChild($childToReplace);
	}
	static function importXml($elem, $model) {
		$n = null;
		if($elem->nodeType == Xml::$Element) {
			$n = Xml::createElement($elem->getNodeName());
			$keys = $elem->attributes();
			while($keys->hasNext()) {
				$key = $keys->next();
				$n->set($key, $elem->get($key));
				unset($key);
			}
			$children = $elem->iterator();
			while($children->hasNext()) {
				$n->addChild(com_wiris_util_xml_WXmlUtils::importXml($children->next(), $model));
			}
		} else {
			if($elem->nodeType == Xml::$Document) {
				$n = com_wiris_util_xml_WXmlUtils::importXml($elem->firstElement(), $model);
			} else {
				if($elem->nodeType == Xml::$CData) {
					$n = Xml::createCData($elem->getNodeValue());
				} else {
					if($elem->nodeType == Xml::$PCData) {
						$n = Xml::createPCData($elem->getNodeValue());
					} else {
						if($elem->nodeType == Xml::$Comment) {
							$n = Xml::createComment($elem->getNodeValue());
						} else {
							throw new HException("Unsupported node type: " . Std::string($elem->nodeType));
						}
					}
				}
			}
		}
		return $n;
	}
	static function importXmlWithoutChildren($elem, $model) {
		$n = null;
		if($elem->nodeType == Xml::$Element) {
			$n = Xml::createElement($elem->getNodeName());
			$keys = $elem->attributes();
			while($keys->hasNext()) {
				$key = $keys->next();
				$n->set($key, $elem->get($key));
				unset($key);
			}
		} else {
			if($elem->nodeType == Xml::$CData) {
				$n = Xml::createCData($elem->getNodeValue());
			} else {
				if($elem->nodeType == Xml::$PCData) {
					$n = Xml::createPCData($elem->getNodeValue());
				} else {
					throw new HException("Unsupported node type: " . Std::string($elem->nodeType));
				}
			}
		}
		return $n;
	}
	static function copyXmlNamespace($elem, $customNamespace, $prefixAttributes) {
		return com_wiris_util_xml_WXmlUtils::importXmlNamespace($elem, $elem, $customNamespace, $prefixAttributes);
	}
	static function importXmlNamespace($elem, $model, $customNamespace, $prefixAttributes) {
		$n = null;
		if($elem->nodeType == Xml::$Element) {
			$n = Xml::createElement($customNamespace . ":" . $elem->getNodeName());
			$keys = $elem->attributes();
			while($keys->hasNext()) {
				$key = $keys->next();
				$keyNamespaced = $key;
				if($prefixAttributes && _hx_index_of($key, ":", null) === -1 && _hx_index_of($key, "xmlns", null) === -1) {
					$keyNamespaced = $customNamespace . ":" . $key;
				}
				$n->set($keyNamespaced, $elem->get($key));
				unset($keyNamespaced,$key);
			}
			$children = $elem->iterator();
			while($children->hasNext()) {
				$n->addChild(com_wiris_util_xml_WXmlUtils::importXmlNamespace($children->next(), $model, $customNamespace, $prefixAttributes));
			}
		} else {
			if($elem->nodeType == Xml::$Document) {
				$n = com_wiris_util_xml_WXmlUtils::importXmlNamespace($elem->firstElement(), $model, $customNamespace, $prefixAttributes);
			} else {
				if($elem->nodeType == Xml::$CData) {
					$n = Xml::createCData($elem->getNodeValue());
				} else {
					if($elem->nodeType == Xml::$PCData) {
						$n = Xml::createPCData($elem->getNodeValue());
					} else {
						throw new HException("Unsupported node type: " . Std::string($elem->nodeType));
					}
				}
			}
		}
		return $n;
	}
	static function indentXml($xml, $space) {
		$depth = 0;
		$opentag = new EReg("^<([\\w-_]+)[^>]*>\$", "");
		$autotag = new EReg("^<([\\w-_]+)[^>]*/>\$", "");
		$closetag = new EReg("^</([\\w-_]+)>\$", "");
		$cdata = new EReg("^<!\\[CDATA\\[[^\\]]*\\]\\]>\$", "");
		$res = new StringBuf();
		$end = 0;
		$start = null;
		$text = null;
		while($end < strlen($xml) && ($start = _hx_index_of($xml, "<", $end)) !== -1) {
			$text = $start > $end;
			if($text) {
				$res->add(_hx_substr($xml, $end, $start - $end));
			}
			$end = _hx_index_of($xml, ">", $start) + 1;
			$aux = _hx_substr($xml, $start, $end - $start);
			if($autotag->match($aux)) {
				$res->add("\x0A");
				$i = null;
				{
					$_g = 0;
					while($_g < $depth) {
						$i1 = $_g++;
						$res->add($space);
						unset($i1);
					}
					unset($_g);
				}
				$res->add($aux);
				unset($i);
			} else {
				if($opentag->match($aux)) {
					$res->add("\x0A");
					$i = null;
					{
						$_g = 0;
						while($_g < $depth) {
							$i1 = $_g++;
							$res->add($space);
							unset($i1);
						}
						unset($_g);
					}
					$res->add($aux);
					$depth++;
					unset($i);
				} else {
					if($closetag->match($aux)) {
						$depth--;
						if(!$text) {
							$res->add("\x0A");
							$i = null;
							{
								$_g = 0;
								while($_g < $depth) {
									$i1 = $_g++;
									$res->add($space);
									unset($i1);
								}
								unset($_g);
							}
							unset($i);
						}
						$res->add($aux);
					} else {
						if($cdata->match($aux)) {
							$res->add($aux);
						} else {
							haxe_Log::trace("WARNING! malformed XML at character " . _hx_string_rec($end, "") . ":" . $xml, _hx_anonymous(array("fileName" => "WXmlUtils.hx", "lineNumber" => 835, "className" => "com.wiris.util.xml.WXmlUtils", "methodName" => "indentXml")));
							$res->add($aux);
						}
					}
				}
			}
			unset($aux);
		}
		return trim($res->b);
	}
	static function isXmlEntity($ent) {
		if(_hx_char_code_at($ent, 0) === 35) {
			$c = null;
			if(_hx_char_code_at($ent, 1) === 120) {
				$c = Std::parseInt("0x" . _hx_substr($ent, 2, null));
			} else {
				$c = Std::parseInt(_hx_substr($ent, 1, null));
			}
			return $c === 34 || $c === 38 || $c === 39 || $c === 60 || $c === 62;
		} else {
			return $ent === "amp" || $ent === "lt" || $ent === "gt" || $ent === "quot" || $ent === "apos";
		}
	}
	static function getNamespace($element, $prefix) {
		if($element !== null && $element->nodeType == Xml::$Document) {
			$element = $element->firstElement();
		}
		$prefixAttr = com_wiris_util_xml_WXmlUtils_6($element, $prefix);
		return com_wiris_util_xml_WXmlUtils::getNamespaceSearch($element, $prefixAttr);
	}
	static function getNamespaceSearch($element, $attribute) {
		while($element !== null && $element->nodeType == Xml::$Element) {
			$attributeValue = $element->get($attribute);
			if($attributeValue !== null) {
				return $attributeValue;
			}
			$element = $element->_parent;
			unset($attributeValue);
		}
		return null;
	}
	static function normalizeWhitespace($s) {
		return (($s !== null) ? com_wiris_util_xml_WXmlUtils::$WHITESPACE_COLLAPSE_REGEX->replace(trim($s), " ") : null);
	}
	function __toString() { return 'com.wiris.util.xml.WXmlUtils'; }
}
com_wiris_util_xml_WXmlUtils::$WHITESPACE_COLLAPSE_REGEX = new EReg("[ \\t\\n\\r]{2,}", "g");
function com_wiris_util_xml_WXmlUtils_0(&$charCode, &$endPosition, &$input, &$number, &$output, &$position, &$start, &$startPosition) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($charCode);
		return $s->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_1(&$c, &$i, &$n, &$sb, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_2(&$c, &$ent, &$i, &$n, &$name, &$sb, &$text, &$val) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(Std::parseInt($val));
		return $s->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_3(&$c, &$dec, &$hent, &$hex, &$i, &$n, &$sb, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($dec);
		return $s->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_4(&$c, &$dec, &$i, &$n, &$sb, &$text) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(Std::parseInt($dec->b));
		return $s->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_5(&$c, &$hex, &$i, &$j, &$n, &$s, &$sb) {
	{
		$s1 = new haxe_Utf8(null);
		$s1->addChar($c);
		return $s1->toString();
	}
}
function com_wiris_util_xml_WXmlUtils_6(&$element, &$prefix) {
	if($prefix === null) {
		return "xmlns";
	} else {
		return "xmlns:" . $prefix;
	}
}

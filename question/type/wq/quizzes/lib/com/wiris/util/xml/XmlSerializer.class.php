<?php

class com_wiris_util_xml_XmlSerializer {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->tags = new Hash();
		$this->elementStack = new _hx_array(array());
		$this->childrenStack = new _hx_array(array());
		$this->childStack = new _hx_array(array());
		$this->cacheTagStackCount = 0;
		$this->ignoreTagStackCount = 0;
	}}
	public function isIgnoreTag($s) {
		if($this->ignore !== null) {
			$i = $this->ignore->iterator();
			while($i->hasNext()) {
				if($i->next() === $s) {
					return true;
				}
			}
		}
		return false;
	}
	public function setIgnoreTags($ignore) {
		$this->ignore = $ignore;
	}
	public function serializeXml($tag, $elem) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			if($tag === null || $this->currentChild() !== null && $this->currentChild()->getNodeName() === $tag) {
				$elem = $this->currentChild();
				$this->nextChild();
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				if($elem !== null && $this->ignoreTagStackCount === 0) {
					$imported = com_wiris_util_xml_WXmlUtils::importXml($elem, $this->element);
					$this->element->addChild($imported);
				}
			} else {
				if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_REGISTER) {
					$this->beginTag($tag);
				}
			}
		}
		return $elem;
	}
	public function getMainTag($xml) {
		$i = 0;
		$c = null;
		do {
			$i = _hx_index_of($xml, "<", $i);
			$i++;
			$c = _hx_char_code_at($xml, $i);
		} while($c !== 33 && $c !== 63);
		$end = new _hx_array(array(">", " ", "/"));
		$j = null;
		$min = 0;
		{
			$_g1 = 0; $_g = $end->length;
			while($_g1 < $_g) {
				$j1 = $_g1++;
				$n = _hx_index_of($xml, $end[$j1], null);
				if($n !== -1 && $n < $min) {
					$n = $min;
				}
				unset($n,$j1);
			}
		}
		return _hx_substr($xml, $i, $min);
	}
	public function endCache() {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_CACHE) {
			$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_WRITE;
		}
	}
	public function beginCache() {
		if($this->cache && $this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
			$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_CACHE;
		}
	}
	public function setCached($cache) {
		$this->cache = $cache;
	}
	public function childInt($name, $value, $def) {
		return Std::parseInt($this->childString($name, "" . _hx_string_rec($value, ""), "" . _hx_string_rec($def, "")));
	}
	public function childString($name, $value, $def) {
		if(!($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE && ($value === null && $def === null || $value !== null && $value === $def))) {
			if($this->beginTag($name)) {
				$value = $this->textContent($value);
				$this->endTag();
			}
		}
		return $value;
	}
	public function popState() {
		$this->element = $this->elementStack->pop();
		$this->children = $this->childrenStack->pop();
		$this->child = $this->childStack->pop();
	}
	public function pushState() {
		$this->elementStack->push($this->element);
		$this->childrenStack->push($this->children);
		$this->childStack->push($this->child);
	}
	public function currentChild() {
		if($this->child === null && $this->children->hasNext()) {
			$this->child = $this->children->next();
		}
		return $this->child;
	}
	public function nextChild() {
		if($this->children->hasNext()) {
			$this->child = $this->children->next();
		} else {
			$this->child = null;
		}
		return $this->child;
	}
	public function setCurrentElement($element) {
		$this->element = $element;
		$this->children = $this->element->elements();
		$this->child = null;
	}
	public function readNodeModel($model) {
		$node = $model->newInstance();
		$node->onSerialize($this);
		return $node;
	}
	public function readNode() {
		if(!$this->tags->exists($this->currentChild()->getNodeName())) {
			throw new HException("Tag " . $this->currentChild()->getNodeName() . " not registered.");
		}
		$model = $this->tags->get($this->currentChild()->getNodeName());
		return $this->readNodeModel($model);
	}
	public function getTagName($elem) {
		$mode = $this->mode;
		$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_REGISTER;
		$this->currentTag = null;
		$elem->onSerialize($this);
		$this->mode = $mode;
		return $this->currentTag;
	}
	public function register($elem) {
		$this->tags->set($this->getTagName($elem), $elem);
	}
	public function serializeArrayName($array, $tagName) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			if($this->beginTag($tagName)) {
				$array = $this->serializeArray($array, null);
				$this->endTag();
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE && $array !== null && $array->length > 0) {
				$element = $this->element;
				$this->element = Xml::createElement($tagName);
				$element->addChild($this->element);
				$array = $this->serializeArray($array, null);
				$this->element = $element;
			} else {
				if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_REGISTER) {
					$this->beginTag($tagName);
				}
			}
		}
		return $array;
	}
	public function serializeChildName($s, $tagName) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$child = $this->currentChild();
			if($child !== null && $child->getNodeName() === $tagName) {
				$s = $this->serializeChild($s);
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				$s = $this->serializeChild($s);
			}
		}
		return $s;
	}
	public function serializeArray($array, $tagName) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$array = new _hx_array(array());
			$child = $this->currentChild();
			while($child !== null && ($tagName === null || $tagName === $child->getNodeName())) {
				$elem = $this->readNode();
				$array->push($elem);
				$child = $this->currentChild();
				unset($elem);
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE && $array !== null && $array->length > 0) {
				$items = $array->iterator();
				while($items->hasNext()) {
					$items->next()->onSerialize($this);
				}
			}
		}
		return $array;
	}
	public function serializeChild($s) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$child = $this->currentChild();
			if($child !== null) {
				$s = $this->readNode();
			} else {
				$s = null;
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE && $s !== null) {
				$s->onSerialize($this);
			}
		}
		return $s;
	}
	public function floatContent($d) {
		return Std::parseFloat($this->textContent(_hx_string_rec($d, "") . ""));
	}
	public function booleanContent($content) {
		return com_wiris_util_xml_XmlSerializer::parseBoolean($this->textContent(com_wiris_util_xml_XmlSerializer::booleanToString($content)));
	}
	public function rawXml($xml) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			throw new HException("Should not use rawXml() function on read operation!");
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				$raw = Xml::createElement("rawXml");
				$raw->set("id", "" . _hx_string_rec($this->rawxmls->length, ""));
				$this->rawxmls->push($xml);
				$this->element->addChild($raw);
			} else {
				if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_REGISTER) {
					$this->currentTag = $this->getMainTag($xml);
				}
			}
		}
		return $xml;
	}
	public function base64Content($data) {
		$b64 = new haxe_BaseCode(haxe_io_Bytes::ofString("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"));
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$content = $this->textContent(null);
			$data = $b64->decodeBytes(haxe_io_Bytes::ofString($content));
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				$this->textContent($b64->encodeBytes($data)->toString());
			}
		}
		return $data;
	}
	public function textContentImpl($content, $forceCData) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$content = com_wiris_util_xml_XmlSerializer::getXmlTextContent($this->element);
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE && $content !== null && $this->ignoreTagStackCount === 0) {
				$textNode = null;
				if(strlen($content) > 100 || StringTools::startsWith($content, "<") && StringTools::endsWith($content, ">") || $forceCData) {
					$k = _hx_index_of($content, "]]>", null);
					$i = 0;
					while($k > -1) {
						$subcontent = _hx_substr($content, $i, $k - $i + 2);
						$textNode = Xml::createCData($subcontent);
						$this->element->addChild($textNode);
						$i = $k + 2;
						$k = _hx_index_of($content, "]]>", $i);
						unset($subcontent);
					}
					$str = _hx_substr($content, $i, null);
					$textNode = Xml::createCData($str);
					$this->element->addChild($textNode);
				} else {
					$textNode = com_wiris_util_xml_WXmlUtils::createPCData($this->element, $content);
					$this->element->addChild($textNode);
				}
			}
		}
		return $content;
	}
	public function textContent($content) {
		return $this->textContentImpl($content, false);
	}
	public function attributeFloat($name, $value, $def) {
		return Std::parseFloat($this->attributeString($name, "" . _hx_string_rec($value, ""), "" . _hx_string_rec($def, "")));
	}
	public function stringToArray($s) {
		if($s === null) {
			return null;
		}
		$ss = _hx_explode(",", $s);
		$a = new _hx_array(array());
		$i = null;
		{
			$_g1 = 0; $_g = $ss->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$a[$i1] = Std::parseInt($ss[$i1]);
				unset($i1);
			}
		}
		return $a;
	}
	public function stringToArrayString($s) {
		if($s === null) {
			return null;
		}
		return _hx_explode(",", $s);
	}
	public function stringArrayToString($a) {
		if($a === null) {
			return null;
		}
		$i = null;
		$sb = new StringBuf();
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($i1 !== 0) {
					$sb->add(",");
				}
				$sb->add($a[$i1]);
				unset($i1);
			}
		}
		return $sb->b;
	}
	public function arrayToString($a) {
		if($a === null) {
			return null;
		}
		$sb = new StringBuf();
		$i = null;
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($i1 !== 0) {
					$sb->add(",");
				}
				$sb->add(_hx_string_rec($a[$i1], "") . "");
				unset($i1);
			}
		}
		return $sb->b;
	}
	public function attributeStringArray($name, $value, $def) {
		return $this->stringToArrayString($this->attributeString($name, $this->stringArrayToString($value), $this->stringArrayToString($def)));
	}
	public function attributeIntArray($name, $value, $def) {
		return $this->stringToArray($this->attributeString($name, $this->arrayToString($value), $this->arrayToString($def)));
	}
	public function attributeInt($name, $value, $def) {
		return Std::parseInt($this->attributeString($name, "" . _hx_string_rec($value, ""), "" . _hx_string_rec($def, "")));
	}
	public function attributeBoolean($name, $value, $def) {
		return com_wiris_util_xml_XmlSerializer::parseBoolean($this->attributeString($name, com_wiris_util_xml_XmlSerializer::booleanToString($value), com_wiris_util_xml_XmlSerializer::booleanToString($def)));
	}
	public function cacheAttribute($name, $value, $def) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
			if($this->cache) {
				$value = $this->attributeString($name, $value, $def);
				$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_CACHE;
				$this->cacheTagStackCount = 0;
			}
		} else {
			$value = $this->attributeString($name, $value, $def);
		}
		return $value;
	}
	public function attributeString($name, $value, $def) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$value = com_wiris_util_xml_WXmlUtils::getAttribute($this->element, $name);
			if($value === null) {
				$value = $def;
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				if($value !== null && !($value === $def) && $this->ignoreTagStackCount === 0) {
					com_wiris_util_xml_WXmlUtils::setAttribute($this->element, $name, $value);
				}
			}
		}
		return $value;
	}
	public function endTag() {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			$this->element = $this->element->_parent;
			$this->popState();
			$this->nextChild();
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				if($this->ignoreTagStackCount > 0) {
					$this->ignoreTagStackCount--;
				} else {
					$this->element = $this->element->_parent;
				}
			} else {
				if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_CACHE) {
					if($this->cacheTagStackCount > 0) {
						$this->cacheTagStackCount--;
					} else {
						$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_WRITE;
						$this->element = $this->element->_parent;
					}
				}
			}
		}
	}
	public function beginTag($tag) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			if($this->currentChild() !== null && $this->currentChild()->nodeType == Xml::$Element && $tag === $this->currentChild()->getNodeName()) {
				$this->pushState();
				$this->setCurrentElement($this->currentChild());
			} else {
				return false;
			}
		} else {
			if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_WRITE) {
				if($this->isIgnoreTag($tag) || $this->ignoreTagStackCount > 0) {
					$this->ignoreTagStackCount++;
				} else {
					$child = Xml::createElement($tag);
					$this->element->addChild($child);
					$this->element = $child;
				}
			} else {
				if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_REGISTER && $this->currentTag === null) {
					$this->currentTag = $tag;
				} else {
					if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_CACHE) {
						$this->cacheTagStackCount++;
					}
				}
			}
		}
		return true;
	}
	public function beginTagIfBool($tag, $current, $desired) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			if($this->beginTag($tag)) {
				return $desired;
			}
		} else {
			if($current === $desired) {
				$this->beginTag($tag);
			}
		}
		return $current;
	}
	public function beginTagIf($tag, $current, $desired) {
		if($this->mode === com_wiris_util_xml_XmlSerializer::$MODE_READ) {
			if($this->beginTag($tag)) {
				return $desired;
			}
		} else {
			if($current === $desired) {
				$this->beginTag($tag);
			}
		}
		return $current;
	}
	public function write($s) {
		$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_WRITE;
		$this->element = Xml::createDocument();
		$this->rawxmls = new _hx_array(array());
		$s->onSerialize($this);
		$res = $this->element->toString();
		if(StringTools::startsWith($res, "<__document")) {
			$res = _hx_substr($res, _hx_index_of($res, ">", null) + 1, null);
		}
		if(StringTools::endsWith($res, "</__document>")) {
			$res = _hx_substr($res, 0, strlen($res) - strlen("</__document>"));
		}
		$i = null;
		{
			$_g1 = 0; $_g = $this->rawxmls->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$start = _hx_index_of($res, "<rawXml id=\"" . _hx_string_rec($i1, "") . "\"", null);
				if($start !== -1) {
					$end = _hx_index_of($res, ">", $start);
					$res = _hx_substr($res, 0, $start) . $this->rawxmls[$i1] . _hx_substr($res, $end + 1, null);
					unset($end);
				}
				unset($start,$i1);
			}
		}
		return $res;
	}
	public function readXml($xml) {
		$this->setCurrentElement($xml);
		$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_READ;
		return $this->readNode();
	}
	public function read($xml) {
		$document = Xml::parse($xml);
		$this->setCurrentElement($document);
		$this->mode = com_wiris_util_xml_XmlSerializer::$MODE_READ;
		return $this->readNode();
	}
	public function getMode() {
		return $this->mode;
	}
	public $ignoreTagStackCount;
	public $ignore;
	public $cacheTagStackCount;
	public $cache;
	public $currentTag;
	public $rawxmls;
	public $tags;
	public $childStack;
	public $childrenStack;
	public $elementStack;
	public $child;
	public $children;
	public $element;
	public $mode;
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
	static $MODE_READ = 0;
	static $MODE_WRITE = 1;
	static $MODE_REGISTER = 2;
	static $MODE_CACHE = 3;
	static function getXmlTextContent($element) {
		if($element->nodeType == Xml::$CData || $element->nodeType == Xml::$PCData) {
			return com_wiris_util_xml_WXmlUtils::getNodeValue($element);
		} else {
			if($element->nodeType == Xml::$Document || $element->nodeType == Xml::$Element) {
				$sb = new StringBuf();
				$children = $element->iterator();
				while($children->hasNext()) {
					$sb->add(com_wiris_util_xml_XmlSerializer::getXmlTextContent($children->next()));
				}
				return $sb->b;
			} else {
				return "";
			}
		}
	}
	static function parseBoolean($s) {
		return strtolower($s) === "true" || $s === "1";
	}
	static function booleanToString($b) {
		return (($b) ? "true" : "false");
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
	function __toString() { return 'com.wiris.util.xml.XmlSerializer'; }
}

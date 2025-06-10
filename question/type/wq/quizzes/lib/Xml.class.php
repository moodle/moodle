<?php

class Xml {
	public function __construct() {
		;
	}
	public function toString() {
		if($this->nodeType == Xml::$PCData) {
			return htmlspecialchars($this->_nodeValue, ENT_COMPAT, "UTF-8");
		}
		$s = "";
		if($this->nodeType == Xml::$Element) {
			$s .= "<";
			$s .= $this->_nodeName;
			if(null == $this->_attributes) throw new HException('null iterable');
			$»it = $this->_attributes->keys();
			while($»it->hasNext()) {
				$k = $»it->next();
				$s .= " ";
				$s .= $k;
				$s .= "=\"";
				$s .= $this->_attributes->get($k);
				$s .= "\"";
			}
			if($this->_children->length === 0) {
				$s .= "/>";
				return $s;
			}
			$s .= ">";
		} else {
			if($this->nodeType == Xml::$CData) {
				return "<![CDATA[" . $this->_nodeValue . "]]>";
			} else {
				if($this->nodeType == Xml::$Comment) {
					return "<!--" . $this->_nodeValue . "-->";
				} else {
					if($this->nodeType == Xml::$DocType) {
						return "<!DOCTYPE " . $this->_nodeValue . ">";
					} else {
						if($this->nodeType == Xml::$Prolog) {
							return "<?" . $this->_nodeValue . "?>";
						}
					}
				}
			}
		}
		if(null == $this) throw new HException('null iterable');
		$»it = $this->iterator();
		while($»it->hasNext()) {
			$x = $»it->next();
			$s .= $x->toString();
		}
		if($this->nodeType == Xml::$Element) {
			$s .= "</";
			$s .= $this->_nodeName;
			$s .= ">";
		}
		return $s;
	}
	public function insertChild($x, $pos) {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		if($x->_parent !== null) {
			$x->_parent->_children->remove($x);
		}
		$x->_parent = $this;
		$this->_children->insert($pos, $x);
	}
	public function removeChild($x) {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		$b = $this->_children->remove($x);
		if($b) {
			$x->_parent = null;
		}
		return $b;
	}
	public function addChild($x) {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		if($x->_parent !== null) {
			$x->_parent->_children->remove($x);
		}
		$x->_parent = $this;
		$this->_children->push($x);
	}
	public function firstElement() {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		{
			$_g = 0; $_g1 = $this->_children;
			while($_g < $_g1->length) {
				$child = $_g1[$_g];
				++$_g;
				if($child->nodeType == Xml::$Element) {
					return $child;
				}
				unset($child);
			}
		}
		return null;
	}
	public function firstChild() {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		if($this->_children->length === 0) {
			return null;
		}
		return $this->_children[0];
	}
	public function elementsNamed($name) {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		return Lambda::filter($this->_children, array(new _hx_lambda(array(&$name), "Xml_0"), 'execute'))->iterator();
	}
	public function elements() {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		return Lambda::filter($this->_children, array(new _hx_lambda(array(), "Xml_1"), 'execute'))->iterator();
	}
	public function iterator() {
		if($this->_children === null) {
			throw new HException("bad nodetype");
		}
		return $this->_children->iterator();
	}
	public function attributes() {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		return $this->_attributes->keys();
	}
	public function exists($att) {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		return $this->_attributes->exists($att);
	}
	public function remove($att) {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		$this->_attributes->remove($att);
	}
	public function set($att, $value) {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		$this->_attributes->set($att, $value);
	}
	public function get($att) {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		return $this->_attributes->get($att);
	}
	public function getParent() {
		return $this->_parent;
	}
	public function setNodeValue($v) {
		if($this->nodeType == Xml::$Element || $this->nodeType == Xml::$Document) {
			throw new HException("bad nodeType");
		}
		return $this->_nodeValue = $v;
	}
	public function getNodeValue() {
		if($this->nodeType == Xml::$Element || $this->nodeType == Xml::$Document) {
			throw new HException("bad nodeType");
		}
		return $this->_nodeValue;
	}
	public function setNodeName($n) {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		return $this->_nodeName = $n;
	}
	public function getNodeName() {
		if($this->nodeType != Xml::$Element) {
			throw new HException("bad nodeType");
		}
		return $this->_nodeName;
	}
	public $_parent;
	public $_children;
	public $_attributes;
	public $_nodeValue;
	public $_nodeName;
	public $parent;
	public $nodeValue;
	public $nodeName;
	public $nodeType;
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
	static $Element;
	static $PCData;
	static $CData;
	static $Comment;
	static $DocType;
	static $Prolog;
	static $Document;
	static $build;
	static $_buffer = "";
	static function __saveText() {
		if(strlen(Xml::$_buffer) > 0) {
			Xml::$build->addChild(Xml::createPCData(Xml::$_buffer));
			Xml::$_buffer = "";
		}
	}
	static function __start_element_handler($parser, $name, $attribs) {
		if(strlen(Xml::$_buffer) > 0) {
			Xml::$build->addChild(Xml::createPCData(Xml::$_buffer));
			Xml::$_buffer = "";
		}
		$node = Xml::createElement($name);
		foreach($attribs as $k => $v) $node->set($k, $v);
		Xml::$build->addChild($node);
		Xml::$build = $node;
	}
	static function __end_element_handler($parser, $name) {
		if(strlen(Xml::$_buffer) > 0) {
			Xml::$build->addChild(Xml::createPCData(Xml::$_buffer));
			Xml::$_buffer = "";
		}
		Xml::$build = Xml::$build->_parent;
	}
	static function __character_data_handler($parser, $data) {
		Xml::$_buffer .= $data;
	}
	static function __default_handler($parser, $data) {
		if($data === "<![CDATA[") {
			return;
		}
		if($data === "]]>") {
			return;
		}
		if("<!--" === _hx_substr($data, 0, 4)) {
			Xml::$build->addChild(Xml::createComment(_hx_substr($data, 4, strlen($data) - 7)));
		} else {
			Xml::$build->addChild(Xml::createPCData($data));
		}
	}
	static $reHeader;
	static function parse($str) {
		Xml::$build = Xml::createDocument();
		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, (isset(Xml::$__start_element_handler) ? Xml::$__start_element_handler: array("Xml", "__start_element_handler")), (isset(Xml::$__end_element_handler) ? Xml::$__end_element_handler: array("Xml", "__end_element_handler")));
		xml_set_character_data_handler($xml_parser, (isset(Xml::$__character_data_handler) ? Xml::$__character_data_handler: array("Xml", "__character_data_handler")));
		xml_set_default_handler($xml_parser, (isset(Xml::$__default_handler) ? Xml::$__default_handler: array("Xml", "__default_handler")));
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 0);
		Xml::$reHeader->match($str);
		$str = "<doc>" . Xml::$reHeader->matchedRight() . "</doc>";
		if(1 !== xml_parse($xml_parser, $str, true)) {
			throw new HException("Xml parse error (" . (xml_error_string(xml_get_error_code($xml_parser)) . ") line #" . xml_get_current_line_number($xml_parser)));
		}
		xml_parser_free($xml_parser);
		Xml::$build = Xml::$build->_children[0];
		Xml::$build->_parent = null;
		Xml::$build->_nodeName = null;
		Xml::$build->nodeType = Xml::$Document;
		$doctype = Xml::$reHeader->matched(2);
		if(null !== $doctype) {
			Xml::$build->insertChild(Xml::createDocType($doctype), 0);
		}
		$prolog = Xml::$reHeader->matched(1);
		if(null !== $prolog) {
			Xml::$build->insertChild(Xml::createProlog($prolog), 0);
		}
		return Xml::$build;
	}
	static function createElement($name) {
		$r = new Xml();
		$r->nodeType = Xml::$Element;
		$r->_children = new _hx_array(array());
		$r->_attributes = new Hash();
		$r->setNodeName($name);
		return $r;
	}
	static function createPCData($data) {
		$r = new Xml();
		$r->nodeType = Xml::$PCData;
		$r->setNodeValue($data);
		return $r;
	}
	static function createCData($data) {
		$r = new Xml();
		$r->nodeType = Xml::$CData;
		$r->setNodeValue($data);
		return $r;
	}
	static function createComment($data) {
		$r = new Xml();
		$r->nodeType = Xml::$Comment;
		$r->setNodeValue($data);
		return $r;
	}
	static function createDocType($data) {
		$r = new Xml();
		$r->nodeType = Xml::$DocType;
		$r->setNodeValue($data);
		return $r;
	}
	static function createProlog($data) {
		$r = new Xml();
		$r->nodeType = Xml::$Prolog;
		$r->setNodeValue($data);
		return $r;
	}
	static function createDocument() {
		$r = new Xml();
		$r->nodeType = Xml::$Document;
		$r->_children = new _hx_array(array());
		return $r;
	}
	static $__properties__ = array("set_nodeName" => "setNodeName","get_nodeName" => "getNodeName","set_nodeValue" => "setNodeValue","get_nodeValue" => "getNodeValue","get_parent" => "getParent");
	function __toString() { return $this->toString(); }
}
{
	Xml::$Element = "element";
	Xml::$PCData = "pcdata";
	Xml::$CData = "cdata";
	Xml::$Comment = "comment";
	Xml::$DocType = "doctype";
	Xml::$Prolog = "prolog";
	Xml::$Document = "document";
}
Xml::$reHeader = new EReg("\\s*(?:<\\?(.+?)\\?>)?(?:<!DOCTYPE ([^>]+)>)?", "mi");
function Xml_0(&$name, $child) {
	{
		return $child->nodeType == Xml::$Element && $child->getNodeName() === $name;
	}
}
function Xml_1($child) {
	{
		return $child->nodeType == Xml::$Element;
	}
}

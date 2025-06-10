<?php

class com_wiris_quizzes_impl_MathContent extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_MathContent();
	}
	public function onSerializeInner($s) {
		$this->type = $s->attributeString("type", $this->type, "text");
		$this->content = $s->textContent($this->content);
	}
	public function onSerialize($s) {
		$s->beginTag("math");
		$this->onSerializeInner($s);
		$s->endTag();
	}
	public function set($content) {
		$this->type = com_wiris_quizzes_impl_MathContent::getMathType($content);
		$this->content = $content;
	}
	public $content;
	public $type;
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
	static $TYPE_TEXT = "text";
	static $TYPE_TEXT_EVAL = "textEval";
	static $TYPE_MATHML = "mathml";
	static $TYPE_MATHML_EVAL = "mathmlEval";
	static $TYPE_IMAGE = "image";
	static $TYPE_IMAGE_REF = "imageref";
	static $TYPE_STRING = "string";
	static $TYPE_GEOMETRY_FILE = "construction";
	static function getMathType($content) {
		if($content === null || $content === "") {
			return com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
		}
		$content = trim($content);
		$i = null;
		if(StringTools::startsWith($content, "<") && StringTools::endsWith($content, ">")) {
			$mathmltags = new _hx_array(array("math", "mn", "mo", "mi", "mrow", "mfrac", "mtext", "ms", "mroot", "msqrt", "mfenced", "msub", "msup", "msubsup", "mover", "munder", "munderover"));
			{
				$_g1 = 0; $_g = $mathmltags->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(StringTools::startsWith($content, "<" . $mathmltags[$i1])) {
						return com_wiris_quizzes_impl_MathContent::$TYPE_MATHML;
					}
					unset($i1);
				}
			}
		}
		if(com_wiris_util_geometry_GeometryFile::isGeometryFile($content)) {
			return com_wiris_quizzes_impl_MathContent::$TYPE_GEOMETRY_FILE;
		}
		return com_wiris_quizzes_impl_MathContent::$TYPE_TEXT;
	}
	static function isEmpty($content) {
		$content = trim($content);
		if(StringTools::startsWith($content, "<math")) {
			$content = _hx_substr($content, _hx_index_of($content, ">", null) + 1, null);
			if(strlen($content) > 0) {
				$content = _hx_substr($content, 0, _hx_last_index_of($content, "<", null));
			}
		}
		while(StringTools::startsWith($content, "<mrow")) {
			$content = _hx_substr($content, _hx_index_of($content, ">", null) + 1, null);
			$content = _hx_substr($content, 0, _hx_last_index_of($content, "<", null));
		}
		return strlen($content) === 0;
	}
	function __toString() { return 'com.wiris.quizzes.impl.MathContent'; }
}

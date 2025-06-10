<?php

class com_wiris_quizzes_impl_ResultErrorLocation extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->fromline = -1;
		$this->toline = -1;
		$this->fromcolumn = -1;
		$this->tocolumn = -1;
	}}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ResultErrorLocation::$tagName);
		$this->element = $s->attributeString("element", $this->element, null);
		$this->elementid = $s->attributeString("ref", $this->elementid, null);
		$this->fromline = $s->attributeInt("fromline", $this->fromline, -1);
		$this->toline = $s->attributeInt("toline", $this->toline, -1);
		$this->fromcolumn = $s->attributeInt("fromcolumn", $this->fromcolumn, -1);
		$this->tocolumn = $s->attributeInt("tocolumn", $this->tocolumn, -1);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ResultErrorLocation();
	}
	public $tocolumn;
	public $fromcolumn;
	public $toline;
	public $fromline;
	public $elementid;
	public $element;
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
	static $tagName = "location";
	function __toString() { return 'com.wiris.quizzes.impl.ResultErrorLocation'; }
}

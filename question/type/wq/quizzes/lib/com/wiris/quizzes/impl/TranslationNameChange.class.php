<?php

class com_wiris_quizzes_impl_TranslationNameChange extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_TranslationNameChange();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_TranslationNameChange::$tagName);
		$this->newname = $s->attributeString("new", $this->newname, null);
		$this->oldname = $s->attributeString("old", $this->oldname, null);
		$s->endTag();
	}
	public $newname;
	public $oldname;
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
	static $tagName = "nameChange";
	function __toString() { return 'com.wiris.quizzes.impl.TranslationNameChange'; }
}

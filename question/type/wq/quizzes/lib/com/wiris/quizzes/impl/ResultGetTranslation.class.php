<?php

class com_wiris_quizzes_impl_ResultGetTranslation extends com_wiris_quizzes_impl_Result {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function newInstance() {
		return new com_wiris_quizzes_impl_ResultGetTranslation();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_ResultGetTranslation::$tagName);
		$this->onSerializeInner($s);
		$this->wirisCasSession = $s->childString("wirisCasSession", $this->wirisCasSession, null);
		$this->namechanges = $s->serializeArray($this->namechanges, com_wiris_quizzes_impl_TranslationNameChange::$tagName);
		$s->endTag();
	}
	public $namechanges;
	public $wirisCasSession;
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
	static $tagName = "getTranslationResult";
	function __toString() { return 'com.wiris.quizzes.impl.ResultGetTranslation'; }
}

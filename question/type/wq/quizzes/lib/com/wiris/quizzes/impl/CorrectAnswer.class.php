<?php

class com_wiris_quizzes_impl_CorrectAnswer extends com_wiris_quizzes_impl_MathContent {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->weight = 1.0;
		$this->id = "0";
	}}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_CorrectAnswer::$TAGNAME);
		$this->id = $s->attributeString("id", $this->id, "0");
		$this->weight = $s->attributeFloat("weight", $this->weight, 1.0);
		$this->reference = $s->attributeString("ref", $this->reference, null);
		parent::onSerializeInner($s);
		$s->endTag();
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_CorrectAnswer();
	}
	public $reference;
	public $id;
	public $weight;
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
	static $TAGNAME = "correctAnswer";
	function __toString() { return 'com.wiris.quizzes.impl.CorrectAnswer'; }
}

<?php

class com_wiris_quizzes_impl_QuestionRequestImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_quizzes_api_QuestionRequest{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function isEmpty() {
		return $this->processes === null || $this->processes->length === 0;
	}
	public function addMetaProperty($name, $value) {
		if($this->meta === null) {
			$this->meta = new _hx_array(array());
		}
		$p = new com_wiris_quizzes_impl_Property();
		$p->name = $name;
		$p->value = $value;
		$this->meta->push($p);
	}
	public function addProcess($p) {
		if($this->processes === null) {
			$this->processes = new _hx_array(array());
		}
		if(Std::is($p, _hx_qtype("com.wiris.quizzes.impl.ProcessGetVariables"))) {
			$this->processes->insert(0, $p);
		} else {
			$this->processes->push($p);
		}
	}
	public function variables($names, $type) {
		$p = new com_wiris_quizzes_impl_ProcessGetVariables();
		$sb = new StringBuf();
		$i = null;
		{
			$_g1 = 0; $_g = $names->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if($i1 !== 0) {
					$sb->add(",");
				}
				$sb->add($names[$i1]);
				unset($i1);
			}
		}
		$p->names = $sb->b;
		$p->type = $type;
		$this->addProcess($p);
	}
	public function checkAssertions() {
		$p = new com_wiris_quizzes_impl_ProcessGetCheckAssertions();
		$this->addProcess($p);
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_QuestionRequestImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_QuestionRequestImpl::$tagName);
		$this->question = $s->serializeChildName($this->question, com_wiris_quizzes_impl_QuestionImpl::$TAGNAME);
		$this->userData = $s->serializeChildName($this->userData, com_wiris_quizzes_impl_UserData::$TAGNAME);
		$this->processes = $s->serializeArrayName($this->processes, "processes");
		$this->meta = $s->serializeArrayName($this->meta, "meta");
		$s->endTag();
	}
	public $meta;
	public $processes;
	public $userData;
	public $question;
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
	static $tagName = "processQuestion";
	function __toString() { return 'com.wiris.quizzes.impl.QuestionRequestImpl'; }
}

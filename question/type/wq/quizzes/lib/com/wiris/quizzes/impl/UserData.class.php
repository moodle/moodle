<?php

class com_wiris_quizzes_impl_UserData extends com_wiris_util_xml_SerializableImpl {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
		$this->randomSeed = -1;
	}}
	public function setParameter($name, $value) {
		if(!_hx_deref(new com_wiris_quizzes_impl_HTMLTools())->isQuizzesIdentifier($name)) {
			throw new HException("Invalid parameter \"name\".");
		}
		if($this->parameters === null) {
			$this->parameters = new _hx_array(array());
		}
		$i = null;
		{
			$_g1 = 0; $_g = $this->parameters->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(_hx_array_get($this->parameters, $i1)->name === $name) {
					$this->parameters->remove($this->parameters[$i1]);
				}
				unset($i1);
			}
		}
		if($value !== null && !($value === "")) {
			$p = new com_wiris_quizzes_impl_Parameter();
			$p->name = $name;
			$p->set($value);
			$this->parameters->push($p);
		}
	}
	public function ensureAnswerPlace($index) {
		if($index < 0) {
			throw new HException("Invalid index: " . _hx_string_rec($index, ""));
		}
		if($this->answers === null) {
			$this->answers = new _hx_array(array());
		}
		while($this->answers->length <= $index) {
			$this->answers->push(new com_wiris_quizzes_impl_Answer());
		}
	}
	public function getUserCompoundAnswer($index, $compoundindex) {
		$a = $this->answers[$index];
		$compound = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($a);
		return $compound[$compoundindex][1];
	}
	public function setUserCompoundAnswer($index, $compoundindex, $content) {
		$this->ensureAnswerPlace($index);
		if($compoundindex < 0) {
			throw new HException("Invalid compound index: " . _hx_string_rec($compoundindex, ""));
		}
		$a = $this->answers[$index];
		$a->id = "" . _hx_string_rec($index, "");
		$compound = null;
		if($a->content === null || strlen($a->content) === 0) {
			$compound = new _hx_array(array());
		} else {
			$compound = com_wiris_quizzes_impl_CompoundAnswerParser::parseCompoundAnswer($a);
		}
		$i = $compound->length;
		while($i <= $compoundindex) {
			$compound[$i] = new _hx_array(array("<math><mo>=</mo></math>", "<math></math>"));
			$i++;
		}
		if(com_wiris_quizzes_impl_MathContent::getMathType($content) === com_wiris_quizzes_impl_MathContent::$TYPE_TEXT) {
			$content = _hx_deref(new com_wiris_quizzes_impl_HTMLTools())->textToMathML($content);
		}
		$compound[$compoundindex][1] = $content;
		$m = com_wiris_quizzes_impl_CompoundAnswerParser::joinCompoundAnswer($compound);
		$a->type = $m->type;
		$a->content = $m->content;
	}
	public function setUserAnswer($index, $content) {
		$this->ensureAnswerPlace($index);
		$a = $this->answers[$index];
		$a->id = "" . _hx_string_rec($index, "");
		$a->set($content);
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_UserData();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_UserData::$TAGNAME);
		$this->randomSeed = $s->childInt("randomSeed", $this->randomSeed, -1);
		$this->answers = $s->serializeArrayName($this->answers, "answers");
		$this->parameters = $s->serializeArrayName($this->parameters, "parameters");
		$s->endTag();
	}
	public $parameters;
	public $answers;
	public $randomSeed;
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
	static $TAGNAME = "userData";
	function __toString() { return 'com.wiris.quizzes.impl.UserData'; }
}

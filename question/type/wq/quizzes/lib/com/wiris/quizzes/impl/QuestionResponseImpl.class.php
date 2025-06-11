<?php

class com_wiris_quizzes_impl_QuestionResponseImpl extends com_wiris_util_xml_SerializableImpl implements com_wiris_quizzes_api_QuestionResponse{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function removePrefix($prefix, $variablesWithPrefix) {
		$prefixLen = strlen($prefix);
		{
			$_g = 0; $_g1 = $this->results;
			while($_g < $_g1->length) {
				$r = $_g1[$_g];
				++$_g;
				if(Std::is($r, _hx_qtype("com.wiris.quizzes.impl.ResultGetVariables"))) {
					$rr = $r;
					$variables = $rr->variables;
					{
						$_g2 = 0;
						while($_g2 < $variables->length) {
							$v = $variables[$_g2];
							++$_g2;
							if(StringTools::startsWith($v->name, $prefix) && com_wiris_util_type_Arrays::containsArray($variablesWithPrefix, _hx_substr($v->name, $prefixLen, null))) {
								$v->name = _hx_substr($v->name, $prefixLen, null);
							}
							unset($v);
						}
						unset($_g2);
					}
					unset($variables,$rr);
				}
				unset($r);
			}
		}
	}
	public function newInstance() {
		return new com_wiris_quizzes_impl_QuestionResponseImpl();
	}
	public function onSerialize($s) {
		$s->beginTag(com_wiris_quizzes_impl_QuestionResponseImpl::$tagName);
		$this->results = $s->serializeArray($this->results, null);
		$s->endTag();
	}
	public $results;
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
	static $tagName = "processQuestionResult";
	function __toString() { return 'com.wiris.quizzes.impl.QuestionResponseImpl'; }
}

<?php

class com_wiris_quizzes_impl_QuizzesTranslator {
	public function __construct($lang, $source) {
		if(!php_Boot::$skip_constructor) {
		$this->lang = $lang;
		$this->strings = new Hash();
		$i = 0;
		while($i < $source->length && !($source[$i][0] === "lang" && $source[$i][1] === $lang)) {
			$i++;
		}
		while($i < $source->length && !($source[$i][0] === "lang" && !($source[$i][1] === $lang))) {
			$this->strings->set($source[$i][0], $source[$i][1]);
			$i++;
		}
	}}
	public function t($code) {
		if($this->strings->exists($code)) {
			$code = $this->strings->get($code);
		} else {
			if(!($this->lang === "en")) {
				$code = com_wiris_quizzes_impl_QuizzesTranslator::getInstance("en")->t($code);
			}
		}
		return $code;
	}
	public $lang;
	public $strings;
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
	static $languages = null;
	static $available = null;
	static function getInstance($lang) {
		if(com_wiris_quizzes_impl_QuizzesTranslator::$languages === null) {
			com_wiris_quizzes_impl_QuizzesTranslator::$languages = new Hash();
		}
		$lang = com_wiris_quizzes_impl_QuizzesTranslator::getBestMatch($lang);
		if($lang === null) {
			throw new HException("No languages defined.");
		}
		if(!com_wiris_quizzes_impl_QuizzesTranslator::$languages->exists($lang)) {
			$translator = new com_wiris_quizzes_impl_QuizzesTranslator($lang, com_wiris_quizzes_impl_Strings::$lang);
			com_wiris_quizzes_impl_QuizzesTranslator::$languages->set($lang, $translator);
		}
		return com_wiris_quizzes_impl_QuizzesTranslator::$languages->get($lang);
	}
	static function getBestMatch($lang) {
		$a = com_wiris_quizzes_impl_QuizzesTranslator::getAvailableLanguages();
		if(com_wiris_util_type_Arrays::contains($a, $lang)) {
			return $lang;
		}
		$i = null;
		if(($i = _hx_index_of($lang, "_", null)) !== -1) {
			$lang = _hx_substr($lang, 0, $i);
			if(com_wiris_util_type_Arrays::contains($a, $lang)) {
				return $lang;
			}
		}
		{
			$_g1 = 0; $_g = $a->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				if(StringTools::startsWith($a[$i1], $lang . "_")) {
					return $a[$i1];
				}
				unset($i1);
			}
		}
		if(com_wiris_util_type_Arrays::contains($a, "en")) {
			return "en";
		}
		if($a->length > 0) {
			return $a[0];
		}
		return null;
	}
	static function getAvailableLanguages() {
		if(com_wiris_quizzes_impl_QuizzesTranslator::$available === null) {
			com_wiris_quizzes_impl_QuizzesTranslator::$available = new _hx_array(array());
			$i = null;
			{
				$_g1 = 0; $_g = com_wiris_quizzes_impl_Strings::$lang->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(com_wiris_quizzes_impl_Strings::$lang[$i1][0] === "lang") {
						com_wiris_quizzes_impl_QuizzesTranslator::$available->push(com_wiris_quizzes_impl_Strings::$lang[$i1][1]);
					}
					unset($i1);
				}
			}
		}
		return com_wiris_quizzes_impl_QuizzesTranslator::$available;
	}
	function __toString() { return 'com.wiris.quizzes.impl.QuizzesTranslator'; }
}

<?php

class com_wiris_util_json_parser_ExtractedString {
	public function __construct($sourceEnd, $str) {
		if(!php_Boot::$skip_constructor) {
		$this->sourceEnd = $sourceEnd;
		$this->str = $str;
	}}
	public $str;
	public $sourceEnd;
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
	function __toString() { return 'com.wiris.util.json.parser.ExtractedString'; }
}

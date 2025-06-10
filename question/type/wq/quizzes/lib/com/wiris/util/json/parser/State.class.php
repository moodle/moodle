<?php

class com_wiris_util_json_parser_State {
	public function __construct($propertyName, $container, $type) {
		if(!php_Boot::$skip_constructor) {
		$this->propertyName = $propertyName;
		$this->container = $container;
		$this->type = $type;
	}}
	public $type;
	public $container;
	public $propertyName;
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
	function __toString() { return 'com.wiris.util.json.parser.State'; }
}

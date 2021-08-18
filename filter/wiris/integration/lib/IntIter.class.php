<?php

class IntIter {
	public function __construct($min, $max) {
		if(!php_Boot::$skip_constructor) {
		$this->min = $min;
		$this->max = $max;
	}}
	public function next() {
		return $this->min++;
	}
	public function hasNext() {
		return $this->min < $this->max;
	}
	public $max;
	public $min;
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
	function __toString() { return 'IntIter'; }
}

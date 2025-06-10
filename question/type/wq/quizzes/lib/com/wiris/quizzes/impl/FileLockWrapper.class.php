<?php

class com_wiris_quizzes_impl_FileLockWrapper implements com_wiris_util_sys_Lock{
	public function __construct($fl) {
		if(!php_Boot::$skip_constructor) {
		$this->fl = $fl;
	}}
	public function release() {
		$this->fl->release();
	}
	public $fl;
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
	function __toString() { return 'com.wiris.quizzes.impl.FileLockWrapper'; }
}

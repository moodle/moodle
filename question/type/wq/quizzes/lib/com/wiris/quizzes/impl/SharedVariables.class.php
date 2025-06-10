<?php

class com_wiris_quizzes_impl_SharedVariables {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->cache = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getVariablesCache();
		$this->locker = com_wiris_quizzes_impl_QuizzesImpl::getInstance()->getLockProvider();
	}}
	public function getCacheKey($name) {
		return $name . ".var";
	}
	public function unlockVariable($name) {
		if(com_wiris_quizzes_impl_SharedVariables::$h !== null) {
			$l = com_wiris_quizzes_impl_SharedVariables::$h->get($name);
			if($l !== null) {
				com_wiris_quizzes_impl_SharedVariables::$h->remove($name);
				$l->release();
			}
		}
	}
	public function lockVariable($name) {
		$l = $this->locker->getLock($this->getCacheKey($name));
		if(com_wiris_quizzes_impl_SharedVariables::$h === null) {
			com_wiris_quizzes_impl_SharedVariables::$h = new Hash();
		}
		com_wiris_quizzes_impl_SharedVariables::$h->set($name, $l);
	}
	public function setVariable($name, $value) {
		$b = haxe_io_Bytes::ofData(com_wiris_system_Utf8::toBytes($value));
		$this->cache->set($this->getCacheKey($name), $b);
	}
	public function getVariable($name) {
		$b = $this->cache->get($this->getCacheKey($name));
		return (($b !== null) ? com_wiris_system_Utf8::fromBytes($b->b) : null);
	}
	public $locker;
	public $cache;
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
	static $h = null;
	function __toString() { return 'com.wiris.quizzes.impl.SharedVariables'; }
}

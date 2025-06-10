<?php

class com_wiris_quizzes_impl_FileLockProvider implements com_wiris_util_sys_LockProvider{
	public function __construct($basedir) {
		if(!php_Boot::$skip_constructor) {
		$this->basedir = com_wiris_system_Storage::newStorage($basedir);
	}}
	public function getLock($id) {
		$filename = com_wiris_system_Storage::newStorageWithParent($this->basedir, $id)->toString();
		return new com_wiris_quizzes_impl_FileLockWrapper(com_wiris_system_FileLock::getLock($filename, com_wiris_quizzes_impl_FileLockProvider::$WAIT, com_wiris_quizzes_impl_FileLockProvider::$TIMEOUT));
	}
	public $basedir;
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
	static $TIMEOUT = 5000;
	static $WAIT = 100;
	function __toString() { return 'com.wiris.quizzes.impl.FileLockProvider'; }
}

<?php

class com_wiris_system_FileLock {
	public function __construct($filename) {
		if(!php_Boot::$skip_constructor) {
		$this->filename = $filename;
	}}
	public function release() {
		try {
			@unlink($this->filename);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
			}
		}
	}
	public $filename;
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
	static function getLock($file, $wait, $remaining) {
		$startwait = haxe_Timer::stamp();
		try {
			$lockfile = $file . ".lock";
			try {
				$ft = @fileatime($lockfile);
				if(($ft !== FALSE) && ($ft + 10 < time())) {
					@unlink($lockfile);
				}
			}catch(Exception $»e) {
				$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
				$e = $_ex_;
				{
				}
			}
			$lh = @fopen($lockfile, "x");
			if(_hx_equal($lh, false)) {
				throw new HException("Could not acquire lock to file " . $file);
			}
			@fclose($lh);
			return new com_wiris_system_FileLock($lockfile);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$e = $_ex_;
			{
				if($remaining < 0) {
					throw new HException($e);
				}
				usleep(rand(1,200));
				$actualwait = (haxe_Timer::stamp() - $startwait) * 1000;
				return com_wiris_system_FileLock::getLock($file, $wait, $remaining - $actualwait);
			}
		}
		return null;
	}
	function __toString() { return 'com.wiris.system.FileLock'; }
}

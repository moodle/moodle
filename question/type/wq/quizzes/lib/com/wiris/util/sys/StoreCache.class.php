<?php

class com_wiris_util_sys_StoreCache implements com_wiris_util_sys_Cache{
	public function __construct($cachedir) {
		if(!php_Boot::$skip_constructor) {
		$this->cachedir = com_wiris_system_Storage::newStorage($cachedir);
		if(!$this->cachedir->exists()) {
			$this->cachedir->mkdirs();
		}
		if(!$this->cachedir->exists()) {
			throw new HException("Variable folder \"" . $this->cachedir->toString() . "\" does not exist and can't be automatically created. Please create it with write permissions.");
		}
	}}
	public function getItemStore($key) {
		return com_wiris_system_Storage::newStorageWithParent($this->cachedir, $key);
	}
	public function delete($key) {
		$this->getItemStore($key)->delete();
	}
	public function deleteStorageDir($s) {
		if($s->exists() && $s->isDirectory()) {
			$files = $s->hlist();
			$i = null;
			{
				$_g1 = 0; $_g = $files->length;
				while($_g1 < $_g) {
					$i1 = $_g1++;
					if(!($files[$i1] === "." || $files[$i1] === "..")) {
						$f = com_wiris_system_Storage::newStorageWithParent($s, $files[$i1]);
						if($f->isDirectory()) {
							$this->deleteStorageDir($f);
						}
						$f->delete();
						unset($f);
					}
					unset($i1);
				}
			}
		}
	}
	public function deleteAll() {
		$this->deleteStorageDir($this->cachedir);
	}
	public function get($key) {
		$s = $this->getItemStore($key);
		if($s->exists()) {
			try {
				return haxe_io_Bytes::ofData($s->readBinary());
			}catch(Exception $»e) {
				$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
				$t = $_ex_;
				{
					haxe_Log::trace("Unable to read cache file \"" . $s->toString() . "\".", _hx_anonymous(array("fileName" => "StoreCache.hx", "lineNumber" => 43, "className" => "com.wiris.util.sys.StoreCache", "methodName" => "get")));
					return null;
				}
			}
		} else {
			return null;
		}
	}
	public function set($key, $value) {
		$s = $this->getItemStore($key);
		try {
			$s->writeBinary($value->b);
		}catch(Exception $»e) {
			$_ex_ = ($»e instanceof HException) ? $»e->e : $»e;
			$t = $_ex_;
			{
				throw new HException("Unable to write the cache file \"" . $s->toString() . "\".");
			}
		}
	}
	public $cachedir;
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
	function __toString() { return 'com.wiris.util.sys.StoreCache'; }
}

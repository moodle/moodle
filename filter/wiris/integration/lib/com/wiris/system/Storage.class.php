<?php

class com_wiris_system_Storage {
	public function __construct($location) {
		if(!php_Boot::$skip_constructor) {
		$location = str_replace("/", com_wiris_system_Storage::getDirectorySeparator(), $location);
		$location = str_replace("\\", com_wiris_system_Storage::getDirectorySeparator(), $location);
		$this->location = $location;
	}}
	public function setResourceObject($obj) {
	}
	public function hlist() {
		return sys_FileSystem::readDirectory($this->location);
	}
	public function isDirectory() {
		return is_dir($this->location);
	}
	public function delete() {
		if(is_dir($this->location)) {
			@rmdir($this->location);
		} else {
			@unlink($this->location);
		}
	}
	public function toString() {
		return $this->location;
	}
	public function getParent() {
		$path = null;
		$path = com_wiris_system_Storage_0($this, $path);
		if($path === null) {
			$path = $this->location;
		}
		$index = _hx_last_index_of($path, com_wiris_system_Storage::getDirectorySeparator(), null);
		$path = (($index !== -1) ? _hx_substr($path, 0, $index) : ".");
		return new com_wiris_system_Storage($path);
	}
	public function mkdirs() {
		$parent = $this->getParent();
		if(!$parent->exists()) {
			$parent->mkdirs();
		}
		if(!$this->exists()) {
			@mkdir($this->location, 493);
		}
	}
	public function exists() {
		$exists = false;
		$exists = file_exists($this->location);
		return $exists;
	}
	public function read() {
		return haxe_io_Bytes::ofData($this->readBinary())->toString();
	}
	public function readBinary() {
		$bytes = null;
		$fi = sys_io_File::read($this->location, true);
		$bytes = $fi->readAll(null);
		return $bytes->b;
	}
	public function write($s) {
		$this->writeBinary(haxe_io_Bytes::ofString($s)->b);
	}
	public function writeBinary($bs) {
		$bytes = haxe_io_Bytes::ofData($bs);
		$fo = sys_io_File::write($this->location, true);
		$fo->writeBytes($bytes, 0, $bytes->length);
	}
	public $location;
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
	static $directorySeparator;
	static $resourcesDir;
	static function newResourceStorage($name) {
		$name = com_wiris_system_Storage::getResourcesDir() . com_wiris_system_Storage::getDirectorySeparator() . $name;
		return new com_wiris_system_Storage($name);
	}
	static function newStorage($name) {
		return new com_wiris_system_Storage($name);
	}
	static function newStorageWithParent($parent, $name) {
		$path = com_wiris_system_Storage_1($name, $parent);
		return new com_wiris_system_Storage($path);
	}
	static function getResourcesDir() {
		if(com_wiris_system_Storage::$resourcesDir === null) {
			com_wiris_system_Storage::setResourcesDir();
		}
		return com_wiris_system_Storage::$resourcesDir;
	}
	static function setResourcesDir() {
		$filedir = com_wiris_system_Storage::getCallerFile();
		com_wiris_system_Storage::$resourcesDir = _hx_substr($filedir, 0, _hx_last_index_of($filedir, com_wiris_system_Storage::getDirectorySeparator() . "com" . com_wiris_system_Storage::getDirectorySeparator(), null));
	}
	static function getCallerFile() {
		$thisfile = "";
		
		$trace = debug_backtrace();
		foreach ($trace as $item) {
			if (!com_wiris_system_Storage::isSystemFile($item['file'])) {
				return $item['file'];
			}
		}
		$thisfile = $trace[0]['file'];
		;
		return $thisfile;
	}
	static function isSystemFile($file) {
		$file = str_replace("\\", "/", $file);
		return _hx_index_of($file, "/com/wiris/system/", null) !== -1 || _hx_index_of($file, "/com/wiris/std/", null) !== -1 || _hx_index_of($file, "/com/wiris/util/", null) !== -1;
	}
	static function getDirectorySeparator() {
		if(com_wiris_system_Storage::$directorySeparator === null) {
			com_wiris_system_Storage::setDirectorySeparator();
		}
		return com_wiris_system_Storage::$directorySeparator;
	}
	static function setDirectorySeparator() {
		$sep = null;
		$sep = DIRECTORY_SEPARATOR;
		com_wiris_system_Storage::$directorySeparator = $sep;
	}
	static function getCurrentPath() {
		throw new HException("Not implemented!");
		return null;
	}
	function __toString() { return $this->toString(); }
}
function com_wiris_system_Storage_0(&$»this, &$path) {
	{
		$p = realpath($»this->location);
		if(($p === false)) {
			return null;
		} else {
			return $p;
		}
		unset($p);
	}
}
function com_wiris_system_Storage_1(&$name, &$parent) {
	if($parent->location === ".") {
		return $name;
	} else {
		return $parent->location . com_wiris_system_Storage::getDirectorySeparator() . $name;
	}
}

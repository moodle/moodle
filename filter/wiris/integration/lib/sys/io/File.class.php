<?php

class sys_io_File {
	public function __construct(){}
	static function getContent($path) {
		return file_get_contents($path);
	}
	static function getBytes($path) {
		return haxe_io_Bytes::ofString(sys_io_File::getContent($path));
	}
	static function saveContent($path, $content) {
		file_put_contents($path, $content);
	}
	static function saveBytes($path, $bytes) {
		$f = sys_io_File::write($path, null);
		$f->write($bytes);
		$f->close();
	}
	static function read($path, $binary = null) {
		if($binary === null) {
			$binary = true;
		}
		return new sys_io_FileInput(fopen($path, (($binary) ? "rb" : "r")));
	}
	static function write($path, $binary = null) {
		if($binary === null) {
			$binary = true;
		}
		return new sys_io_FileOutput(fopen($path, (($binary) ? "wb" : "w")));
	}
	static function append($path, $binary = null) {
		if($binary === null) {
			$binary = true;
		}
		return new sys_io_FileOutput(fopen($path, (($binary) ? "ab" : "a")));
	}
	static function copy($src, $dst) {
		copy($src, $dst);
	}
	function __toString() { return 'sys.io.File'; }
}

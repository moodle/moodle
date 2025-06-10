<?php

class sys__FileSystem_FileKind extends Enum {
	public static $kdir;
	public static $kfile;
	public static function kother($k) { return new sys__FileSystem_FileKind("kother", 2, array($k)); }
	public static $__constructors = array(0 => 'kdir', 1 => 'kfile', 2 => 'kother');
	}
sys__FileSystem_FileKind::$kdir = new sys__FileSystem_FileKind("kdir", 0);
sys__FileSystem_FileKind::$kfile = new sys__FileSystem_FileKind("kfile", 1);

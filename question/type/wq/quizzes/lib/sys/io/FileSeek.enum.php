<?php

class sys_io_FileSeek extends Enum {
	public static $SeekBegin;
	public static $SeekCur;
	public static $SeekEnd;
	public static $__constructors = array(0 => 'SeekBegin', 1 => 'SeekCur', 2 => 'SeekEnd');
	}
sys_io_FileSeek::$SeekBegin = new sys_io_FileSeek("SeekBegin", 0);
sys_io_FileSeek::$SeekCur = new sys_io_FileSeek("SeekCur", 1);
sys_io_FileSeek::$SeekEnd = new sys_io_FileSeek("SeekEnd", 2);

<?php

class com_wiris_system_Logger {
	public function __construct(){}
	static $SEVERE = 1000;
	static $WARNING = 900;
	static $INFO = 800;
	static function log($level, $message) {
		$prefix = null;
		if($level >= 1000) {
			$prefix = "SEVERE";
		} else {
			if($level >= 900) {
				$prefix = "WARNING";
			} else {
				$prefix = "INFO";
			}
		}
		$message = "[" . $prefix . "] " . $message;
		error_log($message);
	}
	function __toString() { return 'com.wiris.system.Logger'; }
}

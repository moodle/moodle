<?php

class com_wiris_system_LocalStorage {
	public function __construct() { 
	}
	static function setItem($key, $value) {
		return false;
	}
	static function getItem($key) {
		return null;
	}
	static function removeItem($key) {
		return false;
	}
	function __toString() { return 'com.wiris.system.LocalStorage'; }
}

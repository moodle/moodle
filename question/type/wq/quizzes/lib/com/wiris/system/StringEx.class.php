<?php

class com_wiris_system_StringEx {
	public function __construct(){}
	static function substring($s, $start, $end = null) {
		if($end === null) {
			return _hx_substr($s, $start, null);
		}
		return _hx_substr($s, $start, $end - $start);
	}
	static function compareTo($s1, $s2) {
		if($s1 > $s2) {
			return 1;
		}
		if($s1 < $s2) {
			return -1;
		}
		return 0;
	}
	function __toString() { return 'com.wiris.system.StringEx'; }
}

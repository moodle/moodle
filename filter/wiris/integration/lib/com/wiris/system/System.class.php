<?php

class com_wiris_system_System {
	public function __construct(){}
	static function arraycopy($src, $srcPos, $dest, $destPos, $n) {
		$_g = 0;
		while($_g < $n) {
			$i = $_g++;
			$dest[$destPos + $i] = $src[$srcPos + $i];
			unset($i);
		}
	}
	function __toString() { return 'com.wiris.system.System'; }
}

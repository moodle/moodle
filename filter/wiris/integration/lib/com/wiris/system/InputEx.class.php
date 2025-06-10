<?php

class com_wiris_system_InputEx {
	public function __construct(){}
	static function readInt32_($a) {
		$ch1 = $a->readByte();
		$ch2 = $a->readByte();
		$ch3 = $a->readByte();
		$ch4 = $a->readByte();
		return ($ch1 << 8 | $ch2) << 16 | ($ch3 << 8 | $ch4);
	}
	static function length_($a) {
		$x = Reflect::field($a, "len");
		if($x !== null && Std::is($x, _hx_qtype("Int"))) {
			return $x;
		} else {
			throw new HException("Not implemented!");
		}
	}
	function __toString() { return 'com.wiris.system.InputEx'; }
}

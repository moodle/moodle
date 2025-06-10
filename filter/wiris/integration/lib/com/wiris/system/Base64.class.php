<?php

class com_wiris_system_Base64 extends haxe_BaseCode {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct(haxe_io_Bytes::ofString("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"));
	}}
	public function encodeBytes($bytes) {
		$base64 = parent::encodeBytes($bytes)->toString();
		$remaining = 4 - _hx_mod(strlen($base64), 4);
		while($remaining > 0 && $remaining < 3) {
			$base64 .= "=";
			--$remaining;
		}
		return haxe_io_Bytes::ofString($base64);
	}
	public function decodeBytes($bytes) {
		return haxe_io_Bytes::ofString(base64_decode($bytes->b));
	}
	function __toString() { return 'com.wiris.system.Base64'; }
}

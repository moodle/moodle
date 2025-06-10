<?php

class com_wiris_system_PropertiesTools {
	public function __construct(){}
	static function getSystemProperty($s) {
		return null;
	}
	static function getProperty($prop, $key, $dflt = null) {
		if(isset($prop[$key])) {
			return $prop[$key];
		}
		return $dflt;
	}
	static function newProperties() {
		return array();;
	}
	static function setProperty($prop, $key, $value) {
		$prop[$key] = $value;
	}
	static function fromProperties($prop) {
		$ht = new Hash();
		$key = "";
		$value = "";
		foreach ($prop as $key => $value) {
		$ht->set($key, $value);
		}
		return $ht;
	}
	static function toProperties($h) {
		$np = array();;
		$ks = $h->keys();
		while($ks->hasNext()) {
			$k = $ks->next();
			$np[$k] = $h->get($k);
			unset($k);
		}
		return $np;
	}
	function __toString() { return 'com.wiris.system.PropertiesTools'; }
}

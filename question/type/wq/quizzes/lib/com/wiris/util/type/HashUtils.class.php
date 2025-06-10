<?php

class com_wiris_util_type_HashUtils {
	public function __construct() { 
	}
	static function putAll($hash1, $hash2) {
		$iterator = $hash1->keys();
		while($iterator->hasNext()) {
			$key = $iterator->next();
			$value = $hash1->get($key);
			$hash2->set($key, $value);
			unset($value,$key);
		}
	}
	static function filterByParam($array, $paramName, $param) {
		{
			$_g1 = 0; $_g = $array->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$item = $array[$i];
				if(_hx_equal($item->get($paramName), $param)) {
					return $item;
				}
				unset($item,$i);
			}
		}
		return null;
	}
	static function exists($obj, $key) {
		return $obj->get($key) !== null;
	}
	static function getKey($value, $hash) {
		$i = $hash->keys();
		while($i->hasNext()) {
			$key = $i->next();
			if($hash->get($key) == $value) {
				return $key;
			}
			unset($key);
		}
		return null;
	}
	static function getKeys($hash) {
		$i = $hash->keys();
		$keys = new _hx_array(array());
		while($i->hasNext()) {
			$keys->push($i->next());
		}
		return $keys;
	}
	function __toString() { return 'com.wiris.util.type.HashUtils'; }
}

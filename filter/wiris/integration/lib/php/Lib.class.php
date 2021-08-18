<?php

class php_Lib {
	public function __construct(){}
	static function hprint($v) {
		echo(Std::string($v));
	}
	static function println($v) {
		php_Lib::hprint($v);
		php_Lib::hprint("\x0A");
	}
	static function dump($v) {
		var_dump($v);
	}
	static function serialize($v) {
		return serialize($v);
	}
	static function unserialize($s) {
		return unserialize($s);
	}
	static function extensionLoaded($name) {
		return extension_loaded($name);
	}
	static function isCli() {
		return (0 == strncasecmp(PHP_SAPI, 'cli', 3));
	}
	static function printFile($file) {
		return fpassthru(fopen($file, "r"));
	}
	static function toPhpArray($a) {
		return $a->»a;
	}
	static function toHaxeArray($a) {
		return new _hx_array($a);
	}
	static function hashOfAssociativeArray($arr) {
		$h = new Hash();
		$h->h = $arr;
		return $h;
	}
	static function associativeArrayOfHash($hash) {
		return $hash->h;
	}
	static function objectOfAssociativeArray($arr) {
		foreach($arr as $key => $value){
			if(is_array($value)) $arr[$key] = php_Lib::objectOfAssociativeArray($value);
		}
		return _hx_anonymous($arr);
	}
	static function associativeArrayOfObject($ob) {
		return (array) $ob;
	}
	static function mail($to, $subject, $message, $additionalHeaders = null, $additionalParameters = null) {
		if(null !== $additionalParameters) {
			return mail($to, $subject, $message, $additionalHeaders, $additionalParameters);
		} else {
			if(null !== $additionalHeaders) {
				return mail($to, $subject, $message, $additionalHeaders);
			} else {
				return mail($to, $subject, $message);
			}
		}
	}
	static function rethrow($e) {
		if(Std::is($e, _hx_qtype("php.Exception"))) {
			$__rtex__ = $e;
			throw $__rtex__;
		} else {
			throw new HException($e);
		}
	}
	static function appendType($o, $path, $t) {
		$name = $path->shift();
		if($path->length === 0) {
			$o->$name = $t;
		} else {
			$so = ((isset($o->$name)) ? $o->$name : _hx_anonymous(array()));
			php_Lib::appendType($so, $path, $t);
			$o->$name = $so;
		}
	}
	static function getClasses() {
		$path = null;
		$o = _hx_anonymous(array());
		reset(php_Boot::$qtypes);
		while(($path = key(php_Boot::$qtypes)) !== null) {
			php_Lib::appendType($o, _hx_explode(".", $path), php_Boot::$qtypes[$path]);
			next(php_Boot::$qtypes);
		}
		return $o;
	}
	static function loadLib($pathToLib) {
		$prefix = null;
		$_hx_types_array = array();
 		$_hx_cache_content = '';
 		//Calling this function will put all types present in the specified types in the $_hx_types_array
 		_hx_build_paths($pathToLib, $_hx_types_array, array(), $prefix);

 		for($i=0;$i<count($_hx_types_array);$i++) {
 			//For every type that has been found, create its description
 			$t = null;
 			if($_hx_types_array[$i]['type'] == 0) {
 				$t = new _hx_class($_hx_types_array[$i]['phpname'], $_hx_types_array[$i]['qname'], $_hx_types_array[$i]['path']);
 			} else if($_hx_types_array[$i]['type'] == 1) {
 				$t = new _hx_enum($_hx_types_array[$i]['phpname'], $_hx_types_array[$i]['qname'], $_hx_types_array[$i]['path']);
 			} else if($_hx_types_array[$i]['type'] == 2) {
 				$t = new _hx_interface($_hx_types_array[$i]['phpname'], $_hx_types_array[$i]['qname'], $_hx_types_array[$i]['path']);
 			} else if($_hx_types_array[$i]['type'] == 3) {
 				$t = new _hx_class($_hx_types_array[$i]['name'], $_hx_types_array[$i]['qname'], $_hx_types_array[$i]['path']);
 			}
 			//Register the type
 			if(!array_key_exists($t->__qname__, php_Boot::$qtypes)) {
 				_hx_register_type($t);
 			}
 		}
 
	}
	function __toString() { return 'php.Lib'; }
}

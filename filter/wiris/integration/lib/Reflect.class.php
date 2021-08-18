<?php

class Reflect {
	public function __construct(){}
	static function hasField($o, $field) {
		return _hx_has_field($o, $field);
	}
	static function field($o, $field) {
		return _hx_field($o, $field);
	}
	static function setField($o, $field, $value) {
		$o->{$field} = $value;
	}
	static function getProperty($o, $field) {
		if(null === $o) {
			return null;
		}
		$cls = ((Std::is($o, _hx_qtype("Class"))) ? $o->__tname__ : get_class($o));
		$cls_vars = get_class_vars($cls);
		if(isset($cls_vars['__properties__']) && isset($cls_vars['__properties__']['get_'.$field]) && ($field = $cls_vars['__properties__']['get_'.$field])) {
			return $o->$field();
		} else {
			return $o->$field;
		}
	}
	static function setProperty($o, $field, $value) {
		if(null === $o) {
			null;
			return;
		}
		$cls = ((Std::is($o, _hx_qtype("Class"))) ? $o->__tname__ : get_class($o));
		$cls_vars = get_class_vars($cls);
		if(isset($cls_vars['__properties__']) && isset($cls_vars['__properties__']['set_'.$field]) && ($field = $cls_vars['__properties__']['set_'.$field])) {
			$o->$field($value);
			return;
		} else {
			$o->$field = $value;
			return;
		}
	}
	static function callMethod($o, $func, $args) {
		if(is_string($o) && !is_array($func)) {
			return call_user_func_array(Reflect::field($o, $func), $args->»a);
		}
		return call_user_func_array(((is_callable($func)) ? $func : array($o, $func)), ((null === $args) ? array() : $args->»a));
	}
	static function fields($o) {
		if($o === null) {
			return new _hx_array(array());
		}
		return (($o instanceof _hx_array) ? new _hx_array(array('concat','copy','insert','iterator','length','join','pop','push','remove','reverse','shift','slice','sort','splice','toString','unshift')) : ((is_string($o)) ? new _hx_array(array('charAt','charCodeAt','indexOf','lastIndexOf','length','split','substr','toLowerCase','toString','toUpperCase')) : new _hx_array(_hx_get_object_vars($o))));
	}
	static function isFunction($f) {
		return (is_array($f) && is_callable($f)) || _hx_is_lambda($f) || is_array($f) && _hx_has_field($f[0], $f[1]) && $f[1] !== "length";
	}
	static function compare($a, $b) {
		return (($a == $b) ? 0 : (($a > $b) ? 1 : -1));
	}
	static function compareMethods($f1, $f2) {
		if(is_array($f1) && is_array($f1)) {
			return $f1[0] === $f2[0] && $f1[1] == $f2[1];
		}
		if(is_string($f1) && is_string($f2)) {
			return _hx_equal($f1, $f2);
		}
		return false;
	}
	static function isObject($v) {
		if($v === null) {
			return false;
		}
		if(is_object($v)) {
			return $v instanceof _hx_anonymous || Type::getClass($v) !== null;
		}
		return is_string($v) && !_hx_is_lambda($v);
	}
	static function deleteField($o, $f) {
		if(!_hx_has_field($o, $f)) {
			return false;
		}
		if(isset($o->»dynamics[$f])) unset($o->»dynamics[$f]); else if($o instanceof _hx_anonymous) unset($o->$f); else $o->$f = null;
		return true;
	}
	static function copy($o) {
		if(is_string($o)) {
			return $o;
		}
		$o2 = _hx_anonymous(array());
		{
			$_g = 0; $_g1 = Reflect::fields($o);
			while($_g < $_g1->length) {
				$f = $_g1[$_g];
				++$_g;
				$o2->{$f} = Reflect::field($o, $f);
				unset($f);
			}
		}
		return $o2;
	}
	static function makeVarArgs($f) {
		return array(new _hx_lambda(array(&$f), '_hx_make_var_args'), 'execute');
	}
	function __toString() { return 'Reflect'; }
}

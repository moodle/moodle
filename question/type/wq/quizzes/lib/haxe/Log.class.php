<?php

class haxe_Log {
	public function __construct(){}
	static function trace($v, $infos = null) { return call_user_func_array(self::$trace, array($v, $infos)); }
	public static $trace = null;
	static function clear() { return call_user_func(self::$clear); }
	public static $clear = null;
	function __toString() { return 'haxe.Log'; }
}
haxe_Log::$trace = array(new _hx_lambda(array(), "haxe_Log_0"), 'execute');
haxe_Log::$clear = array(new _hx_lambda(array(), "haxe_Log_1"), 'execute');
function haxe_Log_0($v, $infos) {
	{
		_hx_trace($v, $infos);
	}
}
function haxe_Log_1() {
	{
	}
}

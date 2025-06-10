<?php

class com_wiris_util_json_StringParser {
	public function __construct() {
		;
	}
	public function isHexDigit($c) {
		if($c >= 48 && $c <= 58) {
			return true;
		}
		if($c >= 97 && $c <= 102) {
			return true;
		}
		if($c >= 65 && $c <= 70) {
			return true;
		}
		return false;
	}
	public function getPositionRepresentation() {
		$i0 = com_wiris_common_WInteger::min($this->i, $this->n);
		$s0 = com_wiris_common_WInteger::max(0, $this->i - 20);
		$e0 = com_wiris_common_WInteger::min($this->n, $this->i + 20);
		return "..." . _hx_substr($this->str, $s0, $i0 - $s0) . " >>> . <<<" . _hx_substr($this->str, $i0, $e0);
	}
	public function nextSafeToken() {
		if($this->i < $this->n) {
			$this->c = haxe_Utf8::charCodeAt(_hx_substr($this->str, $this->i, null), 0);
			$this->i += strlen((com_wiris_util_json_StringParser_0($this)));
		} else {
			$this->c = -1;
		}
	}
	public function nextToken() {
		if($this->c === -1) {
			throw new HException("End of string");
		}
		$this->nextSafeToken();
	}
	public function skipBlanks() {
		while($this->i < $this->n && com_wiris_util_json_StringParser::isBlank($this->c)) {
			$this->nextToken();
		}
	}
	public function init($str) {
		$this->str = $str;
		$this->i = 0;
		$this->n = strlen($str);
		$this->nextToken();
	}
	public $str;
	public $c;
	public $n;
	public $i;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->»dynamics[$m]) && is_callable($this->»dynamics[$m]))
			return call_user_func_array($this->»dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call «'.$m.'»');
	}
	static function isBlank($c) {
		return $c === 32 || $c === 10 || $c === 13 || $c === 9 || $c === 160;
	}
	function __toString() { return 'com.wiris.util.json.StringParser'; }
}
function com_wiris_util_json_StringParser_0(&$»this) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($»this->c);
		return $s->toString();
	}
}

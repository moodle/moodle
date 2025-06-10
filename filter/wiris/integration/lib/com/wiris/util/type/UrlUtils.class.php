<?php

class com_wiris_util_type_UrlUtils {
	public function __construct(){}
	static $charCodeA;
	static $charCodeZ;
	static $charCodea;
	static $charCodez;
	static $charCode0;
	static $charCode9;
	static function isAllowed($c) {
		$allowedChars = _hx_index_of("-_.!~*'()", com_wiris_util_type_UrlUtils_0($c), null) !== -1;
		return $c >= com_wiris_util_type_UrlUtils::$charCodeA && $c <= com_wiris_util_type_UrlUtils::$charCodeZ || $c >= com_wiris_util_type_UrlUtils::$charCodea && $c <= com_wiris_util_type_UrlUtils::$charCodez || $c >= com_wiris_util_type_UrlUtils::$charCode0 && $c <= com_wiris_util_type_UrlUtils::$charCode9 || $allowedChars;
	}
	static function urlComponentEncode($uriComponent) {
		$sb = new StringBuf();
		$buf = haxe_io_Bytes::ofData(com_wiris_system_Utf8::toBytes($uriComponent));
		$i = null;
		{
			$_g1 = 0; $_g = $buf->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$b = ord($buf->b[$i1]) & 255;
				if(com_wiris_util_type_UrlUtils::isAllowed($b)) {
					$sb->add(com_wiris_util_type_UrlUtils_1($_g, $_g1, $b, $buf, $i, $i1, $sb, $uriComponent));
				} else {
					$sb->add("%");
					$sb->add(StringTools::hex($b, 2));
				}
				unset($i1,$b);
			}
		}
		return $sb->b;
	}
	function __toString() { return 'com.wiris.util.type.UrlUtils'; }
}
com_wiris_util_type_UrlUtils::$charCodeA = _hx_char_code_at("A", 0);
com_wiris_util_type_UrlUtils::$charCodeZ = _hx_char_code_at("Z", 0);
com_wiris_util_type_UrlUtils::$charCodea = _hx_char_code_at("a", 0);
com_wiris_util_type_UrlUtils::$charCodez = _hx_char_code_at("z", 0);
com_wiris_util_type_UrlUtils::$charCode0 = _hx_char_code_at("0", 0);
com_wiris_util_type_UrlUtils::$charCode9 = _hx_char_code_at("9", 0);
function com_wiris_util_type_UrlUtils_0(&$c) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($c);
		return $s->toString();
	}
}
function com_wiris_util_type_UrlUtils_1(&$_g, &$_g1, &$b, &$buf, &$i, &$i1, &$sb, &$uriComponent) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar($b);
		return $s->toString();
	}
}

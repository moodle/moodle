<?php

class com_wiris_plugin_impl_TextFilterTags {
	public function __construct() {
		;
	}
	public function init($tags, $mathNamespace) {
		if($mathNamespace !== null) {
			$tags->mathTag = $mathNamespace . ":" . $tags->mathTag;
		}
		$tags->in_appletopen = $this->in_open . "APPLET";
		$tags->in_appletclose = $this->in_open . "/APPLET" . $this->in_close;
		$tags->in_mathopen = $this->in_open . $this->mathTag;
		$tags->in_mathclose = $this->in_open . "/" . $this->mathTag . $this->in_close;
		$tags->out_open = "<";
		$tags->out_close = ">";
		$tags->out_entity = "&";
		$tags->out_quote = "'";
		$tags->out_double_quote = "\"";
	}
	public $mathTag;
	public $in_appletclose;
	public $in_appletopen;
	public $out_quote;
	public $in_quote;
	public $out_entity;
	public $in_entity;
	public $out_close;
	public $in_close;
	public $out_open;
	public $in_open;
	public $out_double_quote;
	public $in_double_quote;
	public $in_mathclose;
	public $in_mathopen;
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
	static function newSafeXml() {
		$tags = new com_wiris_plugin_impl_TextFilterTags();
		$tags->in_open = com_wiris_plugin_impl_TextFilterTags_0($tags);
		$tags->in_close = com_wiris_plugin_impl_TextFilterTags_1($tags);
		$tags->in_entity = com_wiris_plugin_impl_TextFilterTags_2($tags);
		$tags->in_quote = "`";
		$tags->in_double_quote = com_wiris_plugin_impl_TextFilterTags_3($tags);
		$tags->mathTag = "math";
		$tags->init($tags, null);
		return $tags;
	}
	static function newXml($mathNamespace) {
		$tags = new com_wiris_plugin_impl_TextFilterTags();
		$tags->in_open = "<";
		$tags->in_close = ">";
		$tags->in_entity = "&";
		$tags->in_quote = "'";
		$tags->in_double_quote = "\"";
		$tags->mathTag = "math";
		$tags->init($tags, $mathNamespace);
		return $tags;
	}
	function __toString() { return 'com.wiris.plugin.impl.TextFilterTags'; }
}
function com_wiris_plugin_impl_TextFilterTags_0(&$tags) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(171);
		return $s->toString();
	}
}
function com_wiris_plugin_impl_TextFilterTags_1(&$tags) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(187);
		return $s->toString();
	}
}
function com_wiris_plugin_impl_TextFilterTags_2(&$tags) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(167);
		return $s->toString();
	}
}
function com_wiris_plugin_impl_TextFilterTags_3(&$tags) {
	{
		$s = new haxe_Utf8(null);
		$s->addChar(168);
		return $s->toString();
	}
}

<?php

class com_wiris_util_json_parser_JsonParseException extends com_wiris_system_Exception {
	public function __construct($message) { if(!php_Boot::$skip_constructor) {
		parent::__construct($message,null);
	}}
	static function newFromMessage($message) {
		return new com_wiris_util_json_parser_JsonParseException($message);
	}
	static function newFromStack($stateStack, $message) {
		$jsonTrace = "";
		{
			$_g1 = 0; $_g = $stateStack->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$name = _hx_array_get($stateStack, $i)->propertyName;
				if($name === null) {
					$list = _hx_array_get($stateStack, $i)->container;
					$name = "[" . _hx_string_rec($list->length, "") . "]";
					unset($list);
				}
				$jsonTrace .= $name . ((($i !== $stateStack->length - 1) ? "." : ""));
				unset($name,$i);
			}
		}
		$jsonTrace = com_wiris_util_json_parser_JsonParseException_0($jsonTrace, $message, $stateStack);
		return new com_wiris_util_json_parser_JsonParseException($jsonTrace . ": " . $message);
	}
	function __toString() { return 'com.wiris.util.json.parser.JsonParseException'; }
}
function com_wiris_util_json_parser_JsonParseException_0(&$jsonTrace, &$message, &$stateStack) {
	if($jsonTrace === "") {
		return "<root>";
	} else {
		return "<root>." . $jsonTrace;
	}
}

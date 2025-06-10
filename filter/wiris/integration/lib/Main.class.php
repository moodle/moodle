<?php

class Main {
	public function __construct(){}
	static function main() {
		haxe_Log::trace("Hello World !", _hx_anonymous(array("fileName" => "Main.hx", "lineNumber" => 5, "className" => "Main", "methodName" => "main")));
	}
	function __toString() { return 'Main'; }
}

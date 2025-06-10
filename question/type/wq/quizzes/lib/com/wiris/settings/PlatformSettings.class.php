<?php

class com_wiris_settings_PlatformSettings {
	public function __construct(){}
	static $IS_JAVA = false;
	static $IS_CSHARP = false;
	static $PARSE_XML_ENTITIES = false;
	static $UTF8_CONVERSION = true;
	static $IS_JAVASCRIPT = false;
	static $IS_FLASH = false;
	function __toString() { return 'com.wiris.settings.PlatformSettings'; }
}

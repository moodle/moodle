<?php

class com_wiris_plugin_impl_DefaultConfigurationUpdater implements com_wiris_plugin_configuration_ConfigurationUpdater{
	public function __construct() { 
	}
	public function updateConfiguration(&$configuration) {
		$configuration = $configuration;
		$s = com_wiris_system_Storage::newResourceStorage("default-configuration.ini")->read();
		$defaultIniFile = com_wiris_util_sys_IniFile::newIniFileFromString($s);
		$h = $defaultIniFile->getProperties();
		$iter = $h->keys();
		while($iter->hasNext()) {
			$key = null;
			$key = $iter->next();
			if(com_wiris_system_PropertiesTools::getProperty($configuration, $key, null) === null) {
				$configuration[$key] = $h->get($key);
			}
			unset($key);
		}
	}
	public function init($obj) {
	}
	function __toString() { return 'com.wiris.plugin.impl.DefaultConfigurationUpdater'; }
}

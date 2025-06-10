<?php

class com_wiris_plugin_impl_FileConfigurationUpdater implements com_wiris_plugin_configuration_ConfigurationUpdater{
	public function __construct() { 
	}
	public function updateConfiguration(&$configuration) {
		$configuration = $configuration;
		$confDir = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CONFIGURATION_PATH, null);
		if($confDir !== null) {
			$confFile = $confDir . "/configuration.ini";
			$s = com_wiris_system_Storage::newStorage($confFile);
			if($s->exists()) {
				$defaultIniFile = com_wiris_util_sys_IniFile::newIniFileFromFilename($confFile);
				$h = $defaultIniFile->getProperties();
				$iter = $h->keys();
				while($iter->hasNext()) {
					$key = null;
					$key = $iter->next();
					$configuration[$key] = $h->get($key);
					unset($key);
				}
			}
		}
	}
	public function init($obj) {
	}
	function __toString() { return 'com.wiris.plugin.impl.FileConfigurationUpdater'; }
}

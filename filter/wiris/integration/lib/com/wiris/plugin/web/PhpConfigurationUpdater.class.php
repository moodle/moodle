<?php

class com_wiris_plugin_web_PhpConfigurationUpdater implements com_wiris_plugin_configuration_ConfigurationUpdater{
	public function __construct() { 
	}
	public function updateConfiguration(&$configuration) {
		$configuration = $configuration;
		$v = null;
		$base = null;
		$base = dirname(__FILE__);
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CACHE_FOLDER, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$CACHE_FOLDER] = $base . "/../../../../../../cache";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$FORMULA_FOLDER, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$FORMULA_FOLDER] = $base . "/../../../../../../formulas";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$SHOWIMAGE_PATH, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$SHOWIMAGE_PATH] = "integration/showimage.php?formula=";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$SHOWCASIMAGE_PATH, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$SHOWCASIMAGE_PATH] = "integration/showcasimage.php?formula=";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CLEAN_CACHE_PATH, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$CLEAN_CACHE_PATH] = "cleancache.php";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$RESOURCE_PATH, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$RESOURCE_PATH] = "resource.php";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CONTEXT_PATH, null);
		if($v === null) {
			$filePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$CONTEXT_PATH] = $filePath . "/";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CONFIGURATION_PATH, null);
		if($v === null) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$CONFIGURATION_PATH] = $base . "/../../../../../..";
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$EXTERNAL_REFERER, null);
		if($v === null) {
			$external_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$EXTERNAL_REFERER] = $external_referer;
		}
		$v = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$REFERER, null);
		if($v === null) {
			$referer = ((empty($_SERVER['HTTPS'])) ? "http://" : "https://");
			$referer .= $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
			if(isset($_SERVER['QUERY_STRING'])) {
				$referer .= "?" . $_SERVER['QUERY_STRING'];
			}
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$REFERER] = $referer;
		}
		$userAgent = new com_wiris_util_net_UserAgent(null);
		if($userAgent->isIe()) {
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$IMAGE_FORMAT] = "png";
			$configuration[com_wiris_plugin_api_ConfigurationKeys::$IMPROVE_PERFORMANCE] = "false";
		}
	}
	public function init($obj) {
	}
	function __toString() { return 'com.wiris.plugin.web.PhpConfigurationUpdater'; }
}

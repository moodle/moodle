<?php

interface com_wiris_plugin_api_Configuration {
	function setConfigurations($configurationKeys, $configurationValues);
	function getJsonConfiguration($configurationKeys);
	function setInitObject($context);
	function setProperty($name, $value);
	function getProperty($name, $dflt);
	function getJavaScriptConfigurationJson();
	function getFullConfiguration();
}

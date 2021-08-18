<?php

class com_wiris_plugin_impl_CustomConfigurationUpdater implements com_wiris_plugin_configuration_ConfigurationUpdater{
	public function __construct($config) {
		if(!php_Boot::$skip_constructor) {
		$this->config = $config;
	}}
	public function updateConfiguration(&$configuration) {
		$configuration = $configuration;
		$confClass = com_wiris_system_PropertiesTools::getProperty($configuration, com_wiris_plugin_api_ConfigurationKeys::$CONFIGURATION_CLASS, null);
		if($confClass !== null && _hx_index_of($confClass, "com.wiris.plugin.servlets.configuration.ParameterServletConfigurationUpdater", null) !== -1) {
			return;
		}
		if($confClass !== null) {
			$cls = Type::resolveClass($confClass);
			if($cls === null) {
				throw new HException("Class " . $confClass . " not found.");
			}
			$obj = Type::createInstance($cls, new _hx_array(array()));
			if($obj === null) {
				throw new HException("Instance from " . Std::string($cls) . " cannot be created.");
			}
			$cu = $obj;
			$this->config->initialize($cu);
			$cu->updateConfiguration($configuration);
		}
	}
	public function init($obj) {
	}
	public $config;
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->�dynamics[$m]) && is_callable($this->�dynamics[$m]))
			return call_user_func_array($this->�dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call �'.$m.'�');
	}
	function __toString() { return 'com.wiris.plugin.impl.CustomConfigurationUpdater'; }
}

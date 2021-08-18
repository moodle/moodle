<?php

class com_wiris_plugin_impl_GenericParamsProviderImpl implements com_wiris_plugin_api_ParamsProvider{
	public function __construct($properties) {
		if(!php_Boot::$skip_constructor) {
		$this->properties = $properties;
	}}
	public function getServiceParameters() {
		$serviceParams = array();;
		$serviceParamListArray = _hx_explode(",", com_wiris_plugin_api_ConfigurationKeys::$SERVICES_PARAMETERS_LIST);
		$i = null;
		{
			$_g1 = 0; $_g = $serviceParamListArray->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$key = $serviceParamListArray[$i1];
				$value = com_wiris_system_PropertiesTools::getProperty($this->properties, $key, null);
				if($value !== null) {
					$serviceParams[$key] = $value;
				}
				unset($value,$key,$i1);
			}
		}
		return $serviceParams;
	}
	public function getRenderParameters($configuration) {
		$renderParams = array();;
		$renderParameterList = _hx_explode(",", $configuration->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_LIST, com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_DEFAULT_LIST));
		$i = null;
		{
			$_g1 = 0; $_g = $renderParameterList->length;
			while($_g1 < $_g) {
				$i1 = $_g1++;
				$key = $renderParameterList[$i1];
				$value = com_wiris_system_PropertiesTools::getProperty($this->properties, $key, null);
				if($value !== null) {
					$renderParams[$key] = $value;
				}
				unset($value,$key,$i1);
			}
		}
		return $renderParams;
	}
	public function getParameters() {
		return $this->properties;
	}
	public function getRequiredParameter($param) {
		$parameter = com_wiris_system_PropertiesTools::getProperty($this->properties, $param, null);
		if($parameter !== null) {
			return $parameter;
		} else {
			throw new HException("Error: parameter " . $param . " is required");
		}
	}
	public function getParameter($param, $dflt) {
		return com_wiris_system_PropertiesTools::getProperty($this->properties, $param, $dflt);
	}
	public $properties;
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
	function __toString() { return 'com.wiris.plugin.impl.GenericParamsProviderImpl'; }
}

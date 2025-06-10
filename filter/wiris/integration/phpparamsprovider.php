<?php
class PhpParamsProvider implements com_wiris_plugin_api_ParamsProvider {

    private $parameters = array();
    private $serviceParamsList = array('mml', 'lang', 'service', 'latex', 'mode');

    public function __construct() {
        $this->parameters = array_merge($_GET, $_POST);
    }

    public function getRequiredParameter($paramname) {
        if (array_key_exists($paramname, $this->parameters)) {
            return $this->parameters[$paramname];
        } else {
            throw new Exception('Missing param ' . $paramname);
        }
    }

    public function getParameter($paramname, $dflt) {
        if (array_key_exists($paramname, $this->parameters)) {
            return $this->parameters[$paramname];
        } else {
            return $dflt;
        }
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function getServiceParameters() {
        $serviceParams = array();
        foreach ($this->serviceParamsList as $key) {
            if (array_key_exists($key, $this->parameters)) {
                $serviceParams[$key] = $this->parameters[$key];
            }
        }
        return $serviceParams;

    }

    public function getRenderParameters($configuration) {
        $renderParams = array();
        $renderParameterList = explode(",", $configuration->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_LIST, com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_DEFAULT_LIST));
        $i = null;
        foreach ($renderParameterList as $key) {
            if (array_key_exists($key, $this->parameters)) {
                $renderParams[$key] = $this->parameters[$key];
            }
        }
        return $renderParams;
    }
}

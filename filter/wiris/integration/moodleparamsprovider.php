<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once('../../../lib/moodlelib.php');

class MoodleParamsProvider implements com_wiris_plugin_api_ParamsProvider {

    private $parameters = array();
    private $serviceparamlist = array('mml', 'lang', 'service', 'latex');
    private $wrap;

    public function __construct() {
        $this->wrap = com_wiris_system_CallWrapper::getInstance();
    }

    public function getrequiredparameter($paramname) {
        $this->wrap->stop();
        $param = required_param($paramname, PARAM_RAW);
        $this->wrap->start();
        return $param;
    }

    public function getparameter($paramname, $dflt) {
        $this->wrap->stop();
        $param = optional_param($paramname, $dflt, PARAM_RAW);
        $this->wrap->start();
        return $param;
    }

    public function getparameters() {
        return $this->parameters;
    }

    public function getserviceparameters() {
        $this->wrap->stop();
        $serviceparams = array();
        foreach ($this->serviceparamlist as $key) {
            if ($serviceparam = optional_param($key, false, PARAM_RAW)) {
                $serviceparams[$key] = $serviceparam;
            }
        }
        $this->wrap->start();
        return $serviceparams;

    }

    public function getrenderparameters($configuration) {
        $this->wrap->stop();
        $renderparams = array();
        // Can't change EDITOR_PARAMETER_LIST variable name so at this point condingStandars should be disabled.
        // @codingStandardsIgnoreStart
        $renderparameterlist = explode(",", $configuration->getProperty(com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_LIST, com_wiris_plugin_api_ConfigurationKeys::$EDITOR_PARAMETERS_DEFAULT_LIST));
        // @codingStandardsIgnoreEnd
        $i = null;
        foreach ($renderparameterlist as $key) {
            if ($renderparam = optional_param($key, false, PARAM_RAW)) {
                $renderparams[$key] = $renderparam;
            }
        }
        $this->wrap->start();
        return $renderparams;
    }
}

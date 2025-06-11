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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/moodlelib.php');
require_once($CFG->dirroot . '/filter/wiris/integration/lib/com/wiris/plugin/api/ParamsProvider.interface.php');
/**
 * This class implements com_wiris_plugin_api_ParamsProvider interface
 * o use optional_param and required_param methods instead of
 * access directly $_GET nad $_POST variables.
 *
 * @package    filter_wiris
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_wiris_paramsprovider implements com_wiris_plugin_api_ParamsProvider {
    /**
     * @var array $parameters The list of parameters.
     */
    private $parameters = [];

    /**
     * @var array $serviceparamlist The list of service parameters.
     */
    private $serviceparamlist = ['mml', 'lang', 'service', 'latex'];

    /**
     * @var mixed $wrap The wrap instance.
     */
    private $wrap;

    /**
     * Constructor for the ParamsProvider class.
     */
    public function __construct() {
        $this->wrap = com_wiris_system_CallWrapper::getInstance();
    }

    /**
     * Retrieves the value of a required parameter.
     *
     * @param string $paramname The name of the parameter to retrieve.
     * @return mixed The value of the required parameter.
     */
    public function getrequiredparameter($paramname) {
        $this->wrap->stop();
        $param = required_param($paramname, PARAM_RAW);
        $this->wrap->start();
        return $param;
    }

    /**
     * Retrieves the value of a parameter.
     *
     * @param string $paramname The name of the parameter.
     * @param mixed $dflt The default value to return if the parameter is not set.
     * @return mixed The value of the parameter.
     */
    public function getparameter($paramname, $dflt) {
        $this->wrap->stop();
        $param = optional_param($paramname, $dflt, PARAM_RAW);
        $this->wrap->start();
        return $param;
    }

    /**
     * Retrieves the parameters of the ParamsProvider.
     *
     * @return array The parameters of the ParamsProvider.
     */
    public function getparameters() {
        return $this->parameters;
    }

    /**
     * Retrieves the service parameters.
     *
     * This method retrieves the service parameters by iterating through the list of service parameter keys
     * and fetching the corresponding values using the optional_param function.
     *
     * @return array The service parameters as an associative array.
     */
    public function getserviceparameters() {
        $this->wrap->stop();
        $serviceparams = [];
        foreach ($this->serviceparamlist as $key) {
            $serviceparams[$key] = optional_param($key, false, PARAM_RAW);
        }
        $this->wrap->start();
        return $serviceparams;
    }

    /**
     * Retrieves the render parameters based on the given configuration.
     *
     * @param mixed $configuration The configuration object.
     * @return array The render parameters.
     */
    public function getrenderparameters($configuration) {
        $this->wrap->stop();
        $renderparams = [];
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

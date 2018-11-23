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

/**
 * This file contains an abstract definition of an LTI resource
 *
 * @package    mod_lti
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_lti\local\ltiservice;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');


/**
 * The mod_lti\local\ltiservice\resource_base class.
 *
 * @package    mod_lti
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class resource_base {

    /**  HTTP Post method */
    const HTTP_POST = 'POST';
    /**  HTTP Get method */
    const HTTP_GET = 'GET';
    /**  HTTP Put method */
    const HTTP_PUT = 'PUT';
    /**  HTTP Delete method */
    const HTTP_DELETE = 'DELETE';

    /** @var service_base Service associated with this resource. */
    private $service;
    /** @var string Type for this resource. */
    protected $type;
    /** @var string ID for this resource. */
    protected $id;
    /** @var string Template for this resource. */
    protected $template;
    /** @var array Custom parameter substitution variables associated with this resource. */
    protected $variables;
    /** @var array Media types supported by this resource. */
    protected $formats;
    /** @var array HTTP actions supported by this resource. */
    protected $methods;
    /** @var array Template variables parsed from the resource template. */
    protected $params;


    /**
     * Class constructor.
     *
     * @param service_base $service Service instance
     */
    public function __construct($service) {

        $this->service = $service;
        $this->type = 'RestService';
        $this->id = null;
        $this->template = null;
        $this->methods = array();
        $this->variables = array();
        $this->formats = array();
        $this->methods = array();
        $this->params = null;

    }

    /**
     * Get the resource ID.
     *
     * @return string
     */
    public function get_id() {

        return $this->id;

    }

    /**
     * Get the resource template.
     *
     * @return string
     */
    public function get_template() {

        return $this->template;

    }

    /**
     * Get the resource path.
     *
     * @return string
     */
    public function get_path() {

        return $this->get_template();

    }

    /**
     * Get the resource type.
     *
     * @return string
     */
    public function get_type() {

        return $this->type;

    }

    /**
     * Get the resource's service.
     *
     * @return mixed
     */
    public function get_service() {

        return $this->service;

    }

    /**
     * Get the resource methods.
     *
     * @return array
     */
    public function get_methods() {

        return $this->methods;

    }

    /**
     * Get the resource media types.
     *
     * @return array
     */
    public function get_formats() {

        return $this->formats;

    }

    /**
     * Get the resource template variables.
     *
     * @return array
     */
    public function get_variables() {

        return $this->variables;

    }

    /**
     * Get the resource fully qualified endpoint.
     *
     * @return string
     */
    public function get_endpoint() {

        $this->parse_template();
        $url = $this->get_service()->get_service_path() . $this->get_template();
        foreach ($this->params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        $toolproxy = $this->get_service()->get_tool_proxy();
        if (!empty($toolproxy)) {
            $url = str_replace('{tool_proxy_id}', $toolproxy->guid, $url);
        }

        return $url;

    }

    /**
     * Execute the request for this resource.
     *
     * @param response $response  Response object for this request.
     */
    public abstract function execute($response);

    /**
     * Check to make sure the request is valid.
     *
     * @param string $toolproxyguid Consumer key
     * @param string $body          Body of HTTP request message
     *
     * @return boolean
     */
    public function check_tool_proxy($toolproxyguid, $body = null) {

        $ok = false;
        if ($this->get_service()->check_tool_proxy($toolproxyguid, $body)) {
            $toolproxyjson = $this->get_service()->get_tool_proxy()->toolproxy;
            if (empty($toolproxyjson)) {
                $ok = true;
            } else {
                $toolproxy = json_decode($toolproxyjson);
                if (!empty($toolproxy) && isset($toolproxy->security_contract->tool_service)) {
                    $contexts = lti_get_contexts($toolproxy);
                    $tpservices = $toolproxy->security_contract->tool_service;
                    foreach ($tpservices as $service) {
                        $fqid = lti_get_fqid($contexts, $service->service);
                        $id = explode('#', $fqid, 2);
                        if ($this->get_id() === $id[1]) {
                            $ok = true;
                            break;
                        }
                    }
                }
                if (!$ok) {
                    debugging('Requested service not included in tool proxy: ' . $this->get_id(), DEBUG_DEVELOPER);
                }
            }
        }

        return $ok;

    }

    /**
     * Check to make sure the request is valid.
     *
     * @param int $typeid                   The typeid we want to use
     * @param int $contextid                The course we are at
     * @param string $permissionrequested   The permission to be checked
     * @param string $body                  Body of HTTP request message
     *
     * @return boolean
     */
    public function check_type($typeid, $contextid, $permissionrequested, $body = null) {
        $ok = false;
        if ($this->get_service()->check_type($typeid, $contextid, $body)) {
            $neededpermissions = $this->get_permissions($typeid);
            foreach ($neededpermissions as $permission) {
                if ($permission == $permissionrequested) {
                    $ok = true;
                    break;
                }
            }
            if (!$ok) {
                debugging('Requested service ' . $permissionrequested . ' not included in tool type: ' . $typeid,
                    DEBUG_DEVELOPER);
            }
        }
        return $ok;

    }

    /**
     * get permissions from the config of the tool for that resource
     *
     * @param int $ltitype Type of LTI
     * @return array with the permissions related to this resource by the $ltitype or empty if none.
     */
    public function get_permissions($ltitype) {
        return array();
    }

    /**
     * Parse a value for custom parameter substitution variables.
     *
     * @param string $value String to be parsed
     *
     * @return string
     */
    public function parse_value($value) {

        return $value;

    }

    /**
     * Parse the template for variables.
     *
     * @return array
     */
    protected function parse_template() {

        if (empty($this->params)) {
            $this->params = array();
            if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
                $path = explode('/', $_SERVER['PATH_INFO']);
                $parts = explode('/', $this->get_template());
                for ($i = 0; $i < count($parts); $i++) {
                    if ((substr($parts[$i], 0, 1) == '{') && (substr($parts[$i], -1) == '}')) {
                        $value = '';
                        if ($i < count($path)) {
                            $value = $path[$i];
                        }
                        $this->params[substr($parts[$i], 1, -1)] = $value;
                    }
                }
            }
        }

        return $this->params;

    }

}

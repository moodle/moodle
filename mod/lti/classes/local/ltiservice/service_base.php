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
 * This file contains an abstract definition of an LTI service
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
require_once($CFG->dirroot . '/mod/lti/OAuthBody.php');

// TODO: Switch to core oauthlib once implemented - MDL-30149.
use moodle\mod\lti as lti;
use stdClass;


/**
 * The mod_lti\local\ltiservice\service_base class.
 *
 * @package    mod_lti
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class service_base {

    /** Label representing an LTI 2 message type */
    const LTI_VERSION2P0 = 'LTI-2p0';
    /** Service enabled */
    const SERVICE_ENABLED = 1;

    /** @var string ID for the service. */
    protected $id;
    /** @var string Human readable name for the service. */
    protected $name;
    /** @var boolean <code>true</code> if requests for this service do not need to be signed. */
    protected $unsigned;
    /** @var stdClass Tool proxy object for the current service request. */
    private $toolproxy;
    /** @var stdClass LTI type object for the current service request. */
    private $type;
    /** @var array LTI type config array for the current service request. */
    private $typeconfig;
    /** @var array Instances of the resources associated with this service. */
    protected $resources;


    /**
     * Class constructor.
     */
    public function __construct() {

        $this->id = null;
        $this->name = null;
        $this->unsigned = false;
        $this->toolproxy = null;
        $this->type = null;
        $this->typeconfig = null;
        $this->resources = null;

    }

    /**
     * Get the service ID.
     *
     * @return string
     */
    public function get_id() {

        return $this->id;

    }

    /**
     * Get the service compoent ID.
     *
     * @return string
     */
    public function get_component_id() {

        return 'ltiservice_' . $this->id;

    }

    /**
     * Get the service name.
     *
     * @return string
     */
    public function get_name() {

        return $this->name;

    }

    /**
     * Get whether the service requests need to be signed.
     *
     * @return boolean
     */
    public function is_unsigned() {

        return $this->unsigned;

    }

    /**
     * Get the tool proxy object.
     *
     * @return stdClass
     */
    public function get_tool_proxy() {

        return $this->toolproxy;

    }

    /**
     * Set the tool proxy object.
     *
     * @param object $toolproxy The tool proxy for this service request
     *
     * @var stdClass
     */
    public function set_tool_proxy($toolproxy) {

        $this->toolproxy = $toolproxy;

    }

    /**
     * Get the type object.
     *
     * @return stdClass
     */
    public function get_type() {

        return $this->type;

    }

    /**
     * Set the LTI type object.
     *
     * @param object $type The LTI type for this service request
     *
     * @var stdClass
     */
    public function set_type($type) {

        $this->type = $type;

    }

    /**
     * Get the type config array.
     *
     * @return array|null
     */
    public function get_typeconfig() {

        return $this->typeconfig;

    }

    /**
     * Set the LTI type config object.
     *
     * @param array $typeconfig The LTI type config for this service request
     *
     * @var array
     */
    public function set_typeconfig($typeconfig) {

        $this->typeconfig = $typeconfig;

    }

    /**
     * Get the resources for this service.
     *
     * @return resource_base[]
     */
    abstract public function get_resources();

    /**
     * Get the scope(s) permitted for this service.
     *
     * A null value indicates that no scopes are required to access the service.
     *
     * @return array|null
     */
    public function get_permitted_scopes() {
        return null;
    }

    /**
     * Returns the configuration options for this service.
     *
     * @param \MoodleQuickForm $mform Moodle quickform object definition
     */
    public function get_configuration_options(&$mform) {

    }

    /**
     * Called when a new LTI Instance is added.
     *
     * @param object $lti LTI Instance.
     */
    public function instance_added(object $lti): void {

    }

    /**
     * Called when a new LTI Instance is updated.
     *
     * @param object $lti LTI Instance.
     */
    public function instance_updated(object $lti): void {

    }

    /**
     * Called when a new LTI Instance is deleted.
     *
     * @param int $id LTI Instance.
     */
    public function instance_deleted(int $id): void {

    }

    /**
     * Set the form data when displaying the LTI Instance form.
     *
     * @param object $defaultvalues Default form values.
     */
    public function set_instance_form_values(object $defaultvalues): void {

    }

    /**
     * Return an array with the names of the parameters that the service will be saving in the configuration
     *
     * @return array  Names list of the parameters that the service will be saving in the configuration
     * @deprecated since Moodle 3.7 - please do not use this function any more.
     */
    public function get_configuration_parameter_names() {
        debugging('get_configuration_parameter_names() has been deprecated.', DEBUG_DEVELOPER);
        return array();
    }

    /**
     * Default implementation will check for the existence of at least one mod_lti entry for that tool and context.
     *
     * It may be overridden if other inferences can be done.
     *
     * Ideally a Site Tool should be explicitly engaged with a course, the check on the presence of a link is a proxy
     * to infer a Site Tool engagement until an explicit Site Tool - Course relationship exists.
     *
     * @param int $typeid The tool lti type id.
     * @param int $courseid The course id.
     * @return bool returns True if tool is used in context, false otherwise.
     */
    public function is_used_in_context($typeid, $courseid) {
        global $DB;

        $ok = $DB->record_exists('lti', array('course' => $courseid, 'typeid' => $typeid));
        return $ok || $DB->record_exists('lti_types', array('course' => $courseid, 'id' => $typeid));
    }

    /**
     * Checks if there is a site tool or a course tool for this site.
     *
     * @param int $typeid The tool lti type id.
     * @param int $courseid The course id.
     * @return bool returns True if tool is allowed in context, false otherwise.
     */
    public function is_allowed_in_context($typeid, $courseid) {
        global $DB;

        // Check if it is a Course tool for this course or a Site tool.
        $type = $DB->get_record('lti_types', array('id' => $typeid));

        return $type && ($type->course == $courseid || $type->course == SITEID);
    }

    /**
     * Return an array of key/values to add to the launch parameters.
     *
     * @param string $messagetype  'basic-lti-launch-request' or 'ContentItemSelectionRequest'.
     * @param string $courseid     The course id.
     * @param string $userid       The user id.
     * @param string $typeid       The tool lti type id.
     * @param string $modlti       The id of the lti activity.
     *
     * The type is passed to check the configuration and not return parameters for services not used.
     *
     * @return array Key/value pairs to add as launch parameters.
     */
    public function get_launch_parameters($messagetype, $courseid, $userid, $typeid, $modlti = null) {
        return array();
    }

    /**
     * Get the path for service requests.
     *
     * @return string
     */
    public static function get_service_path() {

        $url = new \moodle_url('/mod/lti/services.php');

        return $url->out(false);

    }

    /**
     * Parse a string for custom substitution parameter variables supported by this service's resources.
     *
     * @param string $value  Value to be parsed
     *
     * @return string
     */
    public function parse_value($value) {

        if (empty($this->resources)) {
            $this->resources = $this->get_resources();
        }
        if (!empty($this->resources)) {
            foreach ($this->resources as $resource) {
                $value = $resource->parse_value($value);
            }
        }

        return $value;

    }

    /**
     * Check that the request has been properly signed and is permitted.
     *
     * @param string $typeid    LTI type ID
     * @param string $body      Request body (null if none)
     * @param string[] $scopes  Array of required scope(s) for incoming request
     *
     * @return boolean
     */
    public function check_tool($typeid, $body = null, $scopes = null) {

        $ok = true;
        $toolproxy = null;
        $consumerkey = lti\get_oauth_key_from_headers($typeid, $scopes);
        if ($consumerkey === false) {
            $ok = $this->is_unsigned();
        } else {
            if (empty($typeid) && is_int($consumerkey)) {
                $typeid = $consumerkey;
            }
            if (!empty($typeid)) {
                $this->type = lti_get_type($typeid);
                $this->typeconfig = lti_get_type_config($typeid);
                $ok = !empty($this->type->id);
                if ($ok && !empty($this->type->toolproxyid)) {
                    $this->toolproxy = lti_get_tool_proxy($this->type->toolproxyid);
                }
            } else {
                $toolproxy = lti_get_tool_proxy_from_guid($consumerkey);
                if ($toolproxy !== false) {
                    $this->toolproxy = $toolproxy;
                }
            }
        }
        if ($ok && is_string($consumerkey)) {
            if (!empty($this->toolproxy)) {
                $key = $this->toolproxy->guid;
                $secret = $this->toolproxy->secret;
            } else {
                $key = $this->typeconfig['resourcekey'];
                $secret = $this->typeconfig['password'];
            }
            if (!$this->is_unsigned() && ($key == $consumerkey)) {
                $ok = $this->check_signature($key, $secret, $body);
            } else {
                $ok = $this->is_unsigned();
            }
        }

        return $ok;

    }

    /**
     * Check that the request has been properly signed.
     *
     * @param string $toolproxyguid  Tool Proxy GUID
     * @param string $body           Request body (null if none)
     *
     * @return boolean
     * @deprecated since Moodle 3.7 MDL-62599 - please do not use this function any more.
     * @see service_base::check_tool()
     */
    public function check_tool_proxy($toolproxyguid, $body = null) {

        debugging('check_tool_proxy() is deprecated to allow LTI 1 connections to support services. ' .
                  'Please use service_base::check_tool() instead.', DEBUG_DEVELOPER);
        $ok = false;
        $toolproxy = null;
        $consumerkey = lti\get_oauth_key_from_headers();
        if (empty($toolproxyguid)) {
            $toolproxyguid = $consumerkey;
        }

        if (!empty($toolproxyguid)) {
            $toolproxy = lti_get_tool_proxy_from_guid($toolproxyguid);
            if ($toolproxy !== false) {
                if (!$this->is_unsigned() && ($toolproxy->guid == $consumerkey)) {
                    $ok = $this->check_signature($toolproxy->guid, $toolproxy->secret, $body);
                } else {
                    $ok = $this->is_unsigned();
                }
            }
        }
        if ($ok) {
            $this->toolproxy = $toolproxy;
        }

        return $ok;

    }

    /**
     * Check that the request has been properly signed.
     *
     * @param int $typeid The tool id
     * @param int $courseid The course we are at
     * @param string $body Request body (null if none)
     *
     * @return bool
     * @deprecated since Moodle 3.7 MDL-62599 - please do not use this function any more.
     * @see service_base::check_tool()
     */
    public function check_type($typeid, $courseid, $body = null) {
        debugging('check_type() is deprecated to allow LTI 1 connections to support services. ' .
                  'Please use service_base::check_tool() instead.', DEBUG_DEVELOPER);
        $ok = false;
        $tool = null;
        $consumerkey = lti\get_oauth_key_from_headers();
        if (empty($typeid)) {
            return $ok;
        } else if ($this->is_allowed_in_context($typeid, $courseid)) {
            $tool = lti_get_type_type_config($typeid);
            if ($tool !== false) {
                if (!$this->is_unsigned() && ($tool->lti_resourcekey == $consumerkey)) {
                    $ok = $this->check_signature($tool->lti_resourcekey, $tool->lti_password, $body);
                } else {
                    $ok = $this->is_unsigned();
                }
            }
        }
        return $ok;
    }

    /**
     * Check the request signature.
     *
     * @param string $consumerkey    Consumer key
     * @param string $secret         Shared secret
     * @param string $body           Request body
     *
     * @return boolean
     */
    private function check_signature($consumerkey, $secret, $body) {

        $ok = true;
        try {
            // TODO: Switch to core oauthlib once implemented - MDL-30149.
            lti\handle_oauth_body_post($consumerkey, $secret, $body);
        } catch (\Exception $e) {
            debugging($e->getMessage() . "\n");
            $ok = false;
        }

        return $ok;

    }
}
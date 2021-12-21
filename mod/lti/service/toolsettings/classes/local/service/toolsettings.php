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
 * This file contains a class definition for the Tool Settings service
 *
 * @package    ltiservice_toolsettings
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_toolsettings\local\service;

defined('MOODLE_INTERNAL') || die();

/**
 * A service implementing Tool Settings.
 *
 * @package    ltiservice_toolsettings
 * @since      Moodle 2.8
 * @copyright  2014 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toolsettings extends \mod_lti\local\ltiservice\service_base {

    /** Scope for managing tool settings */
    const SCOPE_TOOL_SETTINGS = 'https://purl.imsglobal.org/spec/lti-ts/scope/toolsetting';

    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->id = 'toolsettings';
        $this->name = 'Tool Settings';

    }

    /**
     * Get the resources for this service.
     *
     * @return array
     */
    public function get_resources() {

        if (empty($this->resources)) {
            $this->resources = array();
            $this->resources[] = new \ltiservice_toolsettings\local\resources\systemsettings($this);
            $this->resources[] = new \ltiservice_toolsettings\local\resources\contextsettings($this);
            $this->resources[] = new \ltiservice_toolsettings\local\resources\linksettings($this);
        }

        return $this->resources;

    }

    /**
     * Get the scope(s) permitted for the tool relevant to this service.
     *
     * @return array
     */
    public function get_permitted_scopes() {

        $scopes = array();
        $ok = !empty($this->get_type());
        if ($ok && isset($this->get_typeconfig()[$this->get_component_id()]) &&
            ($this->get_typeconfig()[$this->get_component_id()] == parent::SERVICE_ENABLED)) {
            $scopes[] = self::SCOPE_TOOL_SETTINGS;
        }

        return $scopes;

    }

    /**
     * Get the scope(s) defined this service.
     *
     * @return array
     */
    public function get_scopes() {
        return [self::SCOPE_TOOL_SETTINGS];
    }

    /**
     * Get the distinct settings from each level by removing any duplicates from higher levels.
     *
     * @param array $systemsettings   System level settings
     * @param array $contextsettings  Context level settings
     * @param array $linksettings      Link level settings
     */
    public static function distinct_settings(&$systemsettings, &$contextsettings, $linksettings) {

        if (!empty($systemsettings)) {
            foreach ($systemsettings as $key => $value) {
                if ((!empty($contextsettings) && array_key_exists($key, $contextsettings)) ||
                    (!empty($linksettings) && array_key_exists($key, $linksettings))) {
                    unset($systemsettings[$key]);
                }
            }
        }
        if (!empty($contextsettings)) {
            foreach ($contextsettings as $key => $value) {
                if (!empty($linksettings) && array_key_exists($key, $linksettings)) {
                    unset($contextsettings[$key]);
                }
            }
        }
    }

    /**
     * Get the JSON representation of the settings.
     *
     * @param array $settings        Settings
     * @param boolean $simpleformat  <code>true</code> if simple JSON is to be returned
     * @param string $type           JSON-LD type
     * @param \mod_lti\local\ltiservice\resource_base $resource       Resource handling the request
     *
     * @return string
     */
    public static function settings_to_json($settings, $simpleformat, $type, $resource) {

        $json = '';
        if (!empty($resource)) {
            $indent = '';
            if (!$simpleformat) {
                $json .= "    {\n      \"@type\":\"{$type}\",\n";
                $json .= "      \"@id\":\"{$resource->get_endpoint()}\",\n";
                $json .= "      \"custom\":{";
                $indent = '      ';
            }
            $isfirst = true;
            if (!empty($settings)) {
                foreach ($settings as $key => $value) {
                    if (!$isfirst) {
                        $json .= ',';
                    } else {
                        $isfirst = false;
                    }
                    $json .= "\n{$indent}  \"{$key}\":\"{$value}\"";
                }
            }
            if (!$simpleformat) {
                $json .= "\n{$indent}}\n    }";
            }
        }

        return $json;

    }

    /**
     * Adds form elements for membership add/edit page.
     *
     * @param \MoodleQuickForm $mform
     */
    public function get_configuration_options(&$mform) {
        $elementname = $this->get_component_id();
        $options = [
            get_string('notallow', $this->get_component_id()),
            get_string('allow', $this->get_component_id())
        ];

        $mform->addElement('select', $elementname, get_string($elementname, $this->get_component_id()), $options);
        $mform->setType($elementname, 'int');
        $mform->setDefault($elementname, 0);
        $mform->addHelpButton($elementname, $elementname, $this->get_component_id());
    }

    /**
     * Return an array of key/values to add to the launch parameters.
     *
     * @param string $messagetype 'basic-lti-launch-request' or 'ContentItemSelectionRequest'.
     * @param string $courseid The course id.
     * @param string $user The user id.
     * @param string $typeid The tool lti type id.
     * @param string $modlti The id of the lti activity.
     *
     * The type is passed to check the configuration
     * and not return parameters for services not used.
     *
     * @return array of key/value pairs to add as launch parameters.
     */
    public function get_launch_parameters($messagetype, $courseid, $user, $typeid, $modlti = null) {
        global $COURSE;

        $launchparameters = array();
        $tool = lti_get_type_type_config($typeid);
        if (isset($tool->{$this->get_component_id()})) {
            if ($tool->{$this->get_component_id()} == self::SERVICE_ENABLED && $this->is_used_in_context($typeid, $courseid)) {
                $launchparameters['system_setting_url'] = '$ToolProxy.custom.url';
                $launchparameters['context_setting_url'] = '$ToolProxyBinding.custom.url';
                if ($messagetype === 'basic-lti-launch-request') {
                    $launchparameters['link_setting_url'] = '$LtiLink.custom.url';
                }
            }
        }
        return $launchparameters;
    }

}

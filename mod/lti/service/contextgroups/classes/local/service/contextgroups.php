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
 * This file contains a class definition for the Context Groups service
 *
 * @package    ltiservice_contextgroups
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltiservice_contextgroups\local\service;

defined('MOODLE_INTERNAL') || die();

/**
 * A service implementing Context Groups.
 *
 * @package    ltiservice_contextgroups
 * @since      Moodle 3.12
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextgroups extends \mod_lti\local\ltiservice\service_base {

    /** Scope for reading course group data */
    const SCOPE_CONTEXT_GROUP_READ = 'https://purl.imsglobal.org/spec/lti-gs/scope/contextgroup.readonly';

    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->id = 'contextgroups';
        $this->name = get_string($this->get_component_id(), $this->get_component_id());

    }

    /**
     * Get the resources for this service.
     *
     * @return array
     */
    public function get_resources() {

        if (empty($this->resources)) {
            $this->resources = array();
            $this->resources[] = new \ltiservice_contextgroups\local\resources\groups($this);
            $this->resources[] = new \ltiservice_contextgroups\local\resources\groupsets($this);
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
            $scopes[] = self::SCOPE_CONTEXT_GROUP_READ;
        }

        return $scopes;

    }

    /**
     * Get the scope(s) defined by this service.
     *
     * @return array
     */
    public function get_scopes() {
        return [self::SCOPE_CONTEXT_GROUP_READ];
    }

    /**
     * Adds form elements for context groups add/edit page.
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
            if ($tool->{$this->get_component_id()} == parent::SERVICE_ENABLED && $this->is_used_in_context($typeid, $courseid)) {
                $launchparameters['context_groups_url'] = '$ToolProxyBinding.groups.url';
                $launchparameters['context_group_sets_url'] = '$ToolProxyBinding.groupsets.url';
                $launchparameters['context_groups_versions'] = '1.0';
            }
        }
        return $launchparameters;
    }

}

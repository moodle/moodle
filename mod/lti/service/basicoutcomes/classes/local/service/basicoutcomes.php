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
 * This file contains a class definition for the Basic Outcomes service
 *
 * @package    ltiservice_basicoutcomes
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace ltiservice_basicoutcomes\local\service;

defined('MOODLE_INTERNAL') || die();

/**
 * A service implementing Basic Outcomes.
 *
 * @package    ltiservice_basicoutcomes
 * @since      Moodle 3.7
 * @copyright  2019 Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class basicoutcomes extends \mod_lti\local\ltiservice\service_base {

    /** Scope for accessing the service */
    const SCOPE_BASIC_OUTCOMES = 'https://purl.imsglobal.org/spec/lti-bo/scope/basicoutcome';

    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->id = 'basicoutcomes';
        $this->name = 'Basic Outcomes';

    }

    /**
     * Get the resources for this service.
     *
     * @return array
     */
    public function get_resources() {

        if (empty($this->resources)) {
            $this->resources = array();
            $this->resources[] = new \ltiservice_basicoutcomes\local\resources\basicoutcomes($this);
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
        if (!isset($this->get_typeconfig()['acceptgrades']) || ($this->get_typeconfig()['acceptgrades'] != LTI_SETTING_NEVER)) {
            $scopes[] = self::SCOPE_BASIC_OUTCOMES;
        }

        return $scopes;

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
            if ($tool->{$this->get_component_id()} == parent::SERVICE_ENABLED) {
                if ($messagetype === 'basic-lti-launch-request') {
                    $launchparameters['lis_outcomes_service_url'] = '$BasicOutcome.url';
                    $launchparameters['lis_result_sourcedid'] = '$BasicOutcome.sourcedId';
                }
            }
        }
        return $launchparameters;
    }

}

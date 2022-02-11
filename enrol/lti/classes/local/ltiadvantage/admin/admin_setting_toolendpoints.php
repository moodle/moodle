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

namespace enrol_lti\local\ltiadvantage\admin;

use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\context_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\resource_link_repository;
use enrol_lti\local\ltiadvantage\repository\user_repository;
use enrol_lti\local\ltiadvantage\service\application_registration_service;

/**
 * The admin_setting_toolendpoints class, for rendering a table of tool endpoints.
 *
 * This setting is useful for LTI 1.3 only.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_toolendpoints extends \admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('enrol_lti_tool_endpoints', get_string('toolendpoints', 'enrol_lti'), '', '');
    }

    /**
     * Always returns true, does nothing.
     *
     * @return bool true.
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing.
     *
     * @return bool true.
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything.
     *
     * @param string|array $data the data
     * @return string Always returns ''.
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Checks if $query is one of the available external services
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        // Let the search match any of the horizontal table headings.
        $strings = [
            get_string('toolurl', 'enrol_lti'),
            get_string('loginurl', 'enrol_lti'),
            get_string('jwksurl', 'enrol_lti'),
            get_string('deeplinkingurl', 'enrol_lti')
        ];

        foreach ($strings as $str) {
            if (stripos($str, $query) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the HTML to display the table.
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $PAGE, $CFG;

        $appregservice = new application_registration_service(
            new application_registration_repository(),
            new deployment_repository(),
            new resource_link_repository(),
            new context_repository(),
            new user_repository()
        );
        $regurl = $appregservice->get_registration_url();
        $expiryinfo = $regurl ? get_string('registrationurlexpiry', 'enrol_lti',
            date('H:i, M dS, Y', $regurl->get_expiry_time())) : null;

        $endpoints = [
            'dynamic_registration_info' => get_string(
                'registrationurlinfomessage',
                'enrol_lti',
                get_docs_url('Publish_as_LTI_tool')
            ),
            'dynamic_registration_url' => [
                'name' => get_string('registrationurl', 'enrol_lti'),
                'url' => $regurl,
                'expiryinfo' => $expiryinfo,
                'id' => uniqid()
            ],
            'manual_registration_info' => get_string('endpointltiversionnotice', 'enrol_lti'),
            'manual_registration_urls' => [
                [
                    'name' => get_string('toolurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/launch.php',
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('loginurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/login.php',
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('jwksurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/jwks.php',
                    'id' => uniqid()
                ],
                [
                    'name' => get_string('deeplinkingurl', 'enrol_lti'),
                    'url' => $CFG->wwwroot . '/enrol/lti/launch_deeplink.php',
                    'id' => uniqid()
                ],
            ],
        ];

        $renderer = $PAGE->get_renderer('enrol_lti');
        $return = $renderer->render_admin_setting_tool_endpoints($endpoints);
        return highlight($query, $return);
    }
}

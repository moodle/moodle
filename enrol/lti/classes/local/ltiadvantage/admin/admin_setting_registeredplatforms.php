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

/**
 * The admin_setting_registeredplatforms class, for rendering a table of platforms which have been registered.
 *
 * This setting is useful for LTI 1.3 only.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_registeredplatforms extends \admin_setting {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('enrol_lti_tool_registered_platforms', get_string('registeredplatforms', 'enrol_lti'), '',
            '');
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

        $appregistrationrepo = new application_registration_repository();
        $registrations = $appregistrationrepo->find_all();
        foreach ($registrations as $reg) {
            if (stripos($reg->get_name(), $query) !== false) {
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
        global $PAGE;

        $appregistrationrepo = new application_registration_repository();
        $renderer = $PAGE->get_renderer('enrol_lti');
        $return = $renderer->render_admin_setting_registered_platforms($appregistrationrepo->find_all());
        return highlight($query, $return);
    }
}

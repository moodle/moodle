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
 * This file contains helper classes for testing the web service and external files.
 *
 * @package    core_webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper base class for external tests. Helpfull to test capabilities.
 *
 * @package    core_webservice
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class externallib_advanced_testcase extends advanced_testcase {

    /**
     * Assign a capability to $USER
     * The function creates a student $USER if $USER->id is empty
     *
     * @param string $capability capability name
     * @param int $contextid
     * @param int $roleid
     * @return int the role id - mainly returned for creation, so calling function can reuse it
     */
    public static function assignUserCapability($capability, $contextid, $roleid = null) {
        global $USER;

        // Create a new student $USER if $USER doesn't exist
        if (empty($USER->id)) {
            $user  = self::getDataGenerator()->create_user();
            self::setUser($user);
        }

        if (empty($roleid)) {
            $roleid = create_role('Dummy role', 'dummyrole', 'dummy role description');
        }

        assign_capability($capability, CAP_ALLOW, $roleid, $contextid);

        role_assign($roleid, $USER->id, $contextid);

        accesslib_clear_all_caches_for_unit_testing();

        return $roleid;
    }

    /**
     * Configure some filters for external tests.
     *
     * @param array $filters Filters to enable. Each filter should contain:
     *                           - name: name of the filter.
     *                           - state: the state of the filter.
     *                           - move: -1 means up, 0 means the same, 1 means down.
     *                           - applytostrings: true to apply the filter to content and headings, false for just content.
     */
    public static function configure_filters($filters) {
        global $CFG;

        $filterstrings = false;

        // Enable the filters.
        foreach ($filters as $filter) {
            $filter = (array) $filter;
            filter_set_global_state($filter['name'], $filter['state'], $filter['move']);
            filter_set_applies_to_strings($filter['name'], $filter['applytostrings']);

            $filterstrings = $filterstrings || $filter['applytostrings'];
        }

        // Set WS filtering.
        $wssettings = external_settings::get_instance();
        $wssettings->set_filter(true);

        // Reset filter caches.
        $filtermanager = filter_manager::instance();
        $filtermanager->reset_caches();

        if ($filterstrings) {
            // Don't strip tags in strings.
            $CFG->formatstringstriptags = false;
        }
    }

    /**
     * Unassign a capability to $USER.
     *
     * @param string $capability capability name.
     * @param int $contextid set the context id if you used assignUserCapability.
     * @param int $roleid set the role id if you used assignUserCapability.
     * @param int $courseid set the course id if you used getDataGenerator->enrol_users.
     * @param string $enrol set the enrol plugin name if you used getDataGenerator->enrol_users with a different plugin than 'manual'.
     */
    public static function unassignUserCapability($capability, $contextid = null, $roleid = null, $courseid = null, $enrol = 'manual') {
        global $DB;

        if (!empty($courseid)) {
            // Retrieve the role id.
            $instances = $DB->get_records('enrol', array('courseid'=>$courseid, 'enrol'=>$enrol));
            if (count($instances) != 1) {
                 throw new coding_exception('No found enrol instance for courseid: ' . $courseid . ' and enrol: ' . $enrol);
            }
            $instance = reset($instances);

            if (is_null($roleid) and $instance->roleid) {
                $roleid = $instance->roleid;
            }
        } else {
            if (empty($contextid) or empty($roleid)) {
                throw new coding_exception('unassignUserCapaibility requires contextid/roleid or courseid');
            }
        }

        unassign_capability($capability, $roleid, $contextid);

        accesslib_clear_all_caches_for_unit_testing();
    }
}


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
     * Unassign a capability to $USER
     *
     * @param string $capability capability name
     * @param int $contextid
     * @param int $roleid
     */
    public static function unassignUserCapability($capability, $contextid, $roleid) {
        global $USER;

        unassign_capability($capability, $roleid, $contextid);

        accesslib_clear_all_caches_for_unit_testing();
    }
}


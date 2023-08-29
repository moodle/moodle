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
 * Availability role - Frontend form
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
 *             on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_role;

/**
 * Availability role - Frontend form class
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
 *             on behalf of Alexander Bias Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {

    /**
     * Get the initial parameters needed for JavaScript.
     *
     * @param \stdClass          $course
     * @param \cm_info|null      $cm
     * @param \section_info|null $section
     *
     * @return array
     */
    protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {
        // Change to JS array format and return.
        $jsarray = array();
        $context = \context_course::instance($course->id);

        // Get all roles for course.
        $roles = $this->get_course_roles($context);

        foreach ($roles as $rec) {
            $jsarray[] = (object)array(
                'id' => $rec->id,
                'name' => $rec->localname
            );
        }

        return array($jsarray);
    }

    /**
     * Get the course roles for a specific context.
     *
     * @param \context          $context
     *
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function get_course_roles($context) {
        global $DB, $CFG;

        $contextroleids = get_roles_for_contextlevels(CONTEXT_COURSE);

        // Add guest role, if desired and guest role exists and is not yet included.
        $guestroleid = get_guest_role()->id;
        if (get_config('availability_role', 'setting_supportguestrole') &&
                !empty($guestroleid) &&
                !in_array($guestroleid, $contextroleids)) {

            $contextroleids[] = $guestroleid;
        }

        // Add role for users that are not logged in, if desired and this role exists and is not yet included.
        $notloggedinroleid = $CFG->notloggedinroleid;
        if (get_config('availability_role', 'setting_supportnotloggedinrole') &&
                !empty($notloggedinroleid) &&
                !in_array($notloggedinroleid, $contextroleids)) {

            $contextroleids[] = $notloggedinroleid;
        }

        $contextroles = $DB->get_records_list('role', 'id', $contextroleids, 'sortorder');

        foreach ($contextroles as $id => $role) {
            $role->localname = role_get_name($role, $context);
        }

        return $contextroles;
    }

    /**
     * Decides whether this plugin should be available in a given course. The plugin can do this depending on course or
     * system settings. Default returns true.
     *
     * @param \stdClass          $course
     * @param \cm_info|null      $cm
     * @param \section_info|null $section
     *
     * @return bool
     */
    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null) {
        return true;
    }
}

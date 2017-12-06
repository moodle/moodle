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
               on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_role;

defined('MOODLE_INTERNAL') || die();

/**
 * Availability role - Frontend form class
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
               on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {

    protected function get_javascript_init_params($course, \cm_info $cm = null, \section_info $section = null) {
        // Get all roles for course.
        // Change to JS array format and return.
        $jsarray = array();
        $context = \context_course::instance($course->id);

        $roles = $this->get_course_roles($context);

        return array($roles);
    }

    protected function get_course_roles($context) {
        global $DB;
        $roleswithnames = array();
        $contextroleids = get_roles_for_contextlevels(CONTEXT_COURSE);
        $contextroles = $DB->get_records_list('role', 'id', $contextroleids);
        foreach ($contextroles as $id => $role) {
            $roleswithnames[$id] = role_get_name($role, $context);
        }

        return $roleswithnames;
    }

    protected function allow_add($course, \cm_info $cm = null, \section_info $section = null) {
        return true;
    }
}

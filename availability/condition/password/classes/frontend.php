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
 * Availability password - Frontend form
 *
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_password;

/**
 * Code to control the settings form for password availability.
 * @package    availability_password
 * @copyright  2016 Davo Smith, Synergy Learning UK on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontend extends \core_availability\frontend {
    /**
     * Returns a list of language strings to pass to the javascript.
     * @return string[]
     */
    protected function get_javascript_strings() {
        return ['title', 'error_setpassword'];
    }

    /**
     * Check if the condition can be added.
     * Can only be added if this is not a section and if the user has the appropriate capability.
     *
     * @param \stdClass $course
     * @param \cm_info|null $cm
     * @param \section_info|null $section
     * @return bool
     */
    protected function allow_add($course, ?\cm_info $cm = null, ?\section_info $section = null) {
        if ($section !== null) {
            // Can only be added to modules, not sections.
            return false;
        }

        if ($cm) {
            $context = $cm->context;
        } else {
            $context = \context_course::instance($course->id);
        }
        return has_capability('availability/password:addinstance', $context);
    }
}

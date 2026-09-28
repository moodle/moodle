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

namespace local_requirelogin;

/**
 * Course entry for learners who are not enrolled yet.
 *
 * Hiding the catalog removes moodle/category:viewcourselist. Moodle then
 * treats every unenrolled course as hidden, including a visible course that
 * still accepts self enrolment. Learners must be able to open that enrol
 * page without the course appearing on Home.
 *
 * @package    local_requirelogin
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access {
    /**
     * True when this user may open the enrol page for a course they cannot browse.
     *
     * @param \stdClass $course
     */
    public static function may_reach_enrol_page(\stdClass $course): bool {
        global $DB;

        if (environment::is_automated_test() || during_initial_install()) {
            return false;
        }
        if (empty($course->id) || (int) $course->id === (int) SITEID || empty($course->visible)) {
            return false;
        }
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $plugin = enrol_get_plugin('self');
        if (!$plugin) {
            return false;
        }

        $instances = $DB->get_records('enrol', [
            'courseid' => $course->id,
            'enrol' => 'self',
            'status' => ENROL_INSTANCE_ENABLED,
        ]);
        foreach ($instances as $instance) {
            if ($plugin->can_self_enrol($instance, false) === true) {
                return true;
            }
        }
        return false;
    }
}

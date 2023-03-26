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
 * Delete courses async.
 *
 * @package    core_course
 * @copyright  2020, ISB Bayern
 * @author     Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\task;

/**
 * Delete courses async.
 *
 * @package    core_course
 * @copyright  2020, ISB Bayern
 * @author     Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_delete_asycn extends \core\task\adhoc_task {

    /**
     * Shown in admin screens.
     */
    public function get_name() {
        return get_string('recyclebintaskname', 'course');
    }

    /**
     * Runs the task.
     */
    public function execute() {
        global $DB;

        $customdata = $this->get_custom_data();
        $courseid = $customdata->courseid;

        // For some reasons it happens, that $courseid contains the whole course object. But we only need the courseid.
        if (is_object($courseid) && isset($courseid->id)) {
            $courseid = $courseid->id;
        }

        if (!$DB->record_exists('course', ['id' => $courseid])) {
            return;
        }

        if (delete_course($courseid, true, true)) {
            fix_course_sortorder();
        }
    }

}

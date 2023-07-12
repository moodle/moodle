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
 * Version details
 *
 * @package    format_remuiformat
 * @copyright  2021
 *  Wisdmlabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat\observers;

use stdClass;

defined('MOODLE_INTERNAL') || die();

class course_module {
    /**
     * Course module create event
     * @param \core\event\course_module_created $event Event Data
     */
    public static function viewed(\core\event\course_module_viewed $event) {
        global $DB;
        $data = $event->get_data();

        $courseid = $data['courseid']; // Course id.
        $userid = $data['userid']; // User id.
        $cmid = $data['contextinstanceid']; // Course module id.

        // Check if course exists.
        if (!$course = $DB->get_record('course', array('id' => $courseid))) {
            return;
        }

        // Check if format set to remuiformat.
        if ($course->format != 'remuiformat') {
            return;
        }

        // Update cm id if course and user record is present.
        if ($visit = $DB->get_record('remuiformat_course_visits', array('course' => $courseid, 'user' => $userid))) {
            $visit->cm = $cmid;
            $visit->timevisited = time();
            $DB->update_record('remuiformat_course_visits', $visit);
            return;
        }

        // Create new visit record.
        $visit = new stdClass;
        $visit->course = $courseid;
        $visit->user = $userid;
        $visit->cm = $cmid;
        $visit->timevisited = time();
        $DB->insert_record('remuiformat_course_visits', $visit);
    }
}

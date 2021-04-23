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
 * List of deprecated mod_lesson functions.
 *
 * @package   mod_lesson
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Obtains the automatic completion state for this lesson based on any conditions
 * in lesson settings.
 *
 * @deprecated since Moodle 3.11
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_lesson\completion\custom_completion
 * @param stdClass $course Course
 * @param cm_info|stdClass $cm course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function lesson_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // No need to call debugging here. Deprecation debugging notice already being called in \completion_info::internal_get_state().

    // Get lesson details.
    $lesson = $DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST);

    $result = $type; // Default return value.
    // If completion option is enabled, evaluate it and return true/false.
    if ($lesson->completionendreached) {
        $value = $DB->record_exists('lesson_timer', array('lessonid' => $lesson->id, 'userid' => $userid, 'completed' => 1));
        if ($type == COMPLETION_AND) {
            $result = $result && $value;
        } else {
            $result = $result || $value;
        }
    }
    if ($lesson->completiontimespent != 0) {
        $duration = $DB->get_field_sql(
            "SELECT SUM(lessontime - starttime)
               FROM {lesson_timer}
              WHERE lessonid = :lessonid
                    AND userid = :userid",
            array('userid' => $userid, 'lessonid' => $lesson->id));
        if (!$duration) {
            $duration = 0;
        }
        if ($type == COMPLETION_AND) {
            $result = $result && ($lesson->completiontimespent < $duration);
        } else {
            $result = $result || ($lesson->completiontimespent < $duration);
        }
    }
    return $result;
}

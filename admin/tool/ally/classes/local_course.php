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
 * Course updates local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

/**
 * Course updates local library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2019 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_course {
    /**
     * A message to send to Ally about a course being updated, created, or deleted.
     *
     * Warning: be very careful about editing this message.  It's used
     * for webservices and for pushed updates.
     *
     * @param \stdClass $event
     * @return array
     */
    public static function to_crud($event) {
        $result = [
            'event_name' => $event->name,
            'event_time' => local::iso_8601($event->time),
            'context_id' => $event->courseid,
        ];

        if (isset($event->sourcecourseid)) {
            $result['source_context_id'] = $event->sourcecourseid;
        }

        return $result;
    }

    /**
     * Get context ids for course modules that have been soft deleted in a specific course.
     * @param int $courseid
     * @throws \dml_exception
     */
    public static function course_cm_soft_delete_contextids(int $courseid) {
        global $DB;
        $sql = "    SELECT cx.id
                      FROM {course_modules} cm
                      JOIN {context} cx ON cx.instanceid = cm.id AND cx.contextlevel = ?
                     WHERE cm.course = ? AND cm.deletioninprogress > 0
            ";
        return array_keys($DB->get_records_sql($sql, [CONTEXT_MODULE, $courseid]));
    }
}


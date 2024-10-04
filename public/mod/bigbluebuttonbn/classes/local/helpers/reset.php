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
 * The mod_bigbluebuttonbn resetting instance helper
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */

namespace mod_bigbluebuttonbn\local\helpers;

use context_module;
use core_tag_tag;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\recording;

/**
 * Utility class for resetting instance routines helper
 *
 * @package mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset {

    /**
     * Used by the reset_course_userdata for deleting recordings
     *
     * This will delete recordings in the database and not in the remote BBB server.
     *
     * @param int $courseid
     */
    public static function reset_recordings(int $courseid): void {
        // Criteria for search : courseid or bigbluebuttonbn=null or subset=false or includedeleted=true.
        $recordings = recording::get_recordings_for_course(
            $courseid,
            [], // Exclude itself.
            false,
            true
        );
        if ($recordings) {
            // Remove all the recordings.
            foreach ($recordings as $recording) {
                $recording->delete();
            }
        }
    }

    /**
     * Used by the reset_course_userdata for deleting tags linked to bigbluebuttonbn instances in the course.
     *
     * @param int $courseid
     */
    public static function reset_tags(int $courseid): void {
        global $DB;
        // Remove all the tags linked to the room/activities in this course.
        if ($bigbluebuttonbns = $DB->get_records('bigbluebuttonbn', ['course' => $courseid])) {
            foreach ($bigbluebuttonbns as $bigbluebuttonbn) {
                if (!$cm = get_coursemodule_from_instance('bigbluebuttonbn', $bigbluebuttonbn->id, $courseid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                core_tag_tag::delete_instances('mod_bigbluebuttonbn', null, $context->id);
            }
        }
    }

    /**
     * Used by the reset_course_userdata for deleting events linked to bigbluebuttonbn instances in the course.
     *
     * @param string $courseid
     * @return bool status
     */
    public static function reset_events($courseid) {
        global $DB;
        // Remove all the events.
        return $DB->delete_records('event', ['modulename' => 'bigbluebuttonbn', 'courseid' => $courseid]);
    }

    /**
     * Returns status used on every defined reset action.
     *
     * @param string $item
     * @return array status array
     */
    public static function reset_getstatus(string $item): array {
        return ['component' => get_string('modulenameplural', 'bigbluebuttonbn'),
            'item' => get_string("removed{$item}", 'bigbluebuttonbn'),
            'error' => false];
    }

    /**
     * Define items to be reset by course/reset.php
     *
     * @return array
     */
    public static function reset_course_items(): array {
        $items = ["events" => 0, "tags" => 0, "logs" => 0];
        // Include recordings only if enabled.
        if ((boolean) config::recordings_enabled()) {
            $items["recordings"] = 0;
        }
        return $items;
    }

    /**
     * Reset logs for each BBB instance of this course
     *
     * @param int $courseid
     * @return bool status
     */
    public static function reset_logs(int $courseid) {
        global $DB;
        return $DB->delete_records('bigbluebuttonbn_logs', ['courseid' => $courseid]);
    }
}

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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\notifier;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\event_notification;

/*
 * This class' methods are called from the event_observer based on the type of
 * event that occured. It is responsible for finding any relevant, active
 * event notifications, and triggering the notification to the appropriate user
 */
class event_notification_handler {

    public static function course_entered($userid, $courseid) {
        // Make sure the course is still active?
        // Make sure the user is still active?
        // Make sure the user is still enrolled in course?

        // Get any relevant event notifications for this course.
        $eventnotifications = self::get_event_notifications_for_course('course-entered', $courseid);

        // Attempt to notify each event notification, if appropriate.
        foreach ($eventnotifications as $eventnotification) {
            $eventnotification->notify($userid);
        }
    }

    /**
     * Returns all active event notifications of the given model for the given course
     *
     * @param  string  $model
     * @param  int     $courseid
     * @return array (event_notification)
     */
    private static function get_event_notifications_for_course($model, $courseid) {
        global $DB;

        $recordset = $DB->get_recordset_sql("
            SELECT en.* FROM {block_quickmail_event_notifs} en
            JOIN {block_quickmail_notifs} n on en.notification_id = n.id
            WHERE en.model = ?
            AND n.course_id = ?
            AND n.is_enabled = 1
            AND n.timedeleted = 0", [$model, $courseid]);

        // Iterate through recordset, instantiate persistents, add to array.
        $data = [];
        foreach ($recordset as $record) {
            $data[] = new event_notification(0, $record);
        }
        $recordset->close();

        return $data;
    }

}

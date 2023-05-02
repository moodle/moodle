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
 * Observer functions used by the calendar sync feature.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\calsync;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/filelib.php');

/**
 * Observer functions used by the calendar sync feature.
 */
class observers {
    /** @var bool Flag indicating whether we're currently importing events. */
    public static $importingevents = false;

    /**
     * Set class static flag indicating whether we're currently importing events.
     *
     * @param bool $status Import status.
     */
    public static function set_event_import($status) {
        static::$importingevents = $status;
    }

    /**
     * Handle user_enrolment_deleted event to clean up calendar subscriptions.
     *
     * @param \core\event\user_enrolment_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_user_enrolment_deleted(\core\event\user_enrolment_deleted $event) {
        global $DB;
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        if (empty($userid) || empty($courseid)) {
            return true;
        }

        // Clean up calendar subscriptions.
        $calsubparams = ['user_id' => $userid, 'caltype' => 'course', 'caltypeid' => $courseid];
        $subscriptions = $DB->get_recordset('local_o365_calsub', $calsubparams);
        foreach ($subscriptions as $subscription) {
            $eventdata = [
                'objectid' => $subscription->id,
                'userid' => $userid,
                'other' => [
                    'caltype' => 'course',
                    'caltypeid' => $courseid
                ]
            ];
            $event = \local_o365\event\calendar_unsubscribed::create($eventdata);
            $event->trigger();
        }
        $subscriptions->close();
        $DB->delete_records('local_o365_calsub', $calsubparams);
        return true;
    }

    /**
     * Handle course_deleted event
     *
     * Does the following:
     *     - clean up calendar subscriptions.
     *
     * @param \core\event\course_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_course_deleted(\core\event\course_deleted $event) {
        global $DB;
        $courseid = $event->objectid;
        $DB->delete_records('local_o365_calsub', ['caltype' => 'course', 'caltypeid' => $courseid]);
        return true;
    }

    /**
     * Handle a calendar_event_created event.
     *
     * @param \core\event\calendar_event_created $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_calendar_event_created(\core\event\calendar_event_created $event) {
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }
        if (static::$importingevents === true) {
            return true;
        }

        $calsync = new \local_o365\feature\calsync\main();
        return $calsync->create_outlook_event_from_moodle_event($event->objectid);
    }

    /**
     * Handle a calendar_event_updated event.
     *
     * @param \core\event\calendar_event_updated $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_calendar_event_updated(\core\event\calendar_event_updated $event) {
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }
        $calsync = new \local_o365\feature\calsync\main();
        return $calsync->update_outlook_event($event->objectid);
    }

    /**
     * Handle a calendar_event_deleted event.
     *
     * @param \core\event\calendar_event_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_calendar_event_deleted(\core\event\calendar_event_deleted $event) {
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }
        $calsync = new \local_o365\feature\calsync\main();
        return $calsync->delete_outlook_event($event->objectid);
    }

    /**
     * Handle calendar_subscribed event - queue calendar sync jobs for cron.
     *
     * @param \local_o365\event\calendar_subscribed $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_calendar_subscribed(\local_o365\event\calendar_subscribed $event) {
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }
        $eventdata = $event->get_data();
        $calsubscribe = new \local_o365\feature\calsync\task\syncoldevents();
        $calsubscribe->set_custom_data([
            'caltype' => $eventdata['other']['caltype'],
            'caltypeid' => ((isset($eventdata['other']['caltypeid'])) ? $eventdata['other']['caltypeid'] : 0),
            'userid' => $eventdata['userid'],
            'timecreated' => time(),
        ]);
        \core\task\manager::queue_adhoc_task($calsubscribe);
        return true;
    }

    /**
     * Handle calendar_unsubscribed event - queue calendar sync jobs for cron.
     *
     * @param \local_o365\event\calendar_unsubscribed $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_calendar_unsubscribed(\local_o365\event\calendar_unsubscribed $event) {
        if (\local_o365\utils::is_connected() !== true) {
            return false;
        }
        $eventdata = $event->get_data();
        $calunsubscribe = new \local_o365\feature\calsync\task\syncoldevents();
        $calunsubscribe->set_custom_data([
            'caltype' => $eventdata['other']['caltype'],
            'caltypeid' => ((isset($eventdata['other']['caltypeid'])) ? $eventdata['other']['caltypeid'] : 0),
            'userid' => $eventdata['userid'],
            'timecreated' => time(),
        ]);
        \core\task\manager::queue_adhoc_task($calunsubscribe);
        return true;
    }

    /**
     * Handle user_deleted event - clean up calendar subscriptions.
     *
     * @param \core\event\user_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_user_deleted(\core\event\user_deleted $event) {
        global $DB;
        $userid = $event->objectid;
        $DB->delete_records('local_o365_calsub', ['user_id' => $userid]);
        return true;
    }
}

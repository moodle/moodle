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

namespace mod_bigbluebuttonbn\local\helpers;

use calendar_event;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\logger;
use mod_bigbluebuttonbn\plugin;
use mod_bigbluebuttonbn\task\send_instance_update_notification;
use stdClass;

/**
 * Utility class for all instance (module) routines helper.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2021 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class mod_helper {

    /**
     * Runs any processes that must run before a bigbluebuttonbn insert/update.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    public static function process_pre_save(stdClass $bigbluebuttonbn) {
        self::process_pre_save_instance($bigbluebuttonbn);
        self::process_pre_save_checkboxes($bigbluebuttonbn);
        self::process_pre_save_common($bigbluebuttonbn);
        $bigbluebuttonbn->participants = htmlspecialchars_decode($bigbluebuttonbn->participants);
    }

    /**
     * Runs process for defining the instance (insert/update).
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_pre_save_instance(stdClass $bigbluebuttonbn): void {
        $bigbluebuttonbn->timemodified = time();
        if ((integer) $bigbluebuttonbn->instance == 0) {
            $bigbluebuttonbn->meetingid = 0;
            $bigbluebuttonbn->timecreated = time();
            $bigbluebuttonbn->timemodified = 0;
            // As it is a new activity, assign passwords.
            $bigbluebuttonbn->moderatorpass = plugin::random_password(12);
            $bigbluebuttonbn->viewerpass = plugin::random_password(12, $bigbluebuttonbn->moderatorpass);
        }
    }

    /**
     * Runs process for assigning default value to checkboxes.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_pre_save_checkboxes($bigbluebuttonbn) {
        if (!isset($bigbluebuttonbn->wait)) {
            $bigbluebuttonbn->wait = 0;
        }
        if (!isset($bigbluebuttonbn->record)) {
            $bigbluebuttonbn->record = 0;
        }
        if (!isset($bigbluebuttonbn->recordallfromstart)) {
            $bigbluebuttonbn->recordallfromstart = 0;
        }
        if (!isset($bigbluebuttonbn->recordhidebutton)) {
            $bigbluebuttonbn->recordhidebutton = 0;
        }
        if (!isset($bigbluebuttonbn->recordings_html)) {
            $bigbluebuttonbn->recordings_html = 0;
        }
        if (!isset($bigbluebuttonbn->recordings_deleted)) {
            $bigbluebuttonbn->recordings_deleted = 0;
        }
        if (!isset($bigbluebuttonbn->recordings_imported)) {
            $bigbluebuttonbn->recordings_imported = 0;
        }
        if (!isset($bigbluebuttonbn->recordings_preview)) {
            $bigbluebuttonbn->recordings_preview = 0;
        }
        if (!isset($bigbluebuttonbn->muteonstart)) {
            $bigbluebuttonbn->muteonstart = 0;
        }
        if (!isset($bigbluebuttonbn->disablecam)) {
            $bigbluebuttonbn->disablecam = 0;
        }
        if (!isset($bigbluebuttonbn->disablemic)) {
            $bigbluebuttonbn->disablemic = 0;
        }
        if (!isset($bigbluebuttonbn->disableprivatechat)) {
            $bigbluebuttonbn->disableprivatechat = 0;
        }
        if (!isset($bigbluebuttonbn->disablepublicchat)) {
            $bigbluebuttonbn->disablepublicchat = 0;
        }
        if (!isset($bigbluebuttonbn->disablenote)) {
            $bigbluebuttonbn->disablenote = 0;
        }
        if (!isset($bigbluebuttonbn->hideuserlist)) {
            $bigbluebuttonbn->hideuserlist = 0;
        }
        if (!isset($bigbluebuttonbn->lockedlayout)) {
            $bigbluebuttonbn->lockedlayout = 0;
        }
        if (!isset($bigbluebuttonbn->lockonjoin)) {
            $bigbluebuttonbn->lockonjoin = 0;
        }
        if (!isset($bigbluebuttonbn->lockonjoinconfigurable)) {
            $bigbluebuttonbn->lockonjoinconfigurable = 0;
        }
        if (!isset($bigbluebuttonbn->recordings_validate_url)) {
            $bigbluebuttonbn->recordings_validate_url = 1;
        }
    }

    /**
     * Runs process for wipping common settings when 'recordings only'.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_pre_save_common(stdClass $bigbluebuttonbn): void {
        // Make sure common settings are removed when 'recordings only'.
        if ($bigbluebuttonbn->type == instance::TYPE_RECORDING_ONLY) {
            $bigbluebuttonbn->groupmode = 0;
            $bigbluebuttonbn->groupingid = 0;
        }
    }

    /**
     * Runs any processes that must be run after a bigbluebuttonbn insert/update.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    public static function process_post_save(stdClass $bigbluebuttonbn): void {
        if (isset($bigbluebuttonbn->notification) && $bigbluebuttonbn->notification) {
            self::process_post_save_notification($bigbluebuttonbn);
        }
        self::process_post_save_event($bigbluebuttonbn);
        self::process_post_save_completion($bigbluebuttonbn);
    }

    /**
     * Generates a message on insert/update which is sent to all users enrolled.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_post_save_notification(stdClass $bigbluebuttonbn): void {
        $task = new send_instance_update_notification();
        $task->set_instance_id($bigbluebuttonbn->id);

        if (isset($bigbluebuttonbn->add) && !empty($bigbluebuttonbn->add)) {
            $task->set_update_type(send_instance_update_notification::TYPE_CREATED);
        } else {
            $task->set_update_type(send_instance_update_notification::TYPE_UPDATED);
        }

        \core\task\manager::queue_adhoc_task($task);
    }

    /**
     * Generates an event after a bigbluebuttonbn insert/update.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_post_save_event(stdClass $bigbluebuttonbn): void {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/calendar/lib.php');
        $eventid = $DB->get_field('event', 'id', [
            'modulename' => 'bigbluebuttonbn',
            'instance' => $bigbluebuttonbn->id,
            'eventtype' => logger::EVENT_MEETING_START
        ]);

        // Delete the event from calendar when/if openingtime is NOT set.
        if (!isset($bigbluebuttonbn->openingtime) || !$bigbluebuttonbn->openingtime) {
            if ($eventid) {
                $calendarevent = calendar_event::load($eventid);
                $calendarevent->delete();
            }
            return;
        }

        // Add event to the calendar as openingtime is set.
        $event = (object) [
            'eventtype' => logger::EVENT_MEETING_START,
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'name' => get_string('calendarstarts', 'bigbluebuttonbn', $bigbluebuttonbn->name),
            'description' => format_module_intro('bigbluebuttonbn', $bigbluebuttonbn, $bigbluebuttonbn->coursemodule, false),
            'format' => FORMAT_HTML,
            'courseid' => $bigbluebuttonbn->course,
            'groupid' => 0,
            'userid' => 0,
            'modulename' => 'bigbluebuttonbn',
            'instance' => $bigbluebuttonbn->id,
            'timestart' => $bigbluebuttonbn->openingtime,
            'timeduration' => 0,
            'timesort' => $bigbluebuttonbn->openingtime,
            'visible' => instance_is_visible('bigbluebuttonbn', $bigbluebuttonbn),
            'priority' => null,
        ];

        // Update the event in calendar when/if eventid was found.
        if ($eventid) {
            $event->id = $eventid;
            $calendarevent = calendar_event::load($eventid);
            $calendarevent->update($event);
            return;
        }
        calendar_event::create($event);
    }

    /**
     * Generates an event after a bigbluebuttonbn activity is completed.
     *
     * @param stdClass $bigbluebuttonbn BigBlueButtonBN form data
     **/
    protected static function process_post_save_completion(stdClass $bigbluebuttonbn): void {
        if (empty($bigbluebuttonbn->completionexpected)) {
            return;
        }
        \core_completion\api::update_completion_date_event(
            $bigbluebuttonbn->coursemodule,
            'bigbluebuttonbn',
            $bigbluebuttonbn->id,
            $bigbluebuttonbn->completionexpected
        );
    }
}

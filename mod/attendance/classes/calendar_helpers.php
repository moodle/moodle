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
 * Calendar related functions
 *
 * @package    mod_attendance
 * @copyright  2016 Vyacheslav Strelkov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/../../../calendar/lib.php');

/**
 * Create single calendar event bases on session data.
 *
 * @param stdClass $session initial sessions to take data from
 * @return bool result of calendar event creation
 */
function attendance_create_calendar_event(&$session) {
    global $DB;

    // We don't want to create multiple calendar events for 1 session.
    if ($session->caleventid) {
        return $session->caleventid;
    }
    if (empty(get_config('attendance', 'enablecalendar')) || $session->calendarevent === 0) {
        // Calendar events are not used, or event not required for this session.
        return true;
    }

    $attendance = $DB->get_record('attendance', array('id' => $session->attendanceid));

    $caleventdata = new stdClass();
    $caleventdata->name           = $attendance->name;
    $caleventdata->courseid       = $attendance->course;
    $caleventdata->groupid        = $session->groupid;
    $caleventdata->instance       = $session->attendanceid;
    $caleventdata->timestart      = $session->sessdate;
    $caleventdata->timeduration   = $session->duration;
    $caleventdata->description    = $session->description;
    $caleventdata->format         = $session->descriptionformat;
    $caleventdata->eventtype      = 'attendance';
    $caleventdata->timemodified   = time();
    $caleventdata->modulename     = 'attendance';

    if (!empty($session->groupid)) {
        $caleventdata->name .= " (". get_string('group', 'group') ." ". groups_get_group_name($session->groupid) .")";
    }

    $calevent = new stdClass();
    if ($calevent = calendar_event::create($caleventdata, false)) {
        $session->caleventid = $calevent->id;
        $DB->set_field('attendance_sessions', 'caleventid', $session->caleventid, array('id' => $session->id));
        return true;
    } else {
        return false;
    }
}

/**
 * Create multiple calendar events based on sessions data.
 *
 * @param array $sessionsids array of sessions ids
 */
function attendance_create_calendar_events($sessionsids) {
    global $DB;

    if (empty(get_config('attendance', 'enablecalendar'))) {
        // Calendar events are not used.
        return true;
    }

    $sessions = $DB->get_recordset_list('attendance_sessions', 'id', $sessionsids);

    foreach ($sessions as $session) {
        attendance_create_calendar_event($session);
        if ($session->caleventid) {
            $DB->update_record('attendance_sessions', $session);
        }
    }
}

/**
 * Update calendar event duration and date
 *
 * @param stdClass $session Session data
 * @return bool result of updating
 */
function attendance_update_calendar_event($session) {
    global $DB;

    $caleventid = $session->caleventid;
    $timeduration = $session->duration;
    $timestart = $session->sessdate;

    if (empty(get_config('attendance', 'enablecalendar'))) {
        // Calendar events are not used.
        return true;
    }

    // Should there even be an event?
    if ($session->calendarevent == 0) {
        if ($session->caleventid != 0) {
            // There is an existing event we should delete, calendarevent just got turned off.
            $DB->delete_records_list('event', 'id', array($caleventid));
            $session->caleventid = 0;
            $DB->update_record('attendance_sessions', $session);
            return true;
        } else {
            // This should be the common case when session does not want event.
            return true;
        }
    }

    // Do we need new event (calendarevent option has just been turned on)?
    if ($session->caleventid == 0) {
        return attendance_create_calendar_event($session);
    }

    // Boring update.
    $caleventdata = new stdClass();
    $caleventdata->timeduration   = $timeduration;
    $caleventdata->timestart      = $timestart;
    $caleventdata->timemodified   = time();
    $caleventdata->description    = $session->description;

    $calendarevent = calendar_event::load($caleventid);
    if ($calendarevent) {
        return $calendarevent->update($caleventdata) ? true : false;
    } else {
        return false;
    }
}

/**
 * Delete calendar events for sessions
 *
 * @param array $sessionsids array of sessions ids
 * @return bool result of updating
 */
function attendance_delete_calendar_events($sessionsids) {
    global $DB;
    $caleventsids = attendance_existing_calendar_events_ids($sessionsids);
    if ($caleventsids) {
        $DB->delete_records_list('event', 'id', $caleventsids);
    }

    $sessions = $DB->get_recordset_list('attendance_sessions', 'id', $sessionsids);
    foreach ($sessions as $session) {
        $session->caleventid = 0;
        $DB->update_record('attendance_sessions', $session);
    }
}

/**
 * Check if calendar events are created for given sessions
 *
 * @param array $sessionsids of sessions ids
 * @return array | bool array of existing calendar events or false if none found
 */
function attendance_existing_calendar_events_ids($sessionsids) {
    global $DB;
    $caleventsids = array_keys($DB->get_records_list('attendance_sessions', 'id', $sessionsids, '', 'caleventid'));
    $existingcaleventsids = array_filter($caleventsids);
    if (! empty($existingcaleventsids)) {
        return $existingcaleventsids;
    } else {
        return false;
    }
}
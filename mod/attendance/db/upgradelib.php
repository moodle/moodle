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
 * Helper functions to keep upgrade.php clean.
 *
 * @package   mod_attendance
 * @copyright 2016 Vyacheslav Strelkov <strelkov.vo@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to help upgrade old attendance records and create calendar events.
 */
function attendance_upgrade_create_calendar_events() {
    global $DB;

    $attendances = $DB->get_records('attendance', null, null, 'id, name, course');
    foreach ($attendances as $att) {
        $sessionsdata = $DB->get_records('attendance_sessions', array('attendanceid' => $att->id), null,
            'id, groupid, sessdate, duration, description, descriptionformat');
        foreach ($sessionsdata as $session) {
            $calevent = new stdClass();
            $calevent->name           = $att->name;
            $calevent->courseid       = $att->course;
            $calevent->groupid        = $session->groupid;
            $calevent->instance       = $att->id;
            $calevent->timestart      = $session->sessdate;
            $calevent->timeduration   = $session->duration;
            $calevent->eventtype      = 'attendance';
            $calevent->timemodified   = time();
            $calevent->modulename     = 'attendance';
            $calevent->description    = $session->description;
            $calevent->format         = $session->descriptionformat;

            $caleventid = $DB->insert_record('event', $calevent);
            $DB->set_field('attendance_sessions', 'caleventid', $caleventid, array('id' => $session->id));
        }
    }
}

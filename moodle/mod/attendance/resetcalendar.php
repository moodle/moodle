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
 * Reset Calendar events.
 *
 * @package    mod_attendance
 * @copyright  2017 onwards Dan Marsden http://danmarsden.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');
require_once($CFG->dirroot.'/mod/attendance/locallib.php');

$action = optional_param('action', '', PARAM_ALPHA);

admin_externalpage_setup('managemodules');
$context = context_system::instance();

// Check permissions.
require_capability('mod/attendance:viewreports', $context);

$exportfilename = 'attendance-absentee.csv';

$PAGE->set_url('/mod/attendance/resetcalendar.php');

$PAGE->set_heading($SITE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('resetcalendar', 'mod_attendance'));
$tabmenu = attendance_print_settings_tabs('resetcalendar');
echo $tabmenu;

if (get_config('attendance', 'enablecalendar')) {
    // Check to see if all sessions have calendar events.
    if ($action == 'create' && confirm_sesskey()) {
        $sessions = $DB->get_recordset('attendance_sessions',  array('caleventid' => 0));
        foreach ($sessions as $session) {
            attendance_create_calendar_event($session);
            if ($session->caleventid) {
                $DB->update_record('attendance_sessions', $session);
            }
        }
        $sessions->close();
        echo $OUTPUT->notification(get_string('eventscreated', 'mod_attendance'), 'notifysuccess');
    } else {
        if ($DB->record_exists('attendance_sessions', array('caleventid' => 0))) {
            $createurl = new moodle_url('/mod/attendance/resetcalendar.php', array('action' => 'create'));
            $returnurl = new moodle_url('/admin/settings.php', array('section' => 'modsettingattendance'));

            echo $OUTPUT->confirm(get_string('resetcaledarcreate', 'mod_attendance'), $createurl, $returnurl);
        } else {
            echo $OUTPUT->box(get_string("noeventstoreset", "mod_attendance"));
        }
    }
} else {
    if ($action == 'delete' && confirm_sesskey()) {
        $caleventids = $DB->get_records_select_menu('attendance_sessions', 'caleventid > 0', array(),
                                                     '', 'caleventid, caleventid as id2');
        $DB->delete_records_list('event', 'id', $caleventids);
        $DB->execute("UPDATE {attendance_sessions} set caleventid = 0");
        echo $OUTPUT->notification(get_string('eventsdeleted', 'mod_attendance'), 'notifysuccess');
    } else {
        // Check to see if there are any events that need to be deleted.
        if ($DB->record_exists_select('attendance_sessions', 'caleventid > 0')) {
            $deleteurl = new moodle_url('/mod/attendance/resetcalendar.php', array('action' => 'delete'));
            $returnurl = new moodle_url('/admin/settings.php', array('section' => 'modsettingattendance'));

            echo $OUTPUT->confirm(get_string('resetcaledardelete', 'mod_attendance'), $deleteurl, $returnurl);
        } else {
            echo $OUTPUT->box(get_string("noeventstoreset", "mod_attendance"));
        }
    }

}

echo $OUTPUT->footer();
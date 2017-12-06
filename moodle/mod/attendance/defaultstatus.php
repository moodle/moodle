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
 * Allows default status set to be modified.
 *
 * @package   mod_attendance
 * @copyright 2017 Dan Marsden http://danmarsden.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/attendance/lib.php');
require_once($CFG->dirroot.'/mod/attendance/locallib.php');

$action         = optional_param('action', null, PARAM_INT);
$statusid       = optional_param('statusid', null, PARAM_INT);
admin_externalpage_setup('managemodules');
$url = new moodle_url('/mod/attendance/defaultstatus.php', array('statusid' => $statusid, 'action' => $action));

// Check sesskey if we are performing an action.
if (!empty($action)) {
    require_sesskey();
}

$output = $PAGE->get_renderer('mod_attendance');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('defaultstatus', 'mod_attendance'));
$tabmenu = attendance_print_settings_tabs('defaultstatus');
echo $tabmenu;

// TODO: Would be good to combine this code block with the one in preferences to avoid duplication.
$errors = array();
switch ($action) {
    case mod_attendance_preferences_page_params::ACTION_ADD:
        $newacronym         = optional_param('newacronym', null, PARAM_TEXT);
        $newdescription     = optional_param('newdescription', null, PARAM_TEXT);
        $newgrade           = optional_param('newgrade', 0, PARAM_RAW);
        $newstudentavailability = optional_param('newstudentavailability', null, PARAM_INT);
        $newgrade = unformat_float($newgrade);

        // Default value uses setnumber/attendanceid = 0.
        $status = new stdClass();
        $status->attendanceid = 0;
        $status->acronym = $newacronym;
        $status->description = $newdescription;
        $status->grade = $newgrade;
        $status->studentavailability = $newstudentavailability;
        $status->setnumber = 0;
        attendance_add_status($status);

        break;
    case mod_attendance_preferences_page_params::ACTION_DELETE:
        $confirm    = optional_param('confirm', null, PARAM_INT);
        $statuses = attendance_get_statuses(0, false);
        $status = $statuses[$statusid];

        if (isset($confirm)) {
            attendance_remove_status($status);
            echo $OUTPUT->notification(get_string('statusdeleted', 'attendance'), 'success');
            break;
        }

        $message = get_string('deletecheckfull', '', get_string('variable', 'attendance'));
        $message .= str_repeat(html_writer::empty_tag('br'), 2);
        $message .= $status->acronym.': '.
            ($status->description ? $status->description : get_string('nodescription', 'attendance'));
        $confirmurl = $url;
        $confirmurl->param('confirm', 1);

        echo $OUTPUT->confirm($message, $confirmurl, $url);
        echo $OUTPUT->footer();
        exit;
    case mod_attendance_preferences_page_params::ACTION_HIDE:
        $statuses = attendance_get_statuses(0, false);
        $status = $statuses[$statusid];
        attendance_update_status($status, null, null, null, 0);
        break;
    case mod_attendance_preferences_page_params::ACTION_SHOW:
        $statuses = attendance_get_statuses(0, false);
        $status = $statuses[$statusid];
        attendance_update_status($status, null, null, null, 1);
        break;
    case mod_attendance_preferences_page_params::ACTION_SAVE:
        $acronym        = required_param_array('acronym', PARAM_TEXT);
        $description    = required_param_array('description', PARAM_TEXT);
        $grade          = required_param_array('grade', PARAM_RAW);
        $studentavailability = required_param_array('studentavailability', PARAM_RAW);
        $unmarkedstatus = optional_param('setunmarked', null, PARAM_INT);
        foreach ($grade as &$val) {
            $val = unformat_float($val);
        }
        $statuses = attendance_get_statuses(0, false);

        foreach ($acronym as $id => $v) {
            $status = $statuses[$id];
            $setunmarked = false;
            if ($unmarkedstatus == $id) {
                $setunmarked = true;
            }
            $errors[$id] = attendance_update_status($status, $acronym[$id], $description[$id], $grade[$id],
                                             null, null, null, $studentavailability[$id], $setunmarked);
        }
        echo $OUTPUT->notification(get_string('eventstatusupdated', 'attendance'), 'success');

        break;
}

$statuses = attendance_get_statuses(0, false);
$prefdata = new attendance_default_statusset($statuses, $errors);
echo $output->render($prefdata);

echo $OUTPUT->footer();
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
 * This file contains course specific settings instance manipulation.
 *
 * @package    local_reminders
 * @copyright  2014 Joannes Burk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/reminders/coursesettings_form.php');

define('CUSTOM_MINUTE_SECS', 60);
define('CUSTOM_HOUR_SECS', CUSTOM_MINUTE_SECS * 60);
define('CUSTOM_DAY_SECS', CUSTOM_HOUR_SECS * 24);
define('CUSTOM_WEEK_SECS', CUSTOM_DAY_SECS * 7);

$activityprefix = 'activity_';

$courseid = required_param('courseid', PARAM_INT);

$return = new moodle_url('/course/view.php', ['id' => $courseid]);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$coursesettings = $DB->get_record('local_reminders_course', ['courseid' => $courseid]);
if (!$coursesettings) {
    $coursesettings = new stdClass();
}
$coursesettings->courseid = $courseid;
$coursesettings->explicitenable = isset($CFG->local_reminders_explicitenable) && $CFG->local_reminders_explicitenable;
$coursecontext = context_course::instance($course->id);

$activitysettings = $DB->get_records('local_reminders_activityconf', ['courseid' => $courseid]);
if (!$activitysettings) {
    $activitysettings = [];
} else {
    foreach ($activitysettings as $asetting) {
        $actkey = 'activity_'.$asetting->eventid.'_'.$asetting->settingkey;
        $coursesettings->$actkey = $asetting->settingvalue;
    }
}

$globalactivityaheaddays = $CFG->local_reminders_duerdays;
if (!isset($globalactivityaheaddays)) {
    $globalactivityaheaddays = [0, 0, 0];
}
$aheaddaysindex = [7 => 0, 3 => 1, 1 => 2];
foreach ($aheaddaysindex as $dkey => $dvalue) {
    $daykey = 'activityglobal_days'.$dkey;
    $coursesettings->$daykey = $globalactivityaheaddays[$dvalue];
}

$globalactivitycustom = $CFG->local_reminders_duecustom;
$customkey = 'activityglobal_custom';
if ($globalactivitycustom && intval($globalactivitycustom) > 0) {
    $customvalue = intval($globalactivitycustom);
    $coursesettings->$customkey = 1;
    $customtimeunits = [
        'weeks' => CUSTOM_WEEK_SECS,
        'days' => CUSTOM_DAY_SECS,
        'hours' => CUSTOM_HOUR_SECS,
        'minutes' => CUSTOM_MINUTE_SECS,
        'seconds' => 1,
    ];
    foreach ($customtimeunits as $unitkey => $unitvalue) {
        $remainder = $customvalue % $unitvalue;
        if ($remainder == 0) {
            $value = intdiv($globalactivitycustom, $unitvalue);
            $coursesettings->customunit = $value . ' ' . $unitkey;
            break;
        }
    }
} else {
    $coursesettings->$customkey = 0;
    $coursesettings->customunit = '';
}

require_login($course);
require_capability('moodle/course:update', $coursecontext);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/reminders/coursesettings.php', ['courseid' => $courseid]);
$PAGE->set_title(get_string('admintreelabel', 'local_reminders'));
$PAGE->set_heading($course->fullname);

$mform = new local_reminders_coursesettings_edit_form(null, [$coursesettings]);

if ($mform->is_cancelled()) {
    redirect($return);
} else if ($data = $mform->get_data()) {
    $dataarray = get_object_vars($data);
    if (isset($coursesettings->id)) {
        $data->id = $coursesettings->id;
        $DB->update_record('local_reminders_course', $data);
    } else {
        $DB->insert_record('local_reminders_course', $data);
    }

    foreach ($dataarray as $key => $value) {
        if (substr($key, 0, strlen($activityprefix)) == $activityprefix) {
            $keyparts = explode('_', $key);
            if (count($keyparts) < 3) {
                continue;
            }
            $eventid = (int)$keyparts[1];
            $status = $DB->get_record_sql("SELECT id
                FROM {local_reminders_activityconf}
                WHERE courseid = :courseid AND eventid = :eventid AND settingkey = :settingkey",
                ['courseid' => $data->courseid, 'eventid' => $eventid, 'settingkey' => $keyparts[2]]);

            $actdata = new stdClass();
            $actdata->courseid = $data->courseid;
            $actdata->eventid = $eventid;
            $actdata->settingkey = $keyparts[2];
            $actdata->settingvalue = $value;
            if (!$status) {
                $DB->insert_record('local_reminders_activityconf', $actdata);
            } else {
                $actdata->id = $status->id;
                $DB->update_record('local_reminders_activityconf', $actdata);
            }
        }
    }
}


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('admintreelabel', 'local_reminders'));

$mform->display();

echo $OUTPUT->footer();

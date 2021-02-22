<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * List all zoom meetings.
 *
 * @package    mod_zoom
 * @copyright  2015 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login check require_login() is called in zoom_get_instance_setup();.
// @codingStandardsIgnoreLine
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/../../lib/accesslib.php');
require_once(dirname(__FILE__).'/../../lib/moodlelib.php');

list($course, $cm, $zoom) = zoom_get_instance_setup();

global $DB;

// Check capability.
$context = context_module::instance($cm->id);
require_capability('mod/zoom:addinstance', $context);

$uuid = required_param('uuid', PARAM_RAW);
$export = optional_param('export', null, PARAM_ALPHA);

$PAGE->set_url('/mod/zoom/participants.php', array('id' => $cm->id, 'uuid' => $uuid, 'export' => $export));

$strname = $zoom->name;
$strtitle = get_string('participants', 'mod_zoom');
$PAGE->navbar->add($strtitle);
$PAGE->set_title("$course->shortname: $strname");
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('incourse');

$maskparticipantdata = get_config('mod_zoom', 'maskparticipantdata');
// If participant data is masked then display a message stating as such and be done with it.
if ($maskparticipantdata) {
    zoom_fatal_error(
        'participantdatanotavailable_help',
        'mod_zoom',
        new moodle_url('/mod/zoom/report.php', array('id' => $cm->id))
    );
}

$sessions = zoom_get_sessions_for_display($zoom->meeting_id);
$participants = $sessions[$uuid]['participants'];

// Display the headers/etc if we're not exporting, or if there is no data.
if (empty($export) || empty($participants)) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strname);
    echo $OUTPUT->heading($strtitle, 4);

    // Stop if there is no data.
    if (empty($participants)) {
        notice(get_string('noparticipants', 'mod_zoom'),
                new moodle_url('/mod/zoom/report.php', array('id' => $cm->id)));
        echo $OUTPUT->footer();
        exit();
    }
}

// Loop through each user to generate id->idnumber mapping.
$coursecontext = context_course::instance($course->id);
$enrolled = get_enrolled_users($coursecontext);
$moodleidtouids = array();
foreach ($enrolled as $user) {
    $moodleidtouids[$user->id] = $user->idnumber;
}

$table = new html_table();
// If we are exporting, then put email as a separate column.
if (!empty($export)) {
    $table->head = array(get_string('idnumber'),
                         get_string('name'),
                         get_string('email'),
                         get_string('jointime', 'mod_zoom'),
                         get_string('leavetime', 'mod_zoom'),
                         get_string('duration', 'mod_zoom'));
} else {
    $table->head = array(get_string('idnumber'),
                         get_string('name'),
                         get_string('jointime', 'mod_zoom'),
                         get_string('leavetime', 'mod_zoom'),
                         get_string('duration', 'mod_zoom'));
}

foreach ($participants as $p) {
    $row = array();

    // Gets moodleuser so we can try to match information to Moodle database.
    $moodleuser = new stdClass();
    if (!empty($p->userid)) {
        $moodleuser = $DB->get_record('user', array('id' => $p->userid), 'idnumber, email');
    }

    // ID number.
    if (array_key_exists($p->userid, $moodleidtouids)) {
        $row[] = $moodleidtouids[$p->userid];
    } else if (isset($moodleuser->idnumber)) {
        $row[] = $moodleuser->idnumber;
    } else {
        $row[] = '';
    }

    // Name/email.
    $name = $p->name;
    $email = '';
    if (!empty($moodleuser->email)) {
        $email = $moodleuser->email;
    } else if (!empty($p->user_email)) {
        $email = $p->user_email;
    }

    // Put email in separate column if we are exporting to Excel.
    if (!empty($export)) {
        $row[] = $name;
        $row[] = $email;
    } else if (!empty($email)) {
        $row[] = html_writer::link("mailto:$email", $name);
    } else {
        $row[] = $name;
    }

    // Join/leave times.
    $row[] = userdate($p->join_time, get_string('strftimedatetimeshort', 'langconfig'));
    $row[] = userdate($p->leave_time, get_string('strftimedatetimeshort', 'langconfig'));

    // Duration.
    $durationremainder = $p->duration % 60;
    if ($durationremainder != 0) {
        $p->duration += 60 - $durationremainder;
    }
    $row[] = $p->duration / 60;

    $table->data[] = $row;
}

if ($export != 'xls') {
    echo html_writer::table($table);

    $exporturl = new moodle_url('/mod/zoom/participants.php', array(
            'id' => $cm->id,
            'uuid' => $uuid,
            'export' => 'xls'
        ));
    $xlsstring = get_string('application/vnd.ms-excel', 'mimetypes');
    $xlsicon = html_writer::img($OUTPUT->image_url('f/spreadsheet'), $xlsstring, array('title' => $xlsstring));
    echo get_string('export', 'mod_zoom') . ': ' . html_writer::link($exporturl, $xlsicon);

    echo $OUTPUT->footer();
} else {
    require_once(dirname(__FILE__).'/../../lib/excellib.class.php');

    $workbook = new MoodleExcelWorkbook("zoom_participants_{$zoom->meeting_id}");
    $worksheet = $workbook->add_worksheet($strtitle);
    $boldformat = $workbook->add_format();
    $boldformat->set_bold(true);
    $row = $col = 0;

    foreach ($table->head as $colname) {
        $worksheet->write_string($row, $col++, $colname, $boldformat);
    }
    $row++; $col = 0;

    foreach ($table->data as $entry) {
        foreach ($entry as $value) {
            $worksheet->write_string($row, $col++, $value);
        }
        $row++; $col = 0;
    }

    $workbook->close();
    exit();
}

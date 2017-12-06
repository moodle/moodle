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
 * Temporary user management.
 *
 * @package    mod_attendance
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
global $CFG, $DB, $OUTPUT, $PAGE, $COURSE;
require_once($CFG->dirroot.'/mod/attendance/locallib.php');
require_once($CFG->dirroot.'/mod/attendance/temp_form.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

$att = new mod_attendance_structure($att, $cm, $course);
$PAGE->set_url($att->url_managetemp());

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/attendance:managetemporaryusers', $context);

$PAGE->set_title($course->shortname.": ".$att->name.' - '.get_string('tempusers', 'attendance'));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('tempusers', 'attendance'));

$output = $PAGE->get_renderer('mod_attendance');
$tabs = new attendance_tabs($att, attendance_tabs::TAB_TEMPORARYUSERS);

$formdata = (object)array(
    'id' => $cm->id,
);
$mform = new temp_form();
$mform->set_data($formdata);

if ($data = $mform->get_data()) {
    // Create temp user in main user table.
    $user = new stdClass();
    $user->auth = 'manual';
    $user->confirmed = 1;
    $user->deleted = 1;
    $user->email = time().'@ghost.user.de';
    $user->username = time().'@ghost.user.de';
    $user->idnumber = 'tempghost';
    $user->mnethostid = $CFG->mnet_localhost_id;
    $studentid = $DB->insert_record('user', $user);

    // Create the temporary user record.
    $newtempuser = new stdClass();
    $newtempuser->fullname = $data->tname;
    $newtempuser->courseid = $COURSE->id;
    $newtempuser->email = $data->temail;
    $newtempuser->created = time();
    $newtempuser->studentid = $studentid;
    $DB->insert_record('attendance_tempusers', $newtempuser);

    redirect($att->url_managetemp());
}

// Output starts here.
echo $output->header();
echo $output->heading(get_string('tempusers', 'attendance').' : '.format_string($course->fullname));
echo $output->render($tabs);
$mform->display();

$tempusers = $DB->get_records('attendance_tempusers', array('courseid' => $course->id), 'fullname, email');

echo '<div>';
echo '<p style="margin-left:10%;">'.get_string('tempuserslist', 'attendance').'</p>';
if ($tempusers) {
    attendance_print_tempusers($tempusers, $att);
}
echo '</div>';
echo $output->footer($course);

/**
 * Print list of users.
 *
 * @param stdClass $tempusers
 * @param mod_attendance_structure $att
 */
function attendance_print_tempusers($tempusers, mod_attendance_structure $att) {
    echo '<p></p>';
    echo '<table border="1" bordercolor="#EEEEEE" style="background-color:#fff" cellpadding="2" align="center"'.
          'width="80%" summary="'.get_string('temptable', 'attendance').'"><tr>';
    echo '<th class="header">'.get_string('tusername', 'attendance').'</th>';
    echo '<th class="header">'.get_string('tuseremail', 'attendance').'</th>';
    echo '<th class="header">'.get_string('tcreated', 'attendance').'</th>';
    echo '<th class="header">'.get_string('tactions', 'attendance').'</th>';
    echo '</tr>';

    $even = false; // Used to colour rows.
    foreach ($tempusers as $tempuser) {
        if ($even) {
            echo '<tr style="background-color: #FCFCFC">';
        } else {
            echo '<tr>';
        }
        $even = !$even;
        echo '<td>'.format_string($tempuser->fullname).'</td>';
        echo '<td>'.format_string($tempuser->email).'</td>';
        echo '<td>'.userdate($tempuser->created, get_string('strftimedatetime')).'</td>';
        $params = array('userid' => $tempuser->id);
        $editlink = html_writer::link($att->url_tempedit($params), get_string('edituser', 'attendance'));
        $deletelink = html_writer::link($att->url_tempdelete($params), get_string('deleteuser', 'attendance'));
        $mergelink = html_writer::link($att->url_tempmerge($params), get_string('mergeuser', 'attendance'));
        echo '<td>'.$editlink.' | '.$deletelink.' | '.$mergelink.'</td>';
        echo '</tr>';
    }
    echo '</table>';
}



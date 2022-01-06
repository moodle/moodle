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
 * Merge temporary user with real user.
 *
 * @package    mod_attendance
 * @copyright  2013 Davo Smith, Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/attendance/locallib.php');

$id = required_param('id', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);
$tempuser = $DB->get_record('attendance_tempusers', array('id' => $userid), '*', MUST_EXIST);

$att = new mod_attendance_structure($att, $cm, $course);
$params = array('userid' => $tempuser->id);
$PAGE->set_url($att->url_tempmerge($params));

require_login($course, true, $cm);

$PAGE->set_title($course->shortname.": ".$att->name.' - '.get_string('tempusermerge', 'attendance'));
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('tempusermerge', 'attendance'));

$formdata = (object)array(
    'id' => $cm->id,
    'userid' => $tempuser->id,
);

$custom = array(
    'description' => format_string($tempuser->fullname).' ('.format_string($tempuser->email).')',
);
$mform = new mod_attendance\form\tempmerge(null, $custom);
$mform->set_data($formdata);

if ($mform->is_cancelled()) {
    redirect($att->url_managetemp());

} else if ($data = $mform->get_data()) {

    $sql = "SELECT s.id, lr.id AS reallogid, lt.id AS templogid
              FROM {attendance_sessions} s
              LEFT JOIN {attendance_log} lr ON lr.sessionid = s.id AND lr.studentid = :realuserid
              LEFT JOIN {attendance_log} lt ON lt.sessionid = s.id AND lt.studentid = :tempuserid
             WHERE s.attendanceid = :attendanceid AND lt.id IS NOT NULL
             ORDER BY s.id";
    $params = array(
        'realuserid' => $data->participant,
        'tempuserid' => $tempuser->studentid,
        'attendanceid' => $att->id,
    );
    $logs = $DB->get_recordset_sql($sql, $params);

    foreach ($logs as $log) {
        if (!is_null($log->reallogid)) {
            // Remove the existing attendance for the real user for this session.
            $DB->delete_records('attendance_log', array('id' => $log->reallogid));
        }
        // Adjust the 'temp user' attendance record to point at the real user.
        $DB->set_field('attendance_log', 'studentid', $data->participant, array('id' => $log->templogid));
    }

    // Delete the temp user.
    $DB->delete_records('attendance_tempusers', array('id' => $tempuser->id));
    $att->update_users_grade(array($data->participant)); // Update the gradebook after the merge.

    redirect($att->url_managetemp());
}

/** @var mod_attendance_renderer $output */
$output = $PAGE->get_renderer('mod_attendance');
$tabs = new attendance_tabs($att, attendance_tabs::TAB_TEMPORARYUSERS);

echo $output->header();
echo $output->heading(get_string('tempusermerge', 'attendance').' : '.format_string($course->fullname));
echo $output->render($tabs);
$mform->display();
echo $output->footer($course);
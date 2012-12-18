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
 * Displays information about all the assignment modules in the requested course
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/assign/locallib.php');

$id = required_param('id', PARAM_INT); // Course ID

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);
$PAGE->set_url('/mod/assign/index.php', array('id' => $id));
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, "assign", "view all", "index.php?id=$course->id", "");

// Print the header
$strplural = get_string("modulenameplural", "assign");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Get all the appropriate data
if (!$assignments = get_all_instances_in_course("assign", $course)) {
    notice(get_string('thereareno', 'moodle', $strplural), new moodle_url('/course/view.php', array('id' => $course->id)));
    die;
}

// Check if we need the closing date header
$table = new html_table();
$table->head  = array ($strplural, get_string('duedate', 'assign'), get_string('submissions', 'assign'));
$table->align = array ('left', 'left', 'center');
$table->data = array();
foreach ($assignments as $assignment) {
    $cm = get_coursemodule_from_instance('assign', $assignment->id, 0, false, MUST_EXIST);

    $link = html_writer::link(new moodle_url('/mod/assign/view.php', array('id' => $cm->id)), $assignment->name);
    $date = '-';
    if (!empty($assignment->duedate)) {
        $date = userdate($assignment->duedate);
    }

    $context = context_module::instance($cm->id);
    $instance = new assign($context, $cm, $course);

    $submitted = '';
    if (has_capability('mod/assign:grade', $context)) {
        $submitted = $instance->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED);
    } else if (has_capability('mod/assign:submit', $context)) {
        $submission = $DB->get_record('assign_submission', array('assignment'=>$assignment->id, 'userid'=>$USER->id));

        if (!empty($submission->status)) {
            $submitted = get_string('submissionstatus_' . $submission->status, 'assign');
        } else {
            $submitted = get_string('submissionstatus_', 'assign');
        }
    }

    $row = array($link, $date, $submitted);
    $table->data[] = $row;
}
echo html_writer::table($table);
echo $OUTPUT->footer();

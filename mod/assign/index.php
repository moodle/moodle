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
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strduedate = get_string("duedate", "assign");
$strsubmission = get_string("submission", "assign");
$strgrade = get_string("grade");
$PAGE->navbar->add($strplural);
$PAGE->set_title($strplural);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

if (!$cms = get_coursemodules_in_course('assign', $course->id, 'cm.idnumber, m.duedate')) {
    notice(get_string('thereareno', 'moodle', $strplural), new moodle_url('/course/view.php', array('id' => $course->id)));
    die;
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = get_all_sections($course->id);
}

$timenow = time();

// Check if we need the closing date header
$table = new html_table();
if ($usesections) {
    $table->head  = array ($strsectionname, $strplural, $strduedate, $strsubmission, $strgrade);
    $table->align = array ('left', 'left', 'center', 'right', 'right');
} else {
    $table->head  = array ($strplural, $strduedate, $strsubmission, $strgrade);
    $table->align = array ('left', 'left', 'center', 'right');
}

$currentsection = "";
$table->data = array();
$modinfo = get_fast_modinfo($course);
foreach ($modinfo->instances['assign'] as $cm) {
    if (!$cm->uservisible) {
        continue;
    }

    $cm->timedue        = $cms[$cm->id]->duedate;
    $cm->idnumber       = $cms[$cm->id]->idnumber;

    $link = html_writer::link(new moodle_url('/mod/assign/view.php', array('id' => $cm->id)), $cm->name);

    $printsection = "";
    if ($usesections) {
        if ($cm->sectionnum !== $currentsection) {
            if ($cm->sectionnum) {
                $printsection = get_section_name($course, $sections[$cm->sectionnum]);
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $cm->sectionnum;
        }
    }

    $submitted = "";
    $cmod = get_coursemodule_from_instance('assign', $cm->instance, 0, false, MUST_EXIST);
    $context = context_module::instance($cmod->id);
    $assignment = new assign($context, $cmod, $course);
    if (has_capability('mod/assign:grade', $context)) {
        $params = array('assignment' => $cm->instance, 'status' => ASSIGN_SUBMISSION_STATUS_SUBMITTED);
        $submissioncount = $DB->count_records('assign_submission', $params);
        if ($submissioncount == 1) {
            $submitted = html_writer::link(new moodle_url('/mod/assign/view.php', array('id' => $assignment->get_course_module()->id,
                                'action' => 'grading')), $submissioncount . " " . get_string('submission', 'assign'));
        } else if ($submissioncount > 1) {
            $submitted = html_writer::link(new moodle_url('/mod/assign/view.php', array('id' => $assignment->get_course_module()->id,
                                'action' => 'grading')), $submissioncount . " " . get_string('submissions', 'assign'));
        } else {
            $submitted = $submissioncount;
        }
    } else if(has_capability('mod/assign:submit', $context)) {
        $submissionstatus = $assignment->get_user_submission($USER->id, false);
        if (!empty($submissionstatus->status)) {
            $submitted = get_string('submissionstatus_' . $submissionstatus->status, 'assign');
        } else {
            $submitted = get_string('submissionstatus_', 'assign');
        }
    } else {
        $submitted = new html_table_cell(get_string('nopermission', 'assign'));
        $submitted->attributes = array('class'=> 'submittedlate');
    }
    $grading_info = grade_get_grades($course->id, 'mod', 'assign', $cm->instance, $USER->id);
    if (isset($grading_info->items[0]) && !$grading_info->items[0]->grades[$USER->id]->hidden ) {
        $grade = $grading_info->items[0]->grades[$USER->id]->str_grade;
    }
    else {
        $grade = '-';
    }

    $due = $cm->timedue ? userdate($cm->timedue) : '-';

    if ($usesections) {
        $row = array ($printsection, $link, $due, $submitted, $grade);
    } else {
        $row = array ($link, $due, $submitted, $grade);
    }

    $table->data[] = $row;

}
echo html_writer::table($table);
echo $OUTPUT->footer();

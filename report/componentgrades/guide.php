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
 * Exports an Excel spreadsheet of the component grades in a Marking Guide-graded assignment.
 *
 * @package    report_componentgrades
 * @copyright  2014 Paul Nicholls
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/lib/excellib.class.php');
require_once($CFG->dirroot.'/report/componentgrades/locallib.php');

$id          = required_param('id', PARAM_INT);// Course ID.
$modid       = required_param('modid', PARAM_INT);// CM ID.

$params['id'] = $id;
$params['modid'] = $id;

$PAGE->set_url('/report/componentgrades/index.php', $params);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);

$modinfo = get_fast_modinfo($course->id);
$cm = $modinfo->get_cm($modid);
$modcontext = context_module::instance($cm->id);
require_capability('mod/assign:grade', $modcontext);

$showgroups = !empty($course->groupmode) && get_config('report_componentgrades', 'showgroups');

// Trigger event for logging.
$event = \report_componentgrades\event\report_viewed::create(array(
    'context' => $modcontext,
    'other' => array(
        'gradingmethod' => 'guide'
    )
));
$event->add_record_snapshot('course_modules', $cm);
$event->trigger();

$filename = $course->shortname . ' - ' . $cm->name . '.xls';

$data = $DB->get_records_sql("SELECT    ggf.id AS ggfid, crs.shortname AS course, asg.name AS assignment, gd.name AS guide,
                                        ggc.shortname, ggf.score, ggf.remark, ggf.criterionid, rubm.username AS grader,
                                        stu.id AS userid, stu.idnumber AS idnumber, stu.firstname, stu.lastname,
                                        stu.username AS student, gin.timemodified AS modified
                                FROM {course} crs
                                JOIN {course_modules} cm ON crs.id = cm.course
                                JOIN {assign} asg ON asg.id = cm.instance
                                JOIN {context} c ON cm.id = c.instanceid
                                JOIN {grading_areas} ga ON c.id=ga.contextid
                                JOIN {grading_definitions} gd ON ga.id = gd.areaid
                                JOIN {gradingform_guide_criteria} ggc ON (ggc.definitionid = gd.id)
                                JOIN {grading_instances} gin ON gin.definitionid = gd.id
                                JOIN {assign_grades} ag ON ag.id = gin.itemid
                                JOIN {user} stu ON stu.id = ag.userid
                                JOIN {user} rubm ON rubm.id = gin.raterid
                                JOIN {gradingform_guide_fillings} ggf ON (ggf.instanceid = gin.id)
                                AND (ggf.criterionid = ggc.id)
                                WHERE cm.id = ? AND gin.status = 1
                                ORDER BY lastname ASC, firstname ASC, userid ASC, ggc.sortorder ASC,
                                ggc.shortname ASC", array($cm->id));

$students = report_componentgrades_get_students($modcontext, $cm);

$first = reset($data);
if ($first === false) {
    $url = $CFG->wwwroot.'/mod/assign/view.php?id='.$cm->id;
    $message = get_string('nogradesenteredguide', 'report_componentgrades');
    redirect($url, $message, 5);
    exit;
}

$workbook = new MoodleExcelWorkbook("-");
$workbook->send($filename);
$sheet = $workbook->add_worksheet($cm->name);

$pos = report_componentgrades_add_header($workbook, $sheet, $course->fullname, $cm->name, 'guide', $first->guide, $showgroups);

$format = $workbook->add_format(array('size' => 12, 'bold' => 1));
$format2 = $workbook->add_format(array('bold' => 1));
foreach ($data as $line) {
    if ($line->userid !== $first->userid) {
        break;
    }
    $sheet->write_string(TITLESROW, $pos, $line->shortname, $format);
    $sheet->merge_cells(TITLESROW, $pos, 4, $pos + 1, $format);
    $sheet->write_string(5, $pos, get_string('score', 'report_componentgrades'), $format2);
    $sheet->set_column($pos, $pos++, 6); // Set column width to 6.
    $sheet->write_string(5, $pos, get_string('feedback', 'report_componentgrades'), $format2);
    $sheet->set_column($pos, $pos++, 10); // Set column widths to 10.
}

$gradinginfopos = $pos;
report_componentgrades_finish_colheaders($workbook, $sheet, $pos);

$students = report_componentgrades_process_data($students, $data);
$groups = array();
if ($showgroups) {
    $groups = report_componentgrades_get_user_groups($course->id);
}

report_componentgrades_add_data($sheet, $students, $gradinginfopos, 'guide', $groups);

$workbook->close();

exit;

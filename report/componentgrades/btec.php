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
 * @copyright  2016 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot . '/lib/excellib.class.php');
require_once($CFG->dirroot . '/report/componentgrades/locallib.php');

$id = required_param('id', PARAM_INT); /* Course ID */
$modid = required_param('modid', PARAM_INT); /* CM ID */

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
                'gradingmethod' => 'btec'
            )
        ));
$event->add_record_snapshot('course_modules', $cm);
$event->trigger();

$filename = $course->shortname . ' - ' . $cm->name . '.xls';

$data = $DB->get_records_sql("SELECT gbf.id AS ggfid, crs.shortname AS course, asg.name AS assignment, gd.name AS btec,
                                        gbc.shortname, gbf.score, gbf.remark, gbf.criterionid, marker.username AS grader,
                                        stu.id AS userid, stu.idnumber AS idnumber, stu.firstname, stu.lastname,
                                        stu.username AS student, gin.timemodified AS modified,ag.id, ag.grade,
                                        afc.commenttext
                                FROM {course} crs
                                JOIN {course_modules} cm ON crs.id = cm.course
                                JOIN {assign} asg ON asg.id = cm.instance
                                JOIN {context} c ON cm.id = c.instanceid
                                JOIN {grading_areas} ga ON c.id=ga.contextid
                                JOIN {grading_definitions} gd ON ga.id = gd.areaid
                                JOIN {gradingform_btec_criteria}  gbc ON (gbc.definitionid = gd.id)
                                JOIN {grading_instances} gin ON gin.definitionid = gd.id
                                JOIN {assign_grades} ag ON ag.id = gin.itemid
                                JOIN {assignfeedback_comments} afc on ag.id=afc.grade
                                JOIN {user} stu ON stu.id = ag.userid
                                JOIN {user} marker ON marker.id = gin.raterid
                                JOIN {gradingform_btec_fillings} gbf ON (gbf.instanceid = gin.id)
                                 AND (gbf.criterionid = gbc.id)
                               WHERE cm.id = ? AND gin.status = 1
                            ORDER BY lastname ASC, firstname ASC, userid ASC, gbc.sortorder ASC,
                                gbc.shortname ASC", array($cm->id));

foreach ($data as $d) {
    $d->grade = num_to_letter($d->grade);
    if ($d->score == 0) {
        $d->score = 'N';
    } else {
        $d->score = 'Y';
    }
}

$students = report_componentgrades_get_students($modcontext, $cm);

$first = reset($data);
if ($first === false) {
    $url = $CFG->wwwroot . '/mod/assign/view.php?id=' . $cm->id;
    redirect($url, get_string('nobtecgrades', 'report_componentgrades'), 5);
    exit;
}

$workbook = new MoodleExcelWorkbook("-");
$workbook->send($filename);
$sheet = $workbook->add_worksheet($cm->name);

$pos = report_componentgrades_add_header($workbook, $sheet, $course->fullname, $cm->name, 'btec', $first->btec, $showgroups);
$format = $workbook->add_format(array('size' => 12, 'bold' => 1));
$format2 = $workbook->add_format(array('bold' => 1));
foreach ($data as $line) {
    if ($line->userid !== $first->userid) {
        break;
    }
    $sheet->write_string(4, $pos, $line->shortname, $format);
    $sheet->merge_cells(4, $pos, 4, $pos + 1, $format);
    $sheet->write_string(5, $pos, 'Met', $format2);
    $sheet->set_column($pos, $pos++, 6); // Set column width to 6.
    $sheet->write_string(5, $pos, 'Feedback', $format2);
    $sheet->set_column($pos, $pos++, 10); // Set column widths to 10.
}

$gradinginfopos = $pos;
$sheet->write_string(4, $pos, 'Assignment', $format2);
$sheet->write_string(5, $pos, 'Grade', $format2);
$pos++;
$sheet->write_string(5, $pos, 'Comment', $format2);
$sheet->set_column($pos, $pos, 12);
$pos++;
report_componentgrades_finish_colheaders($workbook, $sheet, $pos);
foreach ($data as $item) {
    $item->commenttext = strip_tags($item->commenttext);
}
$students = report_componentgrades_process_data($students, $data);

$groups = array();
if ($showgroups) {
    $groups = report_componentgrades_get_user_groups($course->id);
}
report_componentgrades_add_data($sheet, $students, $gradinginfopos, 'btec', $groups);

$workbook->close();

exit;
/**
 * Convert numbers 0 to 4 to grades
 * N for No, R for Refer, P for Pass
 * M for Merit and D for distinction
 * Not sure why R is set as default at the top
 * TODO: fix R, change name to score_to_letter
 *
 * @param number $score
 * @return void
 */
function num_to_letter($score) {
    $letter = "R";
    switch ($score) {
        case 0;
            $letter = 'N';
            break;
        case 1;
            $letter = 'R';
            break;
        case 2;
            $letter = 'P';
            break;
        case 3;
            $letter = 'M';
            break;
        case 4;
            $letter = 'D';
    }
    return $letter;
}

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
 * the gradesettings service allows configuring the grades to be added to the trainingsession
 * report for this course.
 * Grades will be appended to the time report
 *
 * The global course final grade can be selected along with specified modules to get score from.
 *
 * @package    report_trainingsessions
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @version    moodle 2.x
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot.'/report/trainingsessions/gradesettings_form.php');

$id = required_param('id', PARAM_INT); // Course id.
$from = required_param('from', PARAM_INT);
$to = required_param('to', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourse');
}

$context = context_course::instance($course->id);

require_course_login($course);
require_capability('report/trainingsessions:downloadreports', $context);

$params = array('id' => $id, 'from' => $from, 'to' => $to);
$url = new moodle_url('/report/trainingsessions/gradessettings.php', $params);
$PAGE->set_url($url);
$PAGE->set_heading(get_string('gradesettings', 'report_trainingsessions'));
$PAGE->set_title(get_string('gradesettings', 'report_trainingsessions'));
$PAGE->navbar->add(get_string('gradesettings', 'report_trainingsessions'));

$form = new TrainingsessionsGradeSettingsForm();

$renderer = $PAGE->get_renderer('report_trainingsessions');
$coursemodinfo = get_fast_modinfo($course->id);

if ($data = $form->get_data()) {

    // Delete all previous recordings.
    // Purge old special grades for that course.
    $select = " courseid = ? AND moduleid = 0 ";
    $params = array($COURSE->id);
    $DB->delete_records_select('report_trainingsessions', $select, $params);

    // Activate course grade.
    if (!empty($data->coursegrade)) {
        $rec = new StdClass();
        $rec->courseid = $COURSE->id;
        $rec->moduleid = 0;
        $rec->sortorder = 0;
        $rec->label = $data->courselabel;
        $rec->grade = 0;
        $rec->ranges = '';
        $DB->insert_record('report_trainingsessions', $rec);
    }

    // Purge old special grades for that course.
    $select = " courseid = ? AND moduleid > 0 ";
    $params = array($COURSE->id);
    $DB->delete_records_select('report_trainingsessions', $select, $params);

    // Record all module grades.
    if (property_exists($data, 'moduleid')) {
        foreach ($data->moduleid as $ix => $moduleid) {
            if ($moduleid) {
                $rec = new StdClass();
                $rec->courseid = $COURSE->id;
                $rec->moduleid = $moduleid;
                $cminfo = $coursemodinfo->get_cm($moduleid);
                $altlabel = (($cminfo->idnumber) ? $cminfo->idnumber : $cminfo->get_formatted_name());
                $rec->label = (empty($data->scorelabel[$ix])) ? $altlabel : $data->scorelabel[$ix];
                $rec->sortorder = $ix;
                $rec->grade = 0;
                $rec->ranges = '';
                $DB->insert_record('report_trainingsessions', $rec);
            }
        }
    }

    // Purge old special grades for that course.
    $select = " courseid = ? AND moduleid < 0 ";
    $params = array($COURSE->id);
    $DB->delete_records_select('report_trainingsessions', $select, $params);

    // Record special grades.
    if ($data->specialgrade) {
        $rec = new StdClass();
        $rec->courseid = $COURSE->id;
        $rec->moduleid = $data->specialgrade;
        $rec->sortorder = 0;
        $rec->label = '';
        $ranges = explode(',', $data->timegraderanges);
        $timeranges['ranges'] = array();
        if (!empty($ranges)) {
            foreach ($ranges as $r) {
                $timeranges['ranges'][] = trim($r);
            }
        }
        $timeranges['timemode'] = @$data->timegrademode;
        $timeranges['bonusmode'] = @$data->bonusgrademode;
        $timeranges['timesource'] = $data->timegradesource;
        $rec->ranges = json_encode($timeranges);
        $rec->grade = $data->timegrade;
        $DB->insert_record('report_trainingsessions', $rec);
    }

    // Record sumline.
    $moduleid = TR_LINEAGGREGATORS;
    $params = array('courseid' => $COURSE->id, 'moduleid' => $moduleid);
    if (!empty($data->lineaggregators)) {
        $update = true;
        if (!$rec = $DB->get_record('report_trainingsessions', $params)) {
            $rec = new StdClass;
            $update = false;
        }
        $rec->courseid = $COURSE->id;
        $rec->moduleid = $moduleid;
        $rec->sortorder = 0;
        $rec->label = $data->lineaggregators;
        $rec->ranges = '';
        $rec->grade = 0;
        if ($update) {
            $DB->update_record('report_trainingsessions', $rec);
        } else {
            $DB->insert_record('report_trainingsessions', $rec);
        }
    } else {
        $DB->delete_records('report_trainingsessions', $params);
    }

    // Record extra formulas.
    for ($i = 1; $i <= 3; $i++) {
        $formulakey = 'calculated'.$i;
        $labelkey = 'calculated'.$i.'label';
        $moduleid = TR_XLSGRADE_FORMULA1 + ($i - 1);
        $params = array('courseid' => $COURSE->id, 'moduleid' => $moduleid);
        if (!empty($data->$formulakey)) {
            $update = true;
            if (!$rec = $DB->get_record('report_trainingsessions', $params)) {
                $rec = new StdClass;
                $update = false;
            }
            $rec->courseid = $COURSE->id;
            $rec->moduleid = $moduleid;
            $rec->sortorder = 0;
            $rec->label = $data->$labelkey;
            $rec->ranges = $data->$formulakey;
            $rec->grade = 0;

            if ($update) {
                $DB->update_record('report_trainingsessions', $rec);
            } else {
                $DB->insert_record('report_trainingsessions', $rec);
            }
        } else {
            $DB->delete_records('report_trainingsessions', $params);
        }
    }

    $params = array('id' => $COURSE->id, 'view' => 'gradesettings', 'from' => $from, 'to' => $to);
    redirect(new moodle_url('/report/trainingsessions/gradessettings.php', $params));
}

echo $OUTPUT->header();

echo $renderer->tabs($course, 'gradesettings', $from, $to);

echo $OUTPUT->heading(get_string('scoresettings', 'report_trainingsessions'));

echo $OUTPUT->notification(get_string('scoresettingsadvice', 'report_trainingsessions'));

// Prepare form feed in.
$alldata = $DB->get_records('report_trainingsessions', array('courseid' => $COURSE->id), 'sortorder');
if ($alldata) {
    $ix = 0;
    $formdata = new StdClass();
    $formdata->from = $from;
    $formdata->to = $to;
    foreach ($alldata as $datum) {

        if ($datum->moduleid == 0) {
            // Course score column.
            $formdata->coursegrade = 1;
            $formdata->courselabel = $datum->label;
        } else if ($datum->moduleid > 0) {
            // A true module id for defining a column.
            $formdata->moduleid[$ix] = $datum->moduleid;
            $formdata->scorelabel[$ix] = $datum->label;
            $ix++;
        } else if ($datum->moduleid == TR_LINEAGGREGATORS) {
            // Value of -3.
            $formdata->lineaggregators = $datum->label;
        } else if ($datum->moduleid == TR_XLSGRADE_FORMULA1) {
            // Value of -8, -9, -10.
            $jx = 1;
            $formulakey = 'calculated'.$jx;
            $labelkey = 'calculated'.$jx.'label';
            $formdata->$labelkey = $datum->label;
            $formdata->$formulakey = $datum->ranges;
        } else if ($datum->moduleid == TR_XLSGRADE_FORMULA2) {
            // Value of -8, -9, -10.
            $jx = 2;
            $formulakey = 'calculated'.$jx;
            $labelkey = 'calculated'.$jx.'label';
            $formdata->$labelkey = $datum->label;
            $formdata->$formulakey = $datum->ranges;
        } else if ($datum->moduleid == TR_XLSGRADE_FORMULA3) {
            // Value of -8, -9, -10.
            $jx = 3;
            $formulakey = 'calculated'.$jx;
            $labelkey = 'calculated'.$jx.'label';
            $formdata->$labelkey = $datum->label;
            $formdata->$formulakey = $datum->ranges;
        } else {
            // Special grades.
            $formdata->specialgrade = $datum->moduleid;
            $ranges = json_decode(@$datum->ranges);
            $ranges = (array)$ranges;
            if (!empty($ranges)) {
                $formdata->timegraderanges = implode(',', (array)$ranges['ranges']);
                $formdata->timegrademode = @$ranges['timemode'];
                $formdata->bonusgrademode = @$ranges['bonusmode'];
                $formdata->timegradesource = @$ranges['timesource'];
            }
            $formdata->timegrade = $datum->grade;
        }
    }
    $form->set_data($formdata);
} else {
    $form->from = $from;
    $form->to = $to;
    $form->set_data($form);
}

// Display form.
$form->display();

echo $OUTPUT->footer();

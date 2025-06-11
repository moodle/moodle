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
 * Results page for WDS Post Grades block.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/wds_postgrades/classes/wdspg.php');

// Get parameters.
$courseid = required_param('courseid', PARAM_INT);
$sectionid = required_param('sectionid', PARAM_INT);
$gradetype = required_param('gradetype', PARAM_ALPHA);
$resulttype = optional_param('resulttype', '', PARAM_ALPHA);
$errorcode = optional_param('errorcode', '', PARAM_TEXT);
$sectionlistingid = optional_param('sectionlistingid', '', PARAM_TEXT);
$sectiontitle = optional_param('sectiontitle', '', PARAM_TEXT);
$typeword = optional_param('typeword', '', PARAM_TEXT);

$stringvar = [
    'sectiontitle' => $sectiontitle,
    'typeword' => $typeword
];

// Session data for passing complex information.
$resultdata = null;
if (isset($SESSION->wds_postgrades_results)) {
    $resultdata = $SESSION->wds_postgrades_results;

    // Clear after use.
    unset($SESSION->wds_postgrades_results);
}

// Get course.
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

// Setup page.
$PAGE->set_url(new moodle_url('/blocks/wds_postgrades/results.php', ['courseid' => $courseid]));
$PAGE->set_context(context_course::instance($courseid));
$PAGE->set_course($course);
$PAGE->set_pagelayout('standard');

// Set title.
$PAGE->set_title(get_string('postgradesfor', 'block_wds_postgrades', $stringvar));
$PAGE->set_heading(get_string('postgradesfor', 'block_wds_postgrades', $stringvar));
$PAGE->navbar->add(get_string('pluginname', 'block_wds_postgrades'));
$PAGE->navbar->add(get_string('postgraderesults', 'block_wds_postgrades'));

// Check permissions.
require_login($course);
require_capability('block/wds_postgrades:view', $PAGE->context);

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('postgraderesults', 'block_wds_postgrades'));

// Display appropriate message based on result type.
if ($resulttype === 'error') {
    echo $OUTPUT->notification(get_string('postgradefailed', 'block_wds_postgrades'), 'error');
    if (!empty($errorcode)) {
        echo $OUTPUT->notification(get_string('postgradeservererror', 'block_wds_postgrades', $errorcode), 'error');
    }
} else if ($resulttype === 'partial') {
    echo $OUTPUT->notification(get_string('postgradepartial', 'block_wds_postgrades'), 'warning');
} else if ($resulttype === 'success') {
    echo $OUTPUT->notification(get_string('postgradessuccess', 'block_wds_postgrades'), 'success');
}

// Display detailed results if available.
if ($resultdata) {

    // Section information.
    echo html_writer::tag('p', get_string('sectionlisting', 'block_wds_postgrades', $stringvar));

    // If there are errors to display.
    if (isset($resultdata->failures) && !empty($resultdata->failures)) {
        echo html_writer::tag('h4', get_string('errordetails', 'block_wds_postgrades'));

        // Create error table.
        $table = new html_table();
        $table->attributes['class'] = 'wdspgrades generaltable';
        $table->head = [
            get_string('fullname', 'block_wds_postgrades'),
            get_string('universalid', 'block_wds_postgrades'),
            get_string('grade', 'block_wds_postgrades'),
            get_string('errormessage', 'block_wds_postgrades')
        ];

        foreach ($resultdata->failures as $failure) {
            $row = [];
            $row[] = $failure->student_fullname;
            $row[] = $failure->universal_id;
            $row[] = $failure->grade_display;
            $row[] = $failure->errormessage ?? get_string('unknownerror', 'block_wds_postgrades');
            $table->data[] = $row;
        }

        if (!empty($table->data)) {
            echo html_writer::table($table);
        }
    }

    // If there are successful grades to display.
    if (isset($resultdata->successes) && !empty($resultdata->successes)) {
        echo html_writer::tag('h4', get_string('successdetails', 'block_wds_postgrades'));

        // Create success table.
        $table = new html_table();
        $table->attributes['class'] = 'wdspgrades generaltable';
        $table->head = [
            get_string('fullname', 'block_wds_postgrades'),
            get_string('universalid', 'block_wds_postgrades'),
            get_string('grade', 'block_wds_postgrades'),
            get_string('status', 'block_wds_postgrades')
        ];

        foreach ($resultdata->successes as $success) {
            $row = [];
            $row[] = $success->student_fullname;
            $row[] = $success->universal_id;
            $row[] = $success->grade_display;
            $row[] = get_string('gradeposted', 'block_wds_postgrades');
            $table->data[] = $row;
        }

        if (!empty($table->data)) {
            echo html_writer::table($table);
        }
    }

    // If section status message exists.
    if (isset($resultdata->section_status) && $resultdata->section_status) {
        echo html_writer::tag('p', get_string('sectiongraded', 'block_wds_postgrades', $stringvar),
            ['class' => 'alert alert-info']);
    }

    // Check if all final grades have been posted.
    if ($gradetype === 'final') {
        $enrolledstudents = \block_wds_postgrades\wdspg::get_enrolled_students($courseid, $sectionid);
        $allposted = \block_wds_postgrades\wdspg::all_final_grades_posted($sectionid, $enrolledstudents, $courseid);

        if ($allposted) {
            echo $OUTPUT->notification(get_string('allgradesposted', 'block_wds_postgrades'), 'success');
        }
    }
}

// Add navigation buttons.
echo html_writer::start_div('buttons');

// View grades again button.
$viewurl = new moodle_url(
    '/blocks/wds_postgrades/view.php',
    ['courseid' => $courseid, 'sectionid' => $sectionid, 'gradetype' => $gradetype]);
echo $OUTPUT->single_button($viewurl, get_string('viewgrades', 'block_wds_postgrades', $stringvar), 'get');
echo ' ';

// Back to course button.
$courseurl = new moodle_url('/course/view.php', ['id' => $courseid]);
echo $OUTPUT->single_button($courseurl, get_string('backtocourse', 'block_wds_postgrades'), 'get');

echo html_writer::end_div();

// Complete output.
echo $OUTPUT->footer();

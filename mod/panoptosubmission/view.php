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
 * This file is the main entry point for the Panopto Student Submission activity
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

define('PANOPTO_PANEL_WIDTH', 800);
define('PANOPTO_PANEL_HEIGHT', 600);

$id = required_param('id', PARAM_INT);

if (!empty($id)) {
    list($cm, $course, $panactivityinstance) = panoptosubmission_validate_cmid($id);
}

require_course_login($course->id, true, $cm);

global $CFG, $PAGE, $OUTPUT, $DB;

$PAGE->set_url('/mod/panoptosubmission/view.php', ['id' => $id]);
$PAGE->set_title(format_string($panactivityinstance->name));
$PAGE->set_heading($course->fullname);
$pageclass = 'panoptosubmission-body';
$PAGE->add_body_class($pageclass);

$ismoodle40minimum = empty($CFG->version) ? false : $CFG->version >= 2022041908.00;
if ($ismoodle40minimum) {
        $PAGE->activityheader->set_attrs([
            "title" => '',
            "hidecompletion" => false,
            "description" => '',
        ]);
}

$context = context_module::instance($cm->id);

$PAGE->requires->css('/mod/panoptosubmission/styles.css');
$renderer = $PAGE->get_renderer('mod_panoptosubmission');

// Send the viewed activity event once this page is viewed.
$detailsviewedevent = \mod_panoptosubmission\event\assignment_details_viewed::create([
    'objectid' => $panactivityinstance->id,
    'context' => context_module::instance($cm->id),
]);
$detailsviewedevent->trigger();

// Set the activity as viewed in Moodle.
$completioninfo = new completion_info($course);
$completioninfo->set_module_viewed($cm);

echo $OUTPUT->header();
echo $OUTPUT->heading($panactivityinstance->name);
echo $OUTPUT->box_start('generalbox');
echo $renderer->display_mod_info($panactivityinstance, $context);
echo format_module_intro('panoptosubmission', $panactivityinstance, $cm->id);
echo $OUTPUT->box_end();

$submitdisabled = false;
if (panoptosubmission_submission_past_due($panactivityinstance) ||
    panoptosubmission_submission_past_cutoff($panactivityinstance) ||
    !panoptosubmission_submission_available_yet($panactivityinstance)) {
    $submitdisabled = true;
}

$submission = $DB->get_record('panoptosubmission_submission',
    ['panactivityid' => $panactivityinstance->id, 'userid' => $USER->id]
);

$contentitemparams = [
    'courseid' => $course->id,
];

// Limit the instructor buttons to ONLY those users with the role appropriate for them.
if (has_capability('mod/panoptosubmission:gradesubmission', $context)) {
    echo $renderer->display_instructor_buttons($cm, $USER->id);
    echo $renderer->display_grading_summary($cm, $course);
} else {
    echo $renderer->get_view_video_container($submission, $course->id, $cm->id);

    if ($submission) {
        if (!$panactivityinstance->resubmit) {
            $submitdisabled = true;
        }

        echo $renderer->display_student_resubmit_buttons($cm, $USER->id, $submitdisabled);
    } else {
        echo $renderer->display_student_submit_buttons($cm, $USER->id, $submitdisabled);
    }

    echo $renderer->display_grade_feedback($cm, $panactivityinstance, $submission, $context);

    $url = new moodle_url('/mod/panoptosubmission/contentitem.php', $contentitemparams);

    $params = [
        'addvidbtnid' => 'id_add_video',
        'ltilaunchurl' => $url->out(false),
        'height' => PANOPTO_PANEL_HEIGHT,
        'width' => PANOPTO_PANEL_WIDTH,
        'courseid' => $course->id,
    ];

    $PAGE->requires->js_call_amd('mod_panoptosubmission/submissionpanel', 'initsubmissionpanel', [$params]);
    $PAGE->requires->string_for_js('replacevideo', 'panoptosubmission');
    $PAGE->requires->string_for_js('select_submission', 'panoptosubmission');
}

echo $OUTPUT->footer();

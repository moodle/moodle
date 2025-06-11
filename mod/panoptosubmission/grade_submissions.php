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
 * Main grading page for the Panopto Student Submission module.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/renderer.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/grade_preferences_form.php');

// Course Module ID.
$id = required_param('cmid', PARAM_INT);
$mode = optional_param('mode', 0, PARAM_TEXT);
$tifirst = optional_param('tifirst', '', PARAM_TEXT);
$tilast = optional_param('tilast', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);

$url = new moodle_url('/mod/panoptosubmission/grade_submissions.php');
$url->param('cmid', $id);

if (!empty($mode)) {
    require_sesskey();
}

list($cm, $course, $pansubmissionactivity) = panoptosubmission_validate_cmid($id);

require_login($course->id, false, $cm);

global $PAGE, $OUTPUT, $USER;

$currentcrumb = get_string('singlesubmissionheader', 'panoptosubmission');
$PAGE->set_url($url);
$PAGE->set_title(format_string($pansubmissionactivity->name));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($currentcrumb);

$renderer = $PAGE->get_renderer('mod_panoptosubmission');
$courseid = $PAGE->context->get_course_context(false);
$context = context_module::instance($cm->id);

echo $OUTPUT->header();

require_capability('mod/panoptosubmission:gradesubmission', $context);

$event = \mod_panoptosubmission\event\grade_submissions_page_viewed::create([
    'objectid' => $pansubmissionactivity->id,
    'context' => $context,
]);
$event->trigger();

// Ensure we use the appropriate group mode, either course or module.
if (($course->groupmodeforce) == 1) {
    $prefform = new panoptosubmission_gradepreferences_form(null, ['cmid' => $cm->id, 'groupmode' => $course->groupmode]);
} else {
    $prefform = new panoptosubmission_gradepreferences_form(null, ['cmid' => $cm->id, 'groupmode' => $cm->groupmode]);
}

$data = null;

if ($data = $prefform->get_data()) {
    set_user_preference('panoptosubmission_group_filter', $data->group_filter);
    set_user_preference('panoptosubmission_filter', $data->filter);

    // Make sure advanced grading is disabled before we enable quick grading.
    $gradingmanager = get_grading_manager($context, 'mod_panoptosubmission', 'submissions');
    $controller = $gradingmanager->get_active_controller();
    if (!empty($controller)) {
        unset($data->quickgrade);
    }

    if (isset($data->quickgrade)) {
        set_user_preference('panoptosubmission_quickgrade', $data->quickgrade);
    } else {
        set_user_preference('panoptosubmission_quickgrade', '0');
    }

    if ($data->perpage > 0) {
        set_user_preference('panoptosubmission_perpage', $data->perpage);
    }
}

if (empty($data)) {
    $data = new stdClass();
}

$data->filter = get_user_preferences('panoptosubmission_filter', 0);
$data->quickgrade = get_user_preferences('panoptosubmission_quickgrade', 0);
$data->perpage = get_user_preferences('panoptosubmission_perpage', 10);
$data->group_filter = get_user_preferences('panoptosubmission_group_filter', 0);

$gradedata = data_submitted();

// Check if fast grading was passed to the form and process the data.
if (!empty($gradedata->mode)) {

    $usersubmission = [];
    $time = time();
    $updated = false;

    foreach ($gradedata->users as $userid => $val) {

        $userto = $DB->get_record('user', ['id' => $userid]);
        $param = ['panactivityid' => $pansubmissionactivity->id, 'userid' => $userid];

        $usersubmissions = $DB->get_record('panoptosubmission_submission', $param);

        if ($usersubmissions) {

            if (array_key_exists($userid, $gradedata->menu)) {
                // Update grade.
                if (($gradedata->menu[$userid] != $usersubmissions->grade)) {

                    $usersubmissions->grade = $gradedata->menu[$userid];
                    $usersubmissions->timemarked = $time;
                    $usersubmissions->teacher = $USER->id;

                    $updated = true;
                }
            }

            if (   array_key_exists($userid, $gradedata->submissioncomment)
                && 0 != strcmp($usersubmissions->submissioncomment, $gradedata->submissioncomment[$userid])) {
                $usersubmissions->submissioncomment = $gradedata->submissioncomment[$userid];
                $updated = true;
            }

            // Trigger grade event.
            if ($DB->update_record('panoptosubmission_submission', $usersubmissions)) {

                $grade = panoptosubmission_get_submission_grade_object($pansubmissionactivity->id, $userid);

                $pansubmissionactivity->cmidnumber = $cm->idnumber;

                panoptosubmission_grade_item_update($pansubmissionactivity, $grade);

                // Send notification to student.
                if ($pansubmissionactivity->sendstudentnotifications) {
                        panoptosubmission_send_notification($cm,
                            $course,
                            $pansubmissionactivity->name,
                            $submission,
                            $USER,
                            $userto,
                            'feedbackavailable'
                        );
                }

                // Add to log only if updating.
                $event = \mod_panoptosubmission\event\grades_updated::create([
                    'context' => $context,
                    'other' => [
                        'crud' => 'u',
                    ],
                ]);
                $event->trigger();
            }

        } else {
            // No user submission however the instructor has submitted grade data.
            $usersubmissions = new stdClass();
            $usersubmissions->panactivityid = $cm->instance;
            $usersubmissions->userid = $userid;
            $usersubmissions->teacher = $USER->id;
            $usersubmissions->timemarked = $time;

            // Need to prevent completely empty submissions from getting entered.
            // Into the video submissions' table.
            // Check for unchanged grade value and an empty feedback value.
            $emptygrade = empty($gradedata->menu[$userid]) ||
                (array_key_exists($userid, $gradedata->menu) && '-1' == $gradedata->menu[$userid]);

            $emptycomment = array_key_exists(
                $userid, $gradedata->submissioncomment) && empty($gradedata->submissioncomment[$userid]
            );

            if ($emptygrade && $emptycomment) {
                continue;
            }

            if (array_key_exists($userid, $gradedata->menu)) {
                $usersubmissions->grade = $gradedata->menu[$userid];
            }

            if (array_key_exists($userid, $gradedata->submissioncomment)) {
                $usersubmissions->submissioncomment = $gradedata->submissioncomment[$userid];
            }

            // Trigger grade event.
            if ($DB->insert_record('panoptosubmission_submission', $usersubmissions)) {

                $grade = panoptosubmission_get_submission_grade_object($pansubmissionactivity->id, $userid);

                $pansubmissionactivity->cmidnumber = $cm->idnumber;

                panoptosubmission_grade_item_update($pansubmissionactivity, $grade);

                // Send notification to student.
                if ($pansubmissionactivity->sendstudentnotifications) {
                    panoptosubmission_send_notification($cm,
                        $course,
                        $pansubmissionactivity->name,
                        $submission,
                        $USER,
                        $userto,
                        'feedbackavailable'
                    );
                }

                // Add to log only if updating.
                $event = \mod_panoptosubmission\event\grades_updated::create([
                    'context' => $context,
                    'other' => [
                        'crud' => 'c',
                    ],
                ]);
                $event->trigger();
            }
        }

        $updated = false;
    }
}

$renderer->display_submissions_table(
    $cm, $data->perpage, $data->group_filter, $data->filter, $data->quickgrade, $tifirst, $tilast, $page
);

$prefform->set_data($data);
$prefform->display();

echo $OUTPUT->footer();

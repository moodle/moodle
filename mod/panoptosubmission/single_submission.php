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
 * single submission view page for the Panopto Student Submission module.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/filelib.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/renderer.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/single_submission_form.php');

$id = required_param('cmid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$tifirst = optional_param('tifirst', '', PARAM_TEXT);
$tilast = optional_param('tilast', '', PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);


list($cm, $course, $pansubmissionactivity) = panoptosubmission_validate_cmid($id);

require_login($course->id, false, $cm);
require_sesskey();

global $CFG, $PAGE, $OUTPUT, $USER;

$url = new moodle_url('/mod/panoptosubmission/single_submission.php');
$url->params(['cmid' => $id, 'userid' => $userid]);

$context = context_module::instance($cm->id);

$PAGE->set_url($url);
$PAGE->set_title(format_string($pansubmissionactivity->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

$previousurl = new moodle_url('/mod/panoptosubmission/grade_submissions.php',
    ['cmid' => $cm->id, 'tifirst' => $tifirst, 'tilast' => $tilast, 'page' => $page]);

$prevousurlstring = get_string('singlesubmissionheader', 'panoptosubmission');
$PAGE->navbar->add($prevousurlstring, $previousurl);
$PAGE->requires->css('/mod/panoptosubmission/styles.css');

require_capability('mod/panoptosubmission:gradesubmission', $context);

$event = \mod_panoptosubmission\event\single_submission_page_viewed::create([
    'objectid' => $pansubmissionactivity->id,
    'context' => context_module::instance($cm->id),
]);
$event->trigger();

// Get a single submission record.
$submission = panoptosubmission_get_submission($cm->instance, $userid);

// Get the submission user and the time they submitted the video.
$param = ['id' => $userid];
$user = $DB->get_record('user', $param);

$submissionuserpic = $OUTPUT->user_picture($user);
$submissionmodified = ' - ';
$datestringlate = ' - ';
$datestring = ' - ';

$submissionuserinfo = fullname($user);

// Get grading information.
$gradinginfo = grade_get_grades($cm->course, 'mod', 'panoptosubmission', $cm->instance, [$userid]);
$gradingdisabled = $gradinginfo->items[0]->grades[$userid]->locked || $gradinginfo->items[0]->grades[$userid]->overridden;

// Get marking teacher information and the time the submission was marked.
$teacher = '';
$submissioncomment = '';
if (!empty($submission)) {
    $datestringlate = panoptosubmission_display_lateness($submission->timemodified, $pansubmissionactivity->timedue);
    $submissionmodified = userdate($submission->timemodified);
    $datestring = userdate($submission->timemarked) . "&nbsp; (" . format_time(time() - $submission->timemarked) . ")";

    $submissionuserinfo .= '<br />' . $submissionmodified . $datestringlate;

    $submissioncomment = $submission->submissioncomment;

    $param = ['id' => $submission->teacher];
    $teacher = $DB->get_record('user', $param);
}

$markingteacherpic = '';
$markingtreacherinfo = '';

if (!empty($teacher)) {
    $markingteacherpic = $OUTPUT->user_picture($teacher);
    $markingtreacherinfo = fullname($teacher).'<br />'.$datestring;
}

// Setup form data.
$formdata = new stdClass();
$formdata->submissionuserpic = $submissionuserpic;
$formdata->submissionuserinfo = $submissionuserinfo;
$formdata->markingteacherpic = $markingteacherpic;
$formdata->markingteacherinfo = $markingtreacherinfo;
$formdata->grading_info = $gradinginfo;
$formdata->gradingdisabled = $gradingdisabled;
$formdata->cm = $cm;
$formdata->context = $context;
$formdata->cminstance = $pansubmissionactivity;
$formdata->submission = $submission;
$formdata->userid = $userid;
$formdata->enableoutcomes = $CFG->enableoutcomes;
$formdata->submissioncomment_editor = ['text' => $submissioncomment, 'format' => FORMAT_HTML];
$formdata->tifirst = $tifirst;
$formdata->tilast = $tilast;
$formdata->page = $page;

$submissionform = new panoptosubmission_singlesubmission_form(null, $formdata);

if ($submissionform->is_cancelled()) {
    redirect($previousurl);
} else if ($submitteddata = $submissionform->get_data()) {

    if (!isset($submitteddata->cancel) && (isset($submitteddata->xgrade) ||
        isset($submitteddata->advancedgrading)) && isset($submitteddata->submissioncomment_editor)) {

        // Flag used when an instructor is about to grade a user who does not have a submission.
        $updategrade = true;

        $blanksubmission = false;
        if (!isset($submission)) {
            $blanksubmission = true;
            $submission = new stdClass();
            $submission->panactivityid = $cm->instance;
            $submission->userid = $userid;
            $submission->grade = -1;
            $submission->submissioncomment = $submitteddata->submissioncomment_editor['text'];
            $submission->format = $submitteddata->submissioncomment_editor['format'];
            $submission->timemarked = time();
            $submission->teacher = $USER->id;

            $submission->id = $DB->insert_record('panoptosubmission_submission', $submission);
        }

        $cmgrade = $DB->get_record('panoptosubmission', ['id' => $cm->instance], 'grade');

        $gradinginstance = panoptosubmission_get_grading_instance($cmgrade, $context, $submission, $gradingdisabled);

        if ($gradinginstance) {
            $advancedgrade = $gradinginstance->submit_and_get_grade($submitteddata->advancedgrading,
                                                                    $submission->id);

            $currentgrade = $advancedgrade;
        } else {
            $currentgrade = $submitteddata->xgrade;
        }

        $notifystudentchanged = $pansubmissionactivity->sendstudentnotifications != $submitteddata->sendstudentnotifications;
        if (!$blanksubmission) {

            $submissionchanged = strcmp(
                $submission->submissioncomment ?? '',
                $submitteddata->submissioncomment_editor['text'] ?? ''
            );
            if (   $submission->grade == $currentgrade
                && !$notifystudentchanged
                && !$submissionchanged) {
                $updategrade = false;
            }
            if ($submissionchanged || $updategrade) {
                $submission->grade = $currentgrade;
                $submission->submissioncomment = $submitteddata->submissioncomment_editor['text'];
                $submission->format = $submitteddata->submissioncomment_editor['format'];
                $submission->timemarked = time();
                $submission->teacher = $USER->id;
                $DB->update_record('panoptosubmission_submission', $submission);
            }
        } else {

            // Check for unchanged values.
            if ('-1' == $currentgrade && empty($submitteddata->submissioncomment_editor['text'])) {
                $updategrade = false;
            } else {

                $submission->grade = $currentgrade;
                $DB->update_record('panoptosubmission_submission', $submission);
            }
        }

        // Save files if any were uploaded.
        $submission->submissioncomment = file_save_draft_area_files(
                $submitteddata->submissioncomment_editor['itemid'],
                $context->id,
                STUDENTSUBMISSION_FILE_COMPONENT,
                STUDENTSUBMISSION_FILE_FILEAREA,
                $submission->id,
                ['subdirs' => true],
                $submitteddata->submissioncomment_editor['text']
        );
        $DB->update_record('panoptosubmission_submission', $submission);

        if ($updategrade) {
            $pansubmissionactivity->cmidnumber = $cm->idnumber;
            $gradeobj = panoptosubmission_get_submission_grade_object($pansubmissionactivity->id, $userid);

            panoptosubmission_grade_item_update($pansubmissionactivity, $gradeobj);

            if (empty($teacher)) {
                $teacher = $USER;
            }

            // Update notify student flag if there was a change.
            if ($notifystudentchanged) {
                $pansubmissionactivity->sendstudentnotifications = $submitteddata->sendstudentnotifications;
                $DB->update_record('panoptosubmission', $pansubmissionactivity);
            }

            // Send notification to student.
            if ($pansubmissionactivity->sendstudentnotifications) {
                panoptosubmission_send_notification($cm,
                    $course,
                    $pansubmissionactivity->name,
                    $submission,
                    $teacher,
                    $user,
                    'feedbackavailable'
                );
            }

            // Add to log.
            $event = \mod_panoptosubmission\event\grades_updated::create([
                'context' => $context,
            ]);
            $event->trigger();
        }

        // Handle outcome data.
        if (!empty($CFG->enableoutcomes)) {
            require_once($CFG->libdir.'/gradelib.php');

            $data = [];
            $gradinginfo = grade_get_grades($course->id, 'mod', 'panoptosubmission', $pansubmissionactivity->id, $userid);

            if (!empty($gradinginfo->outcomes)) {
                foreach ($gradinginfo->outcomes as $n => $old) {
                    $name = 'outcome_'.$n;
                    if (isset($submitteddata->{$name}[$userid]) &&
                        $old->grades[$userid]->grade != $submitteddata->{$name}[$userid]) {

                        $data[$n] = $submitteddata->{$name}[$userid];
                    }
                }
            }

            if (count($data) > 0) {
                grade_update_outcomes('mod/panoptosubmission',
                    $course->id, 'mod', 'panoptosubmission', $pansubmissionactivity->id, $userid, $data);
            }
        }
    }

    redirect($previousurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('gradesubmission', 'panoptosubmission') . ': ' . fullname($user));

$submissionform->set_data($formdata);

$submissionform->display();

echo $OUTPUT->footer();

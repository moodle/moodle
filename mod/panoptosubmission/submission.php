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
 * submission page for the Panopto Student Submission module.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

require_sesskey();

$source = required_param('source', PARAM_URL);
$customdata = required_param('customdata', PARAM_TEXT);
$cmid = required_param('cmid', PARAM_INT);
$width = required_param('width', PARAM_TEXT);
$height = required_param('height', PARAM_TEXT);
$title = required_param('sessiontitle', PARAM_TEXT);
$thumbnailsource = required_param('thumbnailsource', PARAM_URL);
$thumbnailwidth = required_param('thumbnailwidth', PARAM_TEXT);
$thumbnailheight = required_param('thumbnailheight', PARAM_TEXT);

global $USER, $OUTPUT, $DB, $PAGE;

if (! $cm = get_coursemodule_from_id('panoptosubmission', $cmid)) {
    throw new moodle_exception('invalidcoursemodule');
}

if (! $course = $DB->get_record('course', ['id' => $cm->course])) {
    throw new moodle_exception('coursemisconf');
}

if (! $pansubmissionactivity = $DB->get_record('panoptosubmission', ['id' => $cm->instance])) {
    throw new moodle_exception('invalidid', 'panoptosubmission');
}

require_course_login($course->id, true, $cm);

$PAGE->set_url('/mod/panoptosubmission/view.php', ['id' => $course->id]);
$PAGE->set_title(format_string($pansubmissionactivity->name));
$PAGE->set_heading($course->fullname);


if (panoptosubmission_submission_past_cutoff($pansubmissionactivity)) {
    throw new moodle_exception('assignmentexpired', 'panoptosubmission', 'course/view.php?id=' . $course->id);
} else if (panoptosubmission_submission_past_due($pansubmissionactivity)) {
    throw new moodle_exception('assignmentpastdue', 'panoptosubmission', 'course/view.php?id=' . $course->id);
}

echo $OUTPUT->header();

$param = ['panactivityid' => $pansubmissionactivity->id, 'userid' => $USER->id];
$submission = $DB->get_record('panoptosubmission_submission', $param);

$time = time();
$url = new moodle_url('/mod/panoptosubmission/view.php', ['id' => $cm->id]);

if ($submission) {
    $submission->source = $source;
    $submission->customdata = $customdata;
    $submission->width = $width;
    $submission->height = $height;
    $submission->title = $title;
    $submission->thumbnailsource = $thumbnailsource;
    $submission->thumbnailwidth = $thumbnailwidth;
    $submission->thumbnailheight = $thumbnailheight;
    $submission->timemodified = $time;

    if (0 == $submission->timecreated) {
        $submission->timecreated = $time;
    }

    if ($DB->update_record('panoptosubmission_submission', $submission)) {

        $message = get_string('assignmentsubmitted', 'panoptosubmission');
        $continue = get_string('continue');

        echo $OUTPUT->notification($message, 'success');

        echo html_writer::start_tag('center');

        echo $OUTPUT->single_button($url, $continue, 'post');
        echo html_writer::end_tag('center');

        $event = \mod_panoptosubmission\event\assignment_submitted::create([
            'objectid' => $pansubmissionactivity->id,
            'context' => context_module::instance($cm->id),
        ]);
        $event->trigger();
    } else {
        notice(get_string('failedtoinsertsubmission', 'panoptosubmission'), $url, $course);
    }

} else {
    $submission = new stdClass();
    $submission->userid = $USER->id;
    $submission->panactivityid = $pansubmissionactivity->id;
    $submission->grade = -1;
    $submission->source = $source;
    $submission->customdata = $customdata;
    $submission->width = $width;
    $submission->height = $height;
    $submission->title = $title;
    $submission->thumbnailsource = $thumbnailsource;
    $submission->thumbnailwidth = $thumbnailwidth;
    $submission->thumbnailheight = $thumbnailheight;
    $submission->timecreated = $time;
    $submission->timemodified = $time;

    if ($DB->insert_record('panoptosubmission_submission', $submission)) {

        $message = get_string('assignmentsubmitted', 'panoptosubmission');
        $continue = get_string('continue');

        echo $OUTPUT->notification($message, 'success');

        echo html_writer::start_tag('center');

        echo $OUTPUT->single_button($url, $continue, 'post');
        echo html_writer::end_tag('center');

        $event = \mod_panoptosubmission\event\assignment_submitted::create([
            'objectid' => $pansubmissionactivity->id,
            'context' => context_module::instance($cm->id),
        ]);
        $event->trigger();
    } else {
        notice(get_string('failedtoinsertsubmission', 'panoptosubmission'), $url, $course);
    }
}

$context = $PAGE->context;

// Email an alert to the teacher.
panoptosubmission_notify_graders($pansubmissionactivity,
    $submission,
    $cm,
    $context,
    $course);


echo $OUTPUT->footer();

<?php
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
 * Kaltura video assignment submission script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

$entryid = required_param('entry_id', PARAM_TEXT);
$source = required_param('source', PARAM_URL);
$cmid  = required_param('cmid', PARAM_INT);
$width  = required_param('width', PARAM_TEXT);
$height  = required_param('height', PARAM_TEXT);
$metadata  = required_param('metadata', PARAM_TEXT);

global $USER, $OUTPUT, $DB, $PAGE;

$urlparts = parse_url($source);
if (!empty($urlparts['path'])) {
    $source = 'http://'.KALTURA_URI_TOKEN.$urlparts['path'];
}

if (! $cm = get_coursemodule_from_id('kalvidassign', $cmid)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}

if (! $kalvidassignobj = $DB->get_record('kalvidassign', array('id' => $cm->instance))) {
    print_error('invalidid', 'kalvidassign');
}

require_course_login($course->id, true, $cm);

$PAGE->set_url('/mod/kalvidassign/view.php', array('id' => $course->id));
$PAGE->set_title(format_string($kalvidassignobj->name));
$PAGE->set_heading($course->fullname);


if (kalvidassign_assignemnt_submission_expired($kalvidassignobj)) {
    print_error('assignmentexpired', 'kalvidassign', 'course/view.php?id='.$course->id);
}

echo $OUTPUT->header();

if (empty($entryid)) {
    print_error('emptyentryid', 'kalvidassign', new moodle_url('/mod/kalvidassign/view.php', array('id' => $cm->id)));
}

$param = array('vidassignid' => $kalvidassignobj->id, 'userid' => $USER->id);
$submission = $DB->get_record('kalvidassign_submission', $param);

$time = time();
$url = new moodle_url('/mod/kalvidassign/view.php', array('id' => $cm->id));

if ($submission) {
    $submission->entry_id = $entryid;
    $submission->source = $source;
    $submission->width = $width;
    $submission->height = $height;
    $submission->timemodified = $time;
    $submission->metadata = $metadata;

    if (0 == $submission->timecreated) {
        $submission->timecreated = $time;
    }

    if ($DB->update_record('kalvidassign_submission', $submission)) {

        $message = get_string('assignmentsubmitted', 'kalvidassign');
        $continue = get_string('continue');

        echo $OUTPUT->notification($message, 'notifysuccess');

        echo html_writer::start_tag('center');

        echo $OUTPUT->single_button($url, $continue, 'post');
        echo html_writer::end_tag('center');

    } else {
        notice(get_string('failedtoinsertsubmission', 'kalvidassign'), $url, $course);
    }

} else {
    $submission = new stdClass();
    $submission->entry_id = $entryid;
    $submission->userid = $USER->id;
    $submission->vidassignid = $kalvidassignobj->id;
    $submission->grade = -1;
    $submission->source = $source;
    $submission->width = $width;
    $submission->height = $height;
    $submission->metadata = $metadata;
    $submission->timecreated = $time;
    $submission->timemodified = $time;

    if ($DB->insert_record('kalvidassign_submission', $submission)) {

        $message = get_string('assignmentsubmitted', 'kalvidassign');
        $continue = get_string('continue');

        echo $OUTPUT->notification($message, 'notifysuccess');

        echo html_writer::start_tag('center');

        echo $OUTPUT->single_button($url, $continue, 'post');
        echo html_writer::end_tag('center');

    } else {
        notice(get_string('failedtoinsertsubmission', 'kalvidassign'), $url, $course);
    }

}

$context = $PAGE->context;

// Email an alert to the teacher
if ($kalvidassignobj->emailteachers) {
    kalvidassign_email_teachers($cm, $kalvidassignobj->name, $submission, $context);
}

echo $OUTPUT->footer();

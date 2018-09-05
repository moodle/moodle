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
 * This file plays the mediafile set in lesson settings.
 *
 *  If there is a way to use the resource class instead of this code, please change to do so
 *
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID
$printclose = optional_param('printclose', 0, PARAM_INT);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST), $cm);

require_login($course, false, $cm);

// Apply overrides.
$lesson->update_effective_access($USER->id);

$context = $lesson->context;
$canmanage = $lesson->can_manage();

$url = new moodle_url('/mod/lesson/mediafile.php', array('id'=>$id));
if ($printclose !== '') {
    $url->param('printclose', $printclose);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('popup');
$PAGE->set_title($course->shortname);

$lessonoutput = $PAGE->get_renderer('mod_lesson');

// Get the mimetype
$mimetype = mimeinfo("type", $lesson->mediafile);

if ($printclose) {  // this is for framesets
    if ($lesson->mediaclose) {
        echo $lessonoutput->header($lesson, $cm);
        echo $OUTPUT->box('<form><div><input type="button" onclick="top.close();" value="'.get_string("closewindow").'" /></div></form>', 'lessonmediafilecontrol');
        echo $lessonoutput->footer();
    }
    exit();
}

// Check access restrictions.
if ($timerestriction = $lesson->get_time_restriction_status()) {  // Deadline restrictions.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('notavailable'));
    echo $lessonoutput->lesson_inaccessible(get_string($timerestriction->reason, 'lesson', userdate($timerestriction->time)));
    echo $lessonoutput->footer();
    exit();
} else if ($passwordrestriction = $lesson->get_password_restriction_status(null)) { // Password protected lesson code.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('passwordprotectedlesson', 'lesson', format_string($lesson->name)));
    echo $lessonoutput->login_prompt($lesson, $userpassword !== '');
    echo $lessonoutput->footer();
    exit();
} else if ($dependenciesrestriction = $lesson->get_dependencies_restriction_status()) { // Check for dependencies.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('completethefollowingconditions', 'lesson', format_string($lesson->name)));
    echo $lessonoutput->dependancy_errors($dependenciesrestriction->dependentlesson, $dependenciesrestriction->errors);
    echo $lessonoutput->footer();
    exit();
}

echo $lessonoutput->header($lesson, $cm);

// print the embedded media html code
echo $OUTPUT->box(lesson_get_media_html($lesson, $context));

if ($lesson->mediaclose) {
   echo '<div class="lessonmediafilecontrol">';
   echo $OUTPUT->close_window_button();
   echo '</div>';
}

echo $lessonoutput->footer();

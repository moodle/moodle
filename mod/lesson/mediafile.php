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
 * @package    mod
 * @subpackage lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID
$printclose = optional_param('printclose', 0, PARAM_INT);

$url = new moodle_url('/mod/lesson/mediafile.php', array('id'=>$id));
if ($printclose !== '') {
    $url->param('printclose', $printclose);
}
$PAGE->set_url($url);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);;
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$lessonoutput = $PAGE->get_renderer('mod_lesson');

// Get the mimetype
$mimetype = mimeinfo("type", $lesson->mediafile);

if ($printclose) {  // this is for framesets
    if ($lesson->mediaclose) {
        $PAGE->set_title($course->shortname);
        echo $lessonoutput->header($lesson, $cm);
        echo $OUTPUT->box('<form><div><input type="button" onclick="top.close();" value="'.get_string("closewindow").'" /></div></form>', 'lessonmediafilecontrol');
        echo $lessonoutput->footer();
    }
    exit();
}

$mediafilehtml = lesson_get_media_html($lesson, $context);

$PAGE->set_title($course->shortname);
echo $lessonoutput->header($lesson, $cm);
// print the embedded media html code
echo $OUTPUT->box($mediafilehtml);

if ($lesson->mediaclose) {
   echo '<div class="lessonmediafilecontrol">';
   echo $OUTPUT->close_window_button();
   echo '</div>';
}

echo $lessonoutput->footer();
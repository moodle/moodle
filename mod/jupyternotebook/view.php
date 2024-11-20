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
 * jupyternotebook main interface
 *
 * @package   mod_jupyternotebook
 * @copyright 2021 DNE - Ministere de l'Education Nationale 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/mod/url/lib.php');
require_once($CFG->dirroot.'/mod/url/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = required_param('id', PARAM_INT);        // Course module ID

$cm = get_coursemodule_from_id('jupyternotebook', $id, 0, false, MUST_EXIST);
$jupyternotebook = $DB->get_record('jupyternotebook', array('id'=>$cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/jupyternotebook:view', $context);

if($jupyternotebook->displayoptions == RESOURCELIB_DISPLAY_EMBED){
    $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
    redirect($courseurl);
}

// Completion and trigger events.
jupyternotebook_view($jupyternotebook, $course, $cm, $context);

$PAGE->set_url('/mod/jupyternotebook/view.php', array('id' => $cm->id));

$PAGE->set_heading($course->fullname);
$PAGE->set_title(format_string($jupyternotebook->name));

// Output starts here
echo $OUTPUT->header();

echo get_string('activityhelplabel', 'mod_jupyternotebook');

if(!empty($jupyternotebook->intro)){
    echo $jupyternotebook->intro;
}

echo html_writer::tag('iframe', '', array('src' => jupyternotebook_get_url($jupyternotebook, $USER), 'height' => $jupyternotebook->iframeheight, 'width' => '100%'));

// Finish the page
echo $OUTPUT->footer();



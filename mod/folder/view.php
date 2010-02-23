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
 * Folder module main user interface
 *
 * @package   mod-folder
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/folder/locallib.php");

$id = optional_param('id', 0, PARAM_INT);  // Course module ID
$f  = optional_param('f', 0, PARAM_INT);   // Folder instance id

if ($f) {  // Two ways to specify the module
    $folder = $DB->get_record('folder', array('id'=>$f), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('folder', $folder->id, $folder->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('folder', $id, 0, false, MUST_EXIST);
    $folder = $DB->get_record('folder', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'folder', 'view', 'view.php?id='.$cm->id, $folder->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/folder/view.php', array('id' => $cm->id));

$PAGE->set_title($course->shortname.': '.$folder->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($folder);
echo $OUTPUT->header();

echo $OUTPUT->heading(format_string($folder->name), 2);

if (trim(strip_tags($folder->intro))) {
    echo $OUTPUT->box_start('mod_introbox', 'pageintro');
    echo format_module_intro('folder', $folder, $cm->id);
    echo $OUTPUT->box_end();
}

echo $OUTPUT->box_start('generalbox foldertree');
folder_print_tree($folder, $cm, $course);
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

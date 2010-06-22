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
 * Moodle file tree viewer based on YUI2 Treeview
 *
 * @package    moodlecore
 * @subpackage file
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');

$courseid   = optional_param('id', 0, PARAM_INT);

$contextid  = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
$filearea   = optional_param('filearea', '', PARAM_ALPHAEXT);
$itemid     = optional_param('itemid', -1, PARAM_INT);
$filepath   = optional_param('filepath', '', PARAM_PATH);
$filename   = optional_param('filename', '', PARAM_FILE);

if ($courseid) {
    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
    $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
    redirect(new moodle_url('index.php', array('contextid' => $context->id, 'itemid'=> 0, 'filearea' => 'course_content')));
}

$context = get_context_instance_by_id($contextid, MUST_EXIST);
$PAGE->set_context($context);

$course = null;
$cm = null;
if ($context->contextlevel == CONTEXT_MODULE) {
    $cm = get_coursemodule_from_id(null, $context->instanceid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
} else if ($context->contextlevel == CONTEXT_COURSE) {
    $course = $DB->get_record('course', array('id'=>$context->instanceid), '*', MUST_EXIST);
}

require_login($course, false, $cm);
require_capability('moodle/course:managefiles', $context);

if ($filearea === '') {
    $filearea = null;
}

if ($itemid < 0) {
    $itemid = null;
}

if ($filepath === '') {
    $filepath = null;
}

if ($filename === '') {
    $filename = null;
}

$browser = get_file_browser();

$file_info = $browser->get_file_info($context, $filearea, $itemid, $filepath, $filename);

$strfiles = get_string("files");
if ($context->contextlevel == CONTEXT_MODULE) {
    $PAGE->set_pagelayout('incourse');
} else if ($context->contextlevel == CONTEXT_COURSE) {
    $PAGE->set_pagelayout('course');
} else {
    $PAGE->set_pagelayout('admin');
}

$PAGE->navbar->add($strfiles);
$PAGE->set_url("/files/index.php", $file_info->get_params());
$PAGE->set_title("$SITE->shortname: $strfiles");
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

$options = array();
$options['enabled_fileareas'] = array('section_backup', 'course_backup', 'course_content', 'user_backup');
echo $OUTPUT->box_start();
echo $OUTPUT->moodle_file_tree_viewer($context->id, $filearea, $itemid, $filepath, $options);
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

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

require_once('../config.php');
require_once(dirname(__FILE__) . '/coursefilesedit_form.php');
require_once($CFG->dirroot . '/repository/lib.php');

// current context
$contextid = required_param('contextid', PARAM_INT);
$component = 'course';
$filearea  = 'legacy';
$itemid    = 0;

list($context, $course, $cm) = get_context_info_array($contextid);

$url = new moodle_url('/files/coursefilesedit.php', array('contextid'=>$contextid));

require_login($course);
require_capability('moodle/course:managefiles', $context);

$PAGE->set_url($url);
$heading = get_string('coursefiles') . ': ' . format_string($course->fullname, true, array('context' => $context));
$strfiles = get_string("files");
if ($node = $PAGE->settingsnav->find('coursefiles', navigation_node::TYPE_SETTING)) {
    $node->make_active();
} else {
    $PAGE->navbar->add($strfiles);
}
$PAGE->set_context($context);
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_pagelayout('course');

$data = new stdClass();
$options = array('subdirs'=>1, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
file_prepare_standard_filemanager($data, 'files', $options, $context, $component, $filearea, $itemid);
$form = new coursefiles_edit_form(null, array('data'=>$data, 'contextid'=>$contextid));

$returnurl = new moodle_url('/files/index.php', array('contextid'=>$contextid));

if ($form->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $form->get_data()) {
    $data = file_postupdate_standard_filemanager($data, 'files', $options, $context, $component, $filearea, $itemid);
    redirect($returnurl);
}

echo $OUTPUT->header();

echo $OUTPUT->container_start();
$form->display();
echo $OUTPUT->container_end();

echo $OUTPUT->footer();

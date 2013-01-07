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
 * Manage backup files
 * @package   moodlecore
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once(dirname(__FILE__) . '/backupfilesedit_form.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/repository/lib.php');

// current context
$contextid = required_param('contextid', PARAM_INT);
$currentcontext = required_param('currentcontext', PARAM_INT);
// file parameters
$component  = optional_param('component', null, PARAM_COMPONENT);
$filearea   = optional_param('filearea', null, PARAM_AREA);
$returnurl  = optional_param('returnurl', null, PARAM_LOCALURL);

list($context, $course, $cm) = get_context_info_array($currentcontext);
$filecontext = context::instance_by_id($contextid, IGNORE_MISSING);

$url = new moodle_url('/backup/backupfilesedit.php', array('currentcontext'=>$currentcontext, 'contextid'=>$contextid, 'component'=>$component, 'filearea'=>$filearea));

require_login($course, false, $cm);
require_capability('moodle/restore:uploadfile', $context);

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('managefiles', 'backup'));
$PAGE->set_heading(get_string('managefiles', 'backup'));
$PAGE->set_pagelayout('admin');
$browser = get_file_browser();

$data = new stdClass();
$options = array('subdirs'=>0, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
file_prepare_standard_filemanager($data, 'files', $options, $filecontext, $component, $filearea, 0);
$form = new backup_files_edit_form(null, array('data'=>$data, 'contextid'=>$contextid, 'currentcontext'=>$currentcontext, 'filearea'=>$filearea, 'component'=>$component, 'returnurl'=>$returnurl));

if ($form->is_cancelled()) {
    redirect($returnurl);
}

$data = $form->get_data();
if ($data) {
    $formdata = file_postupdate_standard_filemanager($data, 'files', $options, $filecontext, $component, $filearea, 0);
    redirect($returnurl);
}

echo $OUTPUT->header();

echo $OUTPUT->container_start();
$form->display();
echo $OUTPUT->container_end();

echo $OUTPUT->footer();

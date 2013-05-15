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
 * Manage files in folder module instance
 *
 * @package    mod
 * @subpackage folder
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/folder/locallib.php");
require_once("$CFG->dirroot/mod/folder/edit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

$id = required_param('id', PARAM_INT);  // Course module ID

$cm = get_coursemodule_from_id('folder', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id, MUST_EXIST);
$folder = $DB->get_record('folder', array('id'=>$cm->instance), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_login($course, false, $cm);
require_capability('mod/folder:managefiles', $context);

$PAGE->set_url('/mod/folder/edit.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$folder->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($folder);

$data = new stdClass();
$data->id = $cm->id;
$options = array('subdirs'=>1, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'accepted_types'=>'*');
file_prepare_standard_filemanager($data, 'files', $options, $context, 'mod_folder', 'content', 0);

$mform = new mod_folder_edit_form(null, array('data'=>$data, 'options'=>$options));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/folder/view.php', array('id'=>$cm->id)));

} else if ($formdata = $mform->get_data()) {
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'mod_folder', 'content', 0);
    $DB->set_field('folder', 'revision', $folder->revision+1, array('id'=>$folder->id));

    add_to_log($course->id, 'folder', 'edit', 'edit.php?id='.$cm->id, $folder->id, $cm->id);

    redirect(new moodle_url('/mod/folder/view.php', array('id'=>$cm->id)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox foldertree');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

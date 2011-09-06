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
 *
 * @package   mod-assignment
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once(dirname(__FILE__).'/upload_form.php');
require_once(dirname(__FILE__).'/assignment.class.php');
require_once("$CFG->dirroot/repository/lib.php");

$contextid = required_param('contextid', PARAM_INT);
$id = optional_param('id', null, PARAM_INT);

$formdata = new stdClass();
$formdata->userid = required_param('userid', PARAM_INT);
$formdata->offset = optional_param('offset', null, PARAM_INT);
$formdata->forcerefresh = optional_param('forcerefresh', null, PARAM_INT);
$formdata->mode = optional_param('mode', null, PARAM_ALPHA);

$url = new moodle_url('/mod/assignment/type/upload/upload.php', array('contextid'=>$contextid,
                            'id'=>$id,'offset'=>$formdata->offset,'forcerefresh'=>$formdata->forcerefresh,'userid'=>$formdata->userid,'mode'=>$formdata->mode));

list($context, $course, $cm) = get_context_info_array($contextid);

require_login($course, true, $cm);
if (isguestuser()) {
    die();
}

if (!$assignment = $DB->get_record('assignment', array('id'=>$cm->instance))) {
    print_error('invalidid', 'assignment');
}

$fullname = format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));

$PAGE->set_url($url);
$PAGE->set_context($context);
$title = strip_tags($fullname.': '.get_string('modulename', 'assignment').': '.format_string($assignment->name,true));
$PAGE->set_title($title);
$PAGE->set_heading($title);

$instance = new assignment_upload($cm->id, $assignment, $cm, $course);
$submission = $instance->get_submission($formdata->userid, true);

$filemanager_options = array('subdirs'=>1, 'maxbytes'=>$assignment->maxbytes, 'maxfiles'=>$assignment->var1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

    $mform = new mod_assignment_upload_form(null, array('contextid'=>$contextid, 'userid'=>$formdata->userid, 'options'=>$filemanager_options));

if ($mform->is_cancelled()) {
        redirect(new moodle_url('/mod/assignment/view.php', array('id'=>$cm->id)));
} else if ($formdata = $mform->get_data()) {
    $instance->upload($mform, $filemanager_options);
    die;
}

echo $OUTPUT->header();

echo $OUTPUT->box_start('generalbox');
if ($instance->can_upload_file($submission) && ($id==null)) {
    $data = new stdClass();
    // move submission files to user draft area
    $data = file_prepare_standard_filemanager($data, 'files', $filemanager_options, $context, 'mod_assignment', 'submission', $submission->id);
    // set file manager itemid, so it will find the files in draft area
    $mform->set_data($data);
    $mform->display();
}else {
    echo $OUTPUT->notification(get_string('uploaderror', 'assignment'));
    echo $OUTPUT->continue_button(new moodle_url('/mod/assignment/view.php', array('id'=>$cm->id)));
}
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

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
 * deletes an item of the feedback
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */

require_once("../../config.php");
require_once("lib.php");

$deleteitem = required_param('deleteitem', PARAM_INT);
$item = $DB->get_record('feedback_item', array('id' => $deleteitem), '*', MUST_EXIST);
list($course, $cm) = get_course_and_cm_from_instance($item->feedback, 'feedback');

$PAGE->set_url('/mod/feedback/delete_item.php', array('deleteitem' => $deleteitem));

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/feedback:edititems', $context);
$feedback = $PAGE->activityrecord;

$editurl = new moodle_url('/mod/feedback/edit.php', array('id' => $cm->id));

// Process item deletion.
if (optional_param('confirm', 0, PARAM_BOOL) && confirm_sesskey()) {
    feedback_delete_item($deleteitem);
    redirect($editurl);
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->navbar->add(get_string('delete_item', 'feedback'));
$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading(format_string($feedback->name));
echo $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
$continueurl = new moodle_url($PAGE->url, array('confirm' => 1, 'sesskey' => sesskey()));
echo $OUTPUT->confirm(get_string('confirmdeleteitem', 'feedback'), $continueurl, $editurl);
echo $OUTPUT->box_end();

echo $OUTPUT->footer();



<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * This page prints a particular instance of checklist
 *
 * @author  David Smith <moodle@davosmith.co.uk>
 * @package mod/checklist
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $DB, $PAGE, $CFG, $USER;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$checklistid = optional_param('checklist', 0, PARAM_INT);  // checklist instance ID.

$url = new moodle_url('/mod/checklist/view.php');
if ($id) {
    $cm = get_coursemodule_from_id('checklist', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $checklist = $DB->get_record('checklist', array('id' => $cm->instance), '*', MUST_EXIST);
    $url->param('id', $id);

} else if ($checklistid) {
    $checklist = $DB->get_record('checklist', array('id' => $checklistid), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $checklist->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('checklist', $checklist->id, $course->id, false, MUST_EXIST);
    $url->param('checklist', $checklistid);

} else {
    error('You must specify a course_module ID or an instance ID');
}

$PAGE->set_url($url);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$userid = 0;
if (has_capability('mod/checklist:updateown', $context)) {
    $userid = $USER->id;
}

$chk = new checklist_class($cm->id, $userid, $checklist, $cm, $course);

$chk->view();

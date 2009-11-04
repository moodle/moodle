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
 * preliminary page to find a course to import data from & interface with the
 * backup/restore functionality
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../../../config.php');
require_once('../../lib.php');
require_once($CFG->dirroot.'/backup/lib.php');
require_once($CFG->dirroot.'/backup/restorelib.php');

$id               = required_param('id', PARAM_INT);   // course id to import TO
$fromcourse       = optional_param('fromcourse', 0, PARAM_INT);
$fromcoursesearch = optional_param('fromcoursesearch', '', PARAM_RAW);
$page             = optional_param('page', 0, PARAM_INT);
$filename         = optional_param('filename', 0, PARAM_PATH);

$url = new moodle_url($CFG->wwwroot.'/course/import/activities/index.php', array('id'=>$id));
if ($fromcourse !== 0) {
    $url->param('fromcourse', $fromcourse);
}
if ($fromcoursesearch !== '') {
    $url->param('fromcoursesearch', $fromcoursesearch);
}
if ($page !== 0) {
    $url->param('page', $page);
}
if ($filename !== 0) {
    $url->param('filename', $filename);
}
$PAGE->set_url($url);

$strimportactivities = get_string('importactivities');

if (! ($course = $DB->get_record("course", array("id"=>$id)))) {
    print_error("invalidcourseid");
}

$site = get_site();

require_login($course->id);
$tocontext = get_context_instance(CONTEXT_COURSE, $id);
if ($fromcourse) {
    $fromcontext = get_context_instance(CONTEXT_COURSE, $fromcourse);
}
$syscontext = get_context_instance(CONTEXT_SYSTEM);

if (!has_capability('moodle/course:manageactivities', $tocontext)) {
    print_error('nopermissiontoimportact');
}

// if we're not a course creator , we can only import from our own courses.
if (has_capability('moodle/course:create', $syscontext)) {
    $creator = true;
}

if ($from = $DB->get_record('course', array('id'=>$fromcourse))) {
    if (!has_capability('moodle/course:manageactivities', $fromcontext)) {
        print_error('nopermissiontoimportact');
    }
    if (!empty($filename) && file_exists($CFG->dataroot.'/'.$filename) && !empty($SESSION->import_preferences)) {
        $restore = backup_to_restore_array($SESSION->import_preferences);
        $restore->restoreto = RESTORETO_CURRENT_ADDING;
        $restore->course_id = $id;
        $restore->importing = 1; // magic variable so we know that we're importing rather than just restoring.

        $SESSION->restore = $restore;
        redirect($CFG->wwwroot.'/backup/restore.php?file='.$filename.'&id='.$fromcourse.'&to='.$id);
    }
    else {
        redirect($CFG->wwwroot.'/backup/backup.php?id='.$from->id.'&to='.$course->id);
    }
}

$PAGE->navbar->add($course->shortname, new moodle_url($CFG->wwwroot.'/course/view.php', array('id'=>$course->id)));
$PAGE->navbar->add(get_string('import'), new moodle_url($CFG->wwwroot.'/course/import.php', array('id'=>$course->id)));
$PAGE->navbar->add($strimportactivities);

$PAGE->set_title("$course->shortname: $strimportactivities");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

require_once('mod.php');

echo $OUTPUT->footer();


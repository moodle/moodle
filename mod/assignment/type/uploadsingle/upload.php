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

$url = new moodle_url('/mod/assignment/type/uploadsingle/upload.php', array('contextid'=>$contextid));

list($context, $course, $cm) = get_context_info_array($contextid);

if (!$assignment = $DB->get_record('assignment', array('id'=>$cm->instance))) {
    print_error('invalidid', 'assignment');
}

require_login($course, true, $cm);
if (isguestuser()) {
    die();
}
$instance = new assignment_uploadsingle($cm->id, $assignment, $cm, $course);

$PAGE->set_url($url);
$PAGE->set_context($context);
$title = strip_tags($course->fullname.': '.get_string('modulename', 'assignment').': '.format_string($assignment->name,true));
$PAGE->set_title($title);
$PAGE->set_heading($title);

$options = array('subdirs'=>0, 'maxbytes'=>$CFG->userquota, 'maxfiles'=>1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);

$mform = new mod_assignment_uploadsingle_form(null, array('contextid'=>$contextid, 'options'=>$options));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/assignment/view.php', array('id'=>$cm->id)));
} else if ($formdata = $mform->get_data()) {
    $instance->upload($mform, $options);
    redirect(new moodle_url('/mod/assignment/view.php', array('id'=>$cm->id)));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

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
 * drops records from feedback_sitecourse_map
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/feedback/lib.php');

$id = required_param('id', PARAM_INT);
$cmapid = required_param('cmapid', PARAM_INT);

$url = new moodle_url('/mod/feedback/unmapcourse.php', array('id'=>$id));
if ($cmapid !== '') {
    $url->param('cmapid', $cmapid);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

require_capability('mod/feedback:mapcourse', $context);

// cleanup all lost entries after deleting courses or feedbacks
feedback_clean_up_sitecourse_map();

if ($DB->delete_records('feedback_sitecourse_map', array('id'=>$cmapid))) {
    $mapurl = new moodle_url('/mod/feedback/mapcourse.php', array('id'=>$id));
    redirect ($mapurl->out(false));
} else {
    print_error('cannotunmap', 'feedback');
}


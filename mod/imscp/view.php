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
 * IMS CP module main user interface
 *
 * @package mod_imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/mod/imscp/lib.php');
require_once("$CFG->dirroot/mod/imscp/locallib.php");
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);  // Course module id.
$i  = optional_param('i', 0, PARAM_INT);   // IMS CP instance id.

if ($i) {  // Two ways to specify the module.
    $imscp = $DB->get_record('imscp', array('id' => $i), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('imscp', $imscp->id, $imscp->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('imscp', $id, 0, false, MUST_EXIST);
    $imscp = $DB->get_record('imscp', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/imscp:view', $context);

// Completion and trigger events.
imscp_view($imscp, $course, $cm, $context);

$PAGE->set_url('/mod/imscp/view.php', array('id' => $cm->id));
$PAGE->requires->js('/mod/imscp/dummyapi.js', true);

$PAGE->requires->string_for_js('navigation', 'imscp');
$PAGE->requires->string_for_js('toc', 'imscp');
$PAGE->requires->string_for_js('hide', 'moodle');
$PAGE->requires->string_for_js('show', 'moodle');

// TODO: find some better way to disable blocks and minimise footer - pagetype just for this does not seem like a good solution.
// $PAGE->set_pagelayout('maxcontent'); ?

$PAGE->set_title($course->shortname.': '.$imscp->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($imscp);

// Verify imsmanifest was parsed properly.
if (!$imscp->structure) {
    redirect(course_get_url($course->id, $cm->section), get_string('deploymenterror', 'imscp'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($imscp->name));

imscp_print_content($imscp, $cm, $course);

echo $OUTPUT->footer();

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
 * Prints a particular instance of workshop
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id     = optional_param('id', 0, PARAM_INT); // course_module ID, or
$w      = optional_param('w', 0, PARAM_INT);  // workshop instance ID
$edit   = optional_param('edit', null, PARAM_BOOL);

if ($id) {
    $cm         = get_coursemodule_from_id('workshop', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $workshop   = $DB->get_record('workshop', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $workshop   = $DB->get_record('workshop', array('id' => $w), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $workshop->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('workshop', $workshop->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
$workshop = new workshop($workshop, $cm, $course);

// todo has_capability() check using something like
// if (!(($workshop->is_open() && has_capability('mod/workshop:view')) || has_capability(...) || has_capability(...))) {
//      unable to view this page
//

// todo logging add_to_log($course->id, "workshop", "view", "view.php?id=$cm->id", "$workshop->id");

if (!is_null($edit) && $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$PAGE->set_url($workshop->view_url());
$PAGE->set_title($workshop->name);
$PAGE->set_heading($course->fullname);
$buttons = array();
if ($PAGE->user_allowed_editing()) {
    $editblocks                 = new html_form();
    $editblocks->method         = 'get';
    $editblocks->button->text   = get_string($PAGE->user_is_editing() ? 'blockseditoff' : 'blocksediton');
    $editblocks->url            = new moodle_url($PAGE->url, array('edit' => $PAGE->user_is_editing() ? 'off' : 'on'));
    $buttons[] = $OUTPUT->button($editblocks);
}
$buttons[] = update_module_button($cm->id, $course->id, get_string('modulename', 'workshop'));
$PAGE->set_button(implode('', $buttons));

// todo navigation will be changed yet for Moodle 2.0
$navlinks   = array();
$navlinks[] = array('name' => get_string('modulenameplural', 'workshop'),
                    'link' => "index.php?id=$course->id",
                    'type' => 'activity');
$navlinks[] = array('name' => format_string($workshop->name),
                    'link' => '',
                    'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//$menu       = navmenu($course, $cm); todo

/// Output starts here

echo $OUTPUT->header($navigation);

/// Print the main part of the page - todo these are just links to help during development
echo $OUTPUT->box_start();
echo $OUTPUT->heading('Workshop testing', 1);
echo "<ol>\n";
echo '<li><a href="' . $workshop->editform_url()->out()  . '">Edit grading form (' . get_string('strategy' . $workshop->strategy, 'workshop') . ')</a></li>' . "\n";
echo "<li><a href=\"submission.php?cmid={$cm->id}\">View/edit your own submission</a></li>\n";
echo "<li><a href=\"develtools.php?tool=mksubmissions&amp;cmid={$cm->id}\">Fake others' submissions</a></li>\n";
echo "<li><a href=\"allocation.php?cmid={$cm->id}\">Allocate submissions</a></li>\n";

$assessments = $workshop->get_assessments($USER->id);
echo "<li>Assess submissions\n";
echo "<ol>\n";
foreach ($assessments as $assessment) {
    echo "<li><a href=\"assessment.php?asid={$assessment->id}\">Assessment of '{$assessment->title}' by {$assessment->authorid}</a></li>" . "\n";
}
echo "</ol></li>" . "\n";
echo "</ol>\n";
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

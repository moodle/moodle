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
 * This page lists all the instances of realtimequiz in a particular course
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_realtimequiz
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

require_once(__DIR__."/../../config.php");
global $CFG, $PAGE, $OUTPUT, $DB;
require_once($CFG->dirroot.'/mod/realtimequiz/lib.php');


$id = required_param('id', PARAM_INT);   // Course.
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$PAGE->set_url(new moodle_url('/mod/realtimequiz/index.php', ['id' => $course->id]));
require_course_login($course);
$PAGE->set_pagelayout('incourse');

if ($CFG->version > 2014051200) { // Moodle 2.7+.
    $params = [
        'context' => context_course::instance($course->id),
    ];
    $event = \mod_realtimequiz\event\course_module_instance_list_viewed::create($params);
    $event->add_record_snapshot('course', $course);
    $event->trigger();
} else {
    add_to_log($course->id, "realtimequiz", "view all", "index.php?id=$course->id", "");
}

// Get all required strings.

$strrealtimequizzes = get_string("modulenameplural", "realtimequiz");
$strrealtimequiz = get_string("modulename", "realtimequiz");

$PAGE->navbar->add($strrealtimequizzes);
$PAGE->set_title(strip_tags($course->shortname.': '.$strrealtimequizzes));
echo $OUTPUT->header();

// Get all the appropriate data.

if (!$realtimequizs = get_all_instances_in_course("realtimequiz", $course)) {
    notice("There are no realtimequizes", "../../course/view.php?id=$course->id");
    die;
}

// Print the list of instances (your module will probably extend this).

$timenow = time();
$strname = get_string("name");
$strweek = get_string("week");
$strtopic = get_string("topic");

$table = new html_table();

if ($course->format === "weeks") {
    $table->head = [$strweek, $strname];
    $table->align = ["center", "left"];
} else if ($course->format === "topics") {
    $table->head = [$strtopic, $strname];
    $table->align = ["center", "left"];
} else {
    $table->head = [$strname];
    $table->align = ["left", "left"];
}

foreach ($realtimequizs as $realtimequiz) {
    $url = new moodle_url('/mod/realtimequiz/view.php', ['id' => $realtimequiz->coursemodule]);
    if (!$realtimequiz->visible) {
        // Show dimmed if the mod is hidden.
        $link = '<a class="dimmed" href="'.$url.'">'.$realtimequiz->name.'</a>';
    } else {
        // Show normal if the mod is visible.
        $link = '<a href="'.$url.'">'.$realtimequiz->name.'</a>';
    }

    if ($course->format === 'weeks' || $course->format === 'topics') {
        $table->data[] = [$realtimequiz->section, $link];
    } else {
        $table->data[] = [$link];
    }
}

echo html_writer::table($table);

// Finish the page.

echo $OUTPUT->footer();


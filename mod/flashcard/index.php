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
 * This page prints a particular instance of a flashcard
 *
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @author Valery Fremaux
 * @author Tomasz Muras
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require('../../config.php');
require_once($CFG->dirroot.'/mod/flashcard/lib.php');

$id = required_param('id', PARAM_INT); // Course id.

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

// Security.

$context = context_course::instance($course->id);
require_login($course->id);

// Trigger instances list viewed event.
$event = \mod_flashcard\event\course_module_instance_list_viewed::create(array('context' => $context));
$event->add_record_snapshot('course', $course);
$event->trigger();

// Get all required strings.

$strflashcards = get_string('modulenameplural', 'flashcard');
$strflashcard  = get_string('modulename', 'flashcard');

// Print the header.

$PAGE->set_url(new moodle_url('/mod/flashcard/index.php', array('id' => $course->id)));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->navbar->add($strflashcards);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(get_string('modulename', 'feedback').' '.get_string('activities'));
echo $OUTPUT->header();

// Get all the appropriate data.

if (! $flashcards = get_all_instances_in_course('flashcard', $course)) {
    $returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
    $OUTPUT->notification(get_string('noflashcards', 'flashcard'), $returnurl);
    die;
}

// Print the list of instances (your module will probably extend this).

$timenow = time();
$strname = get_string('name');
$strweek = get_string('week');
$strtopic = get_string('topic');

$table = new html_table();

if ($course->format == 'weeks') {
    $table->head  = array($strweek, $strname);
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array($strtopic, $strname);
    $table->align = array('center', 'left', 'left', 'left');
} else {
    $table->head  = array($strname);
    $table->align = array('left', 'left', 'left');
}

foreach ($flashcards as $flashcard) {
    $instanceurl = new moodle_url('/mod/flashcard/view.php', array('id' => $flashcard->coursemodule));
    if (!$flashcard->visible) {
        // Show dimmed if the mod is hidden.
        $link = '<a class="dimmed" href="'.$instanceuel.'">'.$flashcard->name.'</a>';
    } else {
        // Show normal if the mod is visible.
        $link = '<a href="'.$instanceurl.'">'.$flashcard->name.'</a>';
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($flashcard->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo '<br/>';

echo html_writer::table($table);

// Finish the page.

echo $OUTPUT->footer($course);


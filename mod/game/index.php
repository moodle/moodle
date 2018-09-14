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
 * This page lists all the instances of game module in a particular course
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");
require_once("lib.php");
require_once("locallib.php");

$id = required_param('id', PARAM_INT);   // It stores the courseid.

if (! $course = $DB->get_record( 'course', array( 'id' => $id))) {
    print_error( 'Course ID is incorrect');
}

require_login($course->id);

// Get all required strings game.

$strgames = get_string( 'modulenameplural', 'game');
$strgame = get_string('modulename', 'game');

// Print the header.
$PAGE->set_url('/mod/game/index.php', array('id' => $id));
$coursecontext = game_get_context_course_instance( $id);
$PAGE->set_pagelayout('incourse');

if (game_use_events()) {
    require( 'classes/event/course_module_instance_list_viewed.php');
    \mod_game\event\course_module_instance_list_viewed::create_from_course($course)->trigger();
} else {
    add_to_log($course->id, "game", "view all", "index.php?id=$course->id", "");
}

// Print the header.
$strgames = get_string("modulenameplural", "game");
$streditquestions = '';
$editqcontexts = new question_edit_contexts($coursecontext);
$PAGE->navbar->add($strgames);
$PAGE->set_title($strgames);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Get all the appropriate data.

if (! $games = get_all_instances_in_course("game", $course)) {
    notice("There are no games", "../../course/view.php?id=$course->id");
    die;
}

// Print the list of instances (your module will probably extend this).

$timenow = time();
$strname  = get_string("name");
$strweek  = get_string("week");
$strtopic  = get_string("topic");

$table = new html_table();

if ($course->format == "weeks") {
    $table->head  = array ($strweek, $strname);
    $table->align = array ("center", "left");
} else if ($course->format == "topics") {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ("center", "left", "left", "left");
} else {
    $table->head  = array ($strname);
    $table->align = array ("left", "left", "left");
}

foreach ($games as $game) {
    if (!$game->visible) {
        // Show dimmed if the mod is hidden.
        $link = "<a class=\"dimmed\" href=\"view.php?id=$game->coursemodule\">$game->name</a>";
    } else {
        // Show normal if the mod is visible.
        $link = "<a href=\"view.php?id=$game->coursemodule\">$game->name</a>";
    }

    if ($course->format == "weeks" or $course->format == "topics") {
        $table->data[] = array ($game->section, $link);
    } else {
        $table->data[] = array ($link);
    }
}

echo "<br />";

echo html_writer::table($table);

// Finish the page.

echo $OUTPUT->footer($course);

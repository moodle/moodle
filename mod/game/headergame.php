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
 * The header for many .php files.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/mod/game/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$q = optional_param('q',  0, PARAM_INT);  // Game ID.

if ($id) {
    if (!$cm = get_coursemodule_from_id('game', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    if (! $game = $DB->get_record('game', array('id' => $cm->instance))) {
        print_error('invalidcoursemodule');
    }
} else {
    if (! $game = $DB->get_record('game', array('id' => $q))) {
        print_error('invalidgameid q='.$q, 'game');
    }
    if (!$course = $DB->get_record('course', array('id' => $game->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance('game', $game->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

// Check login and get context.
require_login($course->id, false, $cm);
$context = game_get_context_module_instance( $cm->id);
require_capability('mod/game:view', $context);

// Cache some other capabilites we use several times.
$canattempt = has_capability('mod/game:attempt', $context);
$canreviewmine = has_capability('mod/game:reviewmyattempts', $context);

// Create an object to manage all the other (non-roles) access rules.
$timenow = time();

// Log this request.
if (game_use_events()) {
    require( 'classes/event/course_module_viewed.php');
    \mod_game\event\course_module_viewed::viewed($game, $context)->trigger();
} else {
    add_to_log($course->id, 'game', 'view', "view.php?id=$cm->id", $game->id, $cm->id);
}

// Initialize $PAGE, compute blocks.
$PAGE->set_url('/mod/game/view.php', array('id' => $cm->id));

$edit = optional_param('edit', -1, PARAM_BOOL);
if ($edit != -1 && $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

// Note: MDL-19010 there will be further changes to printing header and blocks.
// The code will be much nicer than this eventually.
$title = $course->shortname . ': ' . format_string($game->name);

if ($PAGE->user_allowed_editing() && !empty($CFG->showblocksonmodpages)) {
    $buttons = '<table><tr><td><form method="get" action="view.php"><div>'.
        '<input type="hidden" name="id" value="'.$cm->id.'" />'.
        '<input type="hidden" name="edit" value="'.($PAGE->user_is_editing() ? 'off' : 'on').'" />'.
        '<input type="submit" value="'.
            get_string($PAGE->user_is_editing() ? 'blockseditoff' : 'blocksediton').
            '" /></div></form></td></tr></table>';
    $PAGE->set_button($buttons);
}

$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

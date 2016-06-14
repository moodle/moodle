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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once("../config.php");
require_once($CFG->dirroot .'/notes/lib.php');

$id    = required_param('id', PARAM_INT);              // Course id.
$users = optional_param_array('userid', array(), PARAM_INT); // Array of user id.
$content = optional_param('content', '', PARAM_RAW); // Note content.
$state = optional_param('state', '', PARAM_ALPHA); // Note publish state.

$url = new moodle_url('/user/groupaddnote.php', array('id' => $id));
if ($content !== '') {
    $url->param('content', $content);
}
if ($state !== '') {
    $url->param('state', $state);
}
$PAGE->set_url($url);

if (! $course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourseid');
}

$context = context_course::instance($id);
require_login($course);

// To create notes the current user needs a capability.
require_capability('moodle/notes:manage', $context);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

if (!empty($users) && !empty($content) && confirm_sesskey()) {
    $note = new stdClass();
    $note->courseid = $id;
    $note->format = FORMAT_PLAIN;
    $note->content = $content;
    $note->publishstate = $state;
    foreach ($users as $k => $v) {
        if (!$user = $DB->get_record('user', array('id' => $v))) {
            continue;
        }
        $note->id = 0;
        $note->userid = $v;
        note_save($note);
    }

    redirect("$CFG->wwwroot/user/index.php?id=$id");
}

$straddnote = get_string('groupaddnewnote', 'notes');

$PAGE->navbar->add($straddnote);
$PAGE->set_title("$course->shortname: ".get_string('extendenrol'));
$PAGE->set_heading($course->fullname);

// Print headers.
echo $OUTPUT->header();

// This will contain all available the based On select options, but we'll disable some on them on a per user basis.

echo $OUTPUT->heading($straddnote);
echo '<form method="post" action="groupaddnote.php" >';
echo '<div style="width:100%;text-align:center;">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
$statenames = note_get_state_names();

// The first time list hack.
if (empty($users) and $post = data_submitted()) {
    foreach ($post as $k => $v) {
        if (preg_match('/^user(\d+)$/', $k, $m)) {
            $users[] = $m[1];
        }
    }
}

$userlist = array();
foreach ($users as $k => $v) {
    if (!$user = $DB->get_record('user', array('id' => $v))) {
        continue;
    }
    echo '<input type="hidden" name="userid['.$k.']" value="'.$v.'" />';
    $userlist[] = fullname($user, true);
}
echo '<p>';
echo get_string('users'). ': ' . implode(', ', $userlist) . '.';
echo '</p>';

echo '<p>' . get_string('content', 'notes');
echo '<br /><textarea name="content" rows="5" cols="50" spellcheck="true">' . strip_tags(@$content) . '</textarea></p>';

echo '<p>';
echo html_writer::label(get_string('publishstate', 'notes'), 'menustate');
echo $OUTPUT->help_icon('publishstate', 'notes');
echo html_writer::select($statenames, 'state', empty($state) ? NOTES_STATE_PUBLIC : $state, false);
echo '</p>';

echo '<input type="submit" value="' . get_string('savechanges'). '" /></div></form>';
echo $OUTPUT->footer();

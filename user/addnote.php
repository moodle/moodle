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
 * This file allows you to add a note for a user
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once("../config.php");
require_once($CFG->dirroot .'/notes/lib.php');

$id    = required_param('id', PARAM_INT);              // course id
$users = optional_param_array('userid', array(), PARAM_INT); // array of user id
$contents = optional_param_array('contents', array(), PARAM_RAW); // array of user notes
$states = optional_param_array('states', array(), PARAM_ALPHA); // array of notes states

$PAGE->set_url('/user/addnote.php', array('id'=>$id));

if (! $course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid');
}

$context = context_course::instance($id);
require_login($course);

// to create notes the current user needs a capability
require_capability('moodle/notes:manage', $context);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

if (!empty($users) && confirm_sesskey()) {
    if (count($users) != count($contents) || count($users) != count($states)) {
        print_error('invalidformdata', '', $CFG->wwwroot.'/user/index.php?id='.$id);
    }

    $note = new stdClass();
    $note->courseid = $id;
    $note->format = FORMAT_PLAIN;
    foreach ($users as $k => $v) {
        if (!$user = $DB->get_record('user', array('id'=>$v)) || empty($contents[$k])) {
            continue;
        }
        $note->id = 0;
        $note->content = $contents[$k];
        $note->publishstate = $states[$k];
        $note->userid = $v;
        if (note_save($note)) {
            add_to_log($note->courseid, 'notes', 'add', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'add note');
        }
    }
    redirect("$CFG->wwwroot/user/index.php?id=$id");
}

/// Print headers

$straddnote = get_string('addnewnote', 'notes');

$PAGE->navbar->add($straddnote);
$PAGE->set_title("$course->shortname: ".get_string('extendenrol'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
// this will contain all available the based On select options, but we'll disable some on them on a per user basis

echo $OUTPUT->heading($straddnote);
echo '<form method="post" action="addnote.php">';
echo '<fieldset class="invisiblefieldset">';
echo '<input type="hidden" name="id" value="'.$course->id.'" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '</fieldset>';
$table = new html_table();
$table->head  = array (get_string('fullnameuser'),
    get_string('content', 'notes'),
    get_string('publishstate', 'notes') . $OUTPUT->help_icon('publishstate', 'notes'),
    );
$table->align = array ('left', 'center', 'center');
$state_names = note_get_state_names();

// the first time list hack
if (empty($users) and $post = data_submitted()) {
    foreach ($post as $k => $v) {
        if (preg_match('/^user(\d+)$/',$k,$m)) {
            $users[] = $m[1];
        }
    }
}
foreach ($users as $k => $v) {
    if(!$user = $DB->get_record('user', array('id'=>$v))) {
        continue;
    }
    $checkbox = html_writer::label(get_string('selectnotestate', 'notes'), 'menustates', false, array('class' => 'accesshide'));
    $checkbox .= html_writer::select($state_names, 'states[' . $k . ']', empty($states[$k]) ? NOTES_STATE_PUBLIC : $states[$k], false, array('id' => 'menustates'));
    $table->data[] = array(
        '<input type="hidden" name="userid['.$k.']" value="'.$v.'" />'. fullname($user, true),
        '<textarea name="contents['. $k . ']" rows="2" cols="40">' . strip_tags(@$contents[$k]) . '</textarea>',
        $checkbox
    );
}
echo html_writer::table($table);
echo '<div style="width:100%;text-align:center;"><input type="submit" value="' . get_string('savechanges'). '" /></div></form>';
echo $OUTPUT->footer();


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

require_once('../config.php');
require_once('lib.php');

$noteid = required_param('id', PARAM_INT);

$PAGE->set_url('/notes/delete.php', array('id' => $noteid));

if (!$note = note_load($noteid)) {
    print_error('invalidid');
}

if (!$course = $DB->get_record('course', array('id' => $note->courseid))) {
    print_error('invalidcourseid');
}

require_login($course);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

if (!$user = $DB->get_record('user', array('id' => $note->userid))) {
    print_error('invaliduserid');
}

$context = context_course::instance($course->id);

if (!has_capability('moodle/notes:manage', $context)) {
    print_error('nopermissiontodelete', 'notes');
}

if (data_submitted() && confirm_sesskey()) {
    // If data was submitted and is valid, then delete note.
    $returnurl = $CFG->wwwroot . '/notes/index.php?course=' . $course->id . '&amp;user=' . $note->userid;
    if (!note_delete($note)) {
        print_error('cannotdeletepost', 'notes', $returnurl);
    }
    redirect($returnurl);

} else {
    // If data was not submitted yet, then show note data with a delete confirmation form.
    $strnotes = get_string('notes', 'notes');
    $optionsyes = array('id' => $noteid, 'sesskey' => sesskey());
    $optionsno  = array('course' => $course->id, 'user' => $note->userid);

    // Output HTML.
    $link = null;
    if (has_capability('moodle/course:viewparticipants', $context)
        || has_capability('moodle/site:viewparticipants', context_system::instance())) {

        $link = new moodle_url('/user/index.php', array('id' => $course->id));
    }
    $PAGE->navbar->add(get_string('participants'), $link);
    $PAGE->navbar->add(fullname($user), new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id)));
    $PAGE->navbar->add(get_string('notes', 'notes'),
                       new moodle_url('/notes/index.php', array('user' => $user->id, 'course' => $course->id)));
    $PAGE->navbar->add(get_string('delete'));
    $PAGE->set_title($course->shortname . ': ' . $strnotes);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('deleteconfirm', 'notes'),
                          new moodle_url('delete.php', $optionsyes),
                          new moodle_url('index.php', $optionsno));
    echo '<br />';
    note_print($note, NOTES_SHOW_BODY | NOTES_SHOW_HEAD);
    echo $OUTPUT->footer();
}

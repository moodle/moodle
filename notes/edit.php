<?php

require_once('../config.php');
require_once('lib.php');
require_once('edit_form.php');

/// retrieve parameters
$noteid = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/notes/edit.php');

if ($noteid) {
    //existing note
    $url->param('id', $noteid);
    if (!$note = note_load($noteid)) {
        print_error('invalidid', 'notes');
    }

} else {
    // adding new note
    $courseid = required_param('courseid', PARAM_INT);
    $userid   = required_param('userid', PARAM_INT);
    $state    = optional_param('publishstate', NOTES_STATE_PUBLIC, PARAM_ALPHA);

    $note = new stdClass();
    $note->courseid     = $courseid;
    $note->userid       = $userid;
    $note->publishstate = $state;

    $url->param('courseid', $courseid);
    $url->param('userid', $userid);
    if ($state !== NOTES_STATE_PUBLIC) {
        $url->param('publishstate', $state);
    }
}

$PAGE->set_url($url);

/// locate course information
if (!$course = $DB->get_record('course', array('id'=>$note->courseid))) {
    print_error('invalidcourseid');
}

/// locate user information
if (!$user = $DB->get_record('user', array('id'=>$note->userid))) {
    print_error('invaliduserid');
}

/// require login to access notes
require_login($course);

/// locate context information
$context = context_course::instance($course->id);
require_capability('moodle/notes:manage', $context);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

/// create form
$noteform = new note_edit_form();

/// set defaults
$noteform->set_data($note);

/// if form was cancelled then return to the notes list of the note
if ($noteform->is_cancelled()) {
    redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
}

/// if data was submitted and validated, then save it to database
if ($note = $noteform->get_data()){
    if (note_save($note)) {
        add_to_log($note->courseid, 'notes', 'update', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id, 'update note');
    }
    // redirect to notes list that contains this note
    redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
}

if ($noteid) {
    $strnotes = get_string('editnote', 'notes');
} else {
    $strnotes = get_string('addnewnote', 'notes');
}

/// output HTML
$link = null;
if (has_capability('moodle/course:viewparticipants', $context) || has_capability('moodle/site:viewparticipants', context_system::instance())) {
    $link = new moodle_url('/user/index.php',array('id'=>$course->id));
}
$PAGE->navbar->add(get_string('participants'), $link);
$PAGE->navbar->add(fullname($user), new moodle_url('/user/view.php', array('id'=>$user->id,'course'=>$course->id)));
$PAGE->navbar->add(get_string('notes', 'notes'), new moodle_url('/notes/index.php', array('user'=>$user->id,'course'=>$course->id)));
$PAGE->navbar->add($strnotes);
$PAGE->set_title($course->shortname . ': ' . $strnotes);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(fullname($user));

$noteform->display();
echo $OUTPUT->footer();

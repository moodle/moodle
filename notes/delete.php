<?php // $Id$

require_once('../config.php');
require_once('lib.php');

// retrieve parameters
$noteid       = required_param('note', PARAM_INT);

// locate note information
if (!$note = note_load($noteid)) {
    error('Incorrect note id specified');
}

// locate course information
if (!$course = get_record('course', 'id', $note->courseid)) {
    error('Incorrect course id found');
}

// locate context information
$context = get_context_instance(CONTEXT_COURSE, $course->id);

// check capability
if (!has_capability('moodle/notes:manage', $context)) {
    error('You may not delete this note');
}

if (data_submitted() && confirm_sesskey()) {
//if data was submitted and is valid, then delete note
    $returnurl = $CFG->wwwroot . '/notes/index.php?course=' . $course->id . '&amp;user=' . $note->userid;
    if (note_delete($noteid)) {
        add_to_log($note->courseid, 'notes', 'delete', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'delete note');
    } else {
        error('Error occured while deleting post', $returnurl);
    }
    redirect($returnurl);
} else {
// if data was not submitted yet, then show note data with a delete confirmation form
    $strnotes = get_string('notes', 'notes');
    $optionsyes = array('note'=>$noteid, 'sesskey'=>sesskey());
    $optionsno = array('course'=>$course->id, 'user'=>$note->userid);
    print_header($course->shortname . ': ' . $strnotes, $course->fullname);
    notice_yesno(get_string('deleteconfirm', 'notes'), 'delete.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
    echo '<br />';
    note_print($note, NOTES_SHOW_BODY | NOTES_SHOW_HEAD);
    print_footer();
}

<?php // $Id$

require_once('../config.php');
require_once('lib.php');

// retrieve parameters
$courseid      = required_param('course', PARAM_INT);
$userid        = optional_param('user', 0, PARAM_INT);

// locate course information
if (!($course = get_record('course', 'id', $courseid))) {
    error('Incorrect course id found');
}
// require login to access notes
require_login($course->id);

// locate context information
$context = get_context_instance(CONTEXT_COURSE, $course->id);

// check capability
if (!has_capability('moodle/notes:manage', $context)) {
    error('You may not create notes');
}

// build-up form
require_once('edit_form.php');
// get option values for the user select
$extradata['userlist'] = array();
$usersincourse = "SELECT * FROM {$CFG->prefix}user WHERE id IN (SELECT userid FROM {$CFG->prefix}role_assignments WHERE contextid={$context->id})";
$userlist = get_records_sql($usersincourse);
// format userdata using fullname
if($userlist) {
    foreach($userlist as $user) {
        $extradata['userlist'][$user->id] = fullname($user);
    }
}
// create form
$noteform = new note_edit_form(null, $extradata);

// if form was cancelled then return to the previous notes list
if ($noteform->is_cancelled()) {
    redirect($CFG->wwwroot . '/notes/index.php?course=' . $courseid . '&amp;user=' . $userid);
}

// if data was submitted and validated, then save it to database
if ($formdata = $noteform->get_data()) {
    $note = new object();
    $note->courseid = $formdata->course;
    $note->content = $formdata->content;
    $note->format = FORMAT_PLAIN;
    $note->userid = $formdata->user;
    $note->publishstate = $formdata->publishstate;
    if (note_save($note)) {
        add_to_log($note->courseid, 'notes', 'add', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id , 'add note');
    }
// redirect to notes list that contains this note
    redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
}

if($noteform->is_submitted()) {
// if data was submitted with errors, then use it as default for new form
    $note = $noteform->get_submitted_data(false);
} else {
// if data was not submitted yet, then use default values
    $note = new object();
    $note->id = 0;
    $note->course = $courseid;
    $note->user = $userid;
    $note->publishstate = optional_param('state', NOTES_STATE_PUBLIC, PARAM_ALPHA);
}
$noteform->set_data($note);
$strnotes = get_string('addnewnote', 'notes');

// output HTML
$crumbs = array(array('name' => $strnotes, 'link' => '', 'type' => 'activity'));
print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($crumbs));
$noteform->display();
print_footer();

<?php // $Id$

    require_once('../config.php');
    require_once('lib.php');
    require_once('edit_form.php');

/// retrieve parameters
    $noteid = optional_param('id', 0, PARAM_INT);

    if ($noteid) {
        //existing note
        if (!$note = note_load($noteid)) {
            print_error('invalidid', 'notes');
        }

    } else {
        // adding new note
        $courseid = required_param('courseid', PARAM_INT);
        $userid   = required_param('userid', PARAM_INT);
        $state    = optional_param('publishstate', NOTES_STATE_PUBLIC, PARAM_ALPHA);

        $note = new object();
        $note->courseid     = $courseid;
        $note->userid       = $userid;
        $note->publishstate = $state;
    }

/// locate course information
    if (!$course = get_record('course', 'id', $note->courseid)) {
        error('Incorrect course id found');
    }

/// locate user information
    if (!$user = get_record('user', 'id', $note->userid)) {
        error('Incorrect user id found');
    }

/// require login to access notes
    require_login($course);

/// locate context information
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
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
    $nav = array();
    if (has_capability('moodle/course:viewparticipants', $context) || has_capability('moodle/site:viewparticipants', get_context_instance(CONTEXT_SYSTEM))) {
        $nav[] = array('name' => get_string('participants'), 'link' => $CFG->wwwroot . '/user/index.php?id=' . $course->id, 'type' => 'misc');
    }
    $nav[] = array('name' => fullname($user), 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id. '&amp;course=' . $course->id, 'type' => 'misc');
    $nav[] = array('name' => get_string('notes', 'notes'), 'link' => $CFG->wwwroot . '/notes/index.php?course=' . $course->id . '&amp;user=' . $user->id, 'type' => 'misc');
    $nav[] = array('name' => $strnotes, 'link' => '', 'type' => 'activity');

    print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($nav));

    print_heading(fullname($user));

    $noteform->display();
    print_footer();
?>

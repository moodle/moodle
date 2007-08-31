<?php // $Id$

    require_once('../config.php');
    require_once('lib.php');

/// retrieve parameters
    $noteid       = required_param('note', PARAM_INT);

/// locate note information
    if (!$note = note_load($noteid)) {
        error('Incorrect note id specified');
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
    require_login($course->id);

/// locate context information
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

/// check capability
    require_capability('moodle/notes:manage', $context);

/// build-up form
    require_once('edit_form.php');

/// get option values for the user select

/// create form
    $noteform = new note_edit_form(null);

/// if form was cancelled then return to the notes list of the note
    if ($noteform->is_cancelled()) {
        redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
    }

/// if data was submitted and validated, then save it to database
    if ($formdata = $noteform->get_data()){
        $note->courseid = $formdata->course;
        $note->userid = $formdata->user;
        $note->content = $formdata->content;
        $note->format = FORMAT_PLAIN;
        $note->publishstate = $formdata->publishstate;
        if (note_save($note)) {
            add_to_log($note->courseid, 'notes', 'update', 'index.php?course='.$note->courseid.'&amp;user='.$note->userid . '#note-' . $note->id, 'update note');
        }
        // redirect to notes list that contains this note
        redirect($CFG->wwwroot . '/notes/index.php?course=' . $note->courseid . '&amp;user=' . $note->userid);
    }


    if ($noteform->is_submitted()) {
        // if data was submitted with errors, then use it as default for new form
        $note = $noteform->get_submitted_data(false);
    } else {
        // if data was not submitted yet, then used values retrieved from the database
        $note->user = $note->userid;
        $note->course = $note->courseid;
        $note->note = $note->id;
    }
    $noteform->set_data($note);
    $strnotes = get_string('editnote', 'notes');

/// output HTML
    $crumbs = array();
    $crumbs[] = array('name' => fullname($user), 'link' => $CFG->wwwroot . '/notes/index.php?course=' . $course->id . '&amp;user=' . $user->id, 'type' => 'misc');
    $crumbs[] = array('name' => $strnotes, 'link' => '', 'type' => 'activity');

    print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($crumbs));

    print_heading(fullname($user));

    $noteform->display();
    print_footer();
?>

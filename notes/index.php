<?php

/**
 * file index.php
 * index page to view notes.
 * if a course id is specified then the entries from that course are shown
 * if a user id is specified only notes related to that user are shown
 */
require_once('../config.php');
require_once('lib.php');

/// retrieve parameters
$courseid     = optional_param('course', SITEID, PARAM_INT);
$userid       = optional_param('user', 0, PARAM_INT);
$filtertype   = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

$url = new moodle_url('/notes/index.php');
if ($courseid != SITEID) {
    $url->param('course', $courseid);
}
if ($userid !== 0) {
    $url->param('user', $userid);
}
$PAGE->set_url($url);

/// tabs compatibility
switch($filtertype) {
    case 'course':
        $courseid = $filterselect;
        break;
    case 'site':
        $courseid = SITEID;
        break;
}

if (empty($courseid)) {
    $courseid = SITEID;
}

/// locate course information
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

/// locate user information
if ($userid) {
    $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
    $filtertype = 'user';
    $filterselect = $user->id;

    if ($user->deleted) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('userdeleted'));
        echo $OUTPUT->footer();
        die;
    }

} else {
    $filtertype = 'course';
    $filterselect = $course->id;
}

/// require login to access notes
require_login($course);
add_to_log($courseid, 'notes', 'view', 'index.php?course='.$courseid.'&amp;user='.$userid, 'view notes');

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

/// output HTML
if ($course->id == SITEID) {
    $coursecontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context
} else {
    $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);   // Course context
}
$systemcontext = get_context_instance(CONTEXT_SYSTEM);   // SYSTEM context

$strnotes = get_string('notes', 'notes');
if ($userid) {
    $PAGE->set_context(get_context_instance(CONTEXT_USER, $user->id));
    $PAGE->navigation->extend_for_user($user);
} else {
    $link = null;
    if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
        $link = new moodle_url('/user/index.php',array('id'=>$course->id));
    }
}

$PAGE->set_pagelayout('course');
$PAGE->set_title($course->shortname . ': ' . $strnotes);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
if ($userid) {
    echo $OUTPUT->heading(fullname($user).': '.$strnotes);
} else {
    echo $OUTPUT->heading(format_string($course->shortname, true, array('context' => $coursecontext)).': '.$strnotes);
}

$strsitenotes = get_string('sitenotes', 'notes');
$strcoursenotes = get_string('coursenotes', 'notes');
$strpersonalnotes = get_string('personalnotes', 'notes');
$straddnewnote = get_string('addnewnote', 'notes');

echo $OUTPUT->box_start();

if ($courseid != SITEID) {
    //echo '<a href="#sitenotes">' . $strsitenotes . '</a> | <a href="#coursenotes">' . $strcoursenotes . '</a> | <a href="#personalnotes">' . $strpersonalnotes . '</a>';
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    $addid = has_capability('moodle/notes:manage', $context) ? $courseid : 0;
    $view = has_capability('moodle/notes:view', $context);
    $fullname = format_string($course->fullname, true, array('context' => $context));
    note_print_notes('<a name="sitenotes"></a>' . $strsitenotes, $addid, $view, 0, $userid, NOTES_STATE_SITE, 0);
    note_print_notes('<a name="coursenotes"></a>' . $strcoursenotes. ' ('.$fullname.')', $addid, $view, $courseid, $userid, NOTES_STATE_PUBLIC, 0);
    note_print_notes('<a name="personalnotes"></a>' . $strpersonalnotes, $addid, $view, $courseid, $userid, NOTES_STATE_DRAFT, $USER->id);

} else {  // Normal course
    //echo '<a href="#sitenotes">' . $strsitenotes . '</a> | <a href="#coursenotes">' . $strcoursenotes . '</a>';
    $view = has_capability('moodle/notes:view', get_context_instance(CONTEXT_SYSTEM));
    note_print_notes('<a name="sitenotes"></a>' . $strsitenotes, 0, $view, 0, $userid, NOTES_STATE_SITE, 0);
    echo '<a name="coursenotes"></a>';

    if (!empty($userid)) {
        $courses = enrol_get_users_courses($userid);
        foreach($courses as $c) {
            $ccontext = get_context_instance(CONTEXT_COURSE, $c->id);
            $cfullname = format_string($c->fullname, true, array('context' => $ccontext));
            $header = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $c->id . '">' . $cfullname . '</a>';
            if (has_capability('moodle/notes:manage', get_context_instance(CONTEXT_COURSE, $c->id))) {
                $addid = $c->id;
            } else {
                $addid = 0;
            }
            note_print_notes($header, $addid, $view, $c->id, $userid, NOTES_STATE_PUBLIC, 0);
        }
    }
}

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

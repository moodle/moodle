<?php // $Id$

/**
 * file index.php
 * index page to view notes. 
 * if a course id is specified then the entries from that course are shown
 * if a user id is specified only notes related to that user are shown
 */
require_once('../config.php');
require_once('lib.php');
// retrieve parameters
$courseid     = optional_param('course', SITEID, PARAM_INT);
$userid       = optional_param('user', 0, PARAM_INT);
$filtertype   = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

// tabs compatibility
switch($filtertype) {
    case 'course':
        $courseid = $filterselect;
        break;
    case 'site':
        $courseid = SITEID;
        break;
}

// locate course information
if (!$course = get_record('course', 'id', $courseid)) {
    error('Incorrect course id specified');
}

// locate user information
if ($userid) {
    if(!$user = get_record('user', 'id', $userid)) {
        error('Incorrect user id specified');
    }
    $filtertype = 'user';
    $filterselect = $user->id;
} else {
    $filtertype = 'course';
    $filterselect = $course->id;
}

// require login to access notes
require_login($course->id);
$strnotes = get_string('notes', 'notes');
$crumbs = array(array('name' => $strnotes, 'link' => '', 'type' => 'activity'));
$currenttab = 'notes';
// output HTML
print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($crumbs));

require_once($CFG->dirroot .'/user/tabs.php');

if($courseid != SITEID) {
    $context = get_context_instance(CONTEXT_COURSE, $courseid);
    if (has_capability('moodle/notes:manage', $context)) {
        $addlink = $CFG->wwwroot .'/notes/add.php?course=' . $courseid . '&amp;user=' . $userid;
        echo '<p><a href="'. $addlink . '">' . get_string('addnewnote', 'notes') . '</a></p>';
    }
    note_print_notes(get_string('sitenotes', 'notes'), $context, 0, $userid, NOTES_STATE_SITE, 0);
    note_print_notes(get_string('coursenotes', 'notes'), $context, $courseid, $userid, NOTES_STATE_PUBLIC, 0);
    note_print_notes(get_string('personalnotes', 'notes'), $context, $courseid, $userid, NOTES_STATE_DRAFT, $USER->id);
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
    note_print_notes(get_string('sitenotes', 'notes'), $context, 0, $userid, NOTES_STATE_SITE, 0);
    if($userid) {
        $courses = get_my_courses($userid);
        foreach($courses as $c) {
            $header = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $c->id . '">' . $c->fullname . '</a>';
            note_print_notes($header, $context, $c->id, $userid, NOTES_STATE_PUBLIC, 0);
        }
    }
}    

add_to_log($courseid, 'notes', 'view', 'index.php?course='.$courseid.'&amp;user='.$userid, 'view notes');

print_footer($course);

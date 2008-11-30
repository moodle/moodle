<?php // $Id$

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

/// tabs compatibility
    switch($filtertype) {
        case 'course':
            $courseid = $filterselect;
            break;
        case 'site':
            $courseid = SITEID;
            break;
    }

/// locate course information
    if (!$course = get_record('course', 'id', $courseid)) {
        error('Incorrect course id specified');
    }

/// locate user information
    if ($userid) {
        if (!$user = get_record('user', 'id', $userid)) {
            error('Incorrect user id specified');
        }
        $filtertype = 'user';
        $filterselect = $user->id;

        if ($user->deleted) {
            print_header();
            print_heading(get_string('userdeleted'));
            print_footer();
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
    $nav = array();
    if (has_capability('moodle/course:viewparticipants', $coursecontext) || has_capability('moodle/site:viewparticipants', $systemcontext)) {
        $nav[] = array('name' => get_string('participants'), 'link' => $CFG->wwwroot . '/user/index.php?id=' . $course->id, 'type' => 'misc');
    }
    if ($userid) {
        $nav[] = array('name' => fullname($user), 'link' => $CFG->wwwroot . '/user/view.php?id=' . $user->id. '&amp;course=' . $course->id, 'type' => 'misc');
    }
    $nav[] = array('name' => $strnotes, 'link' => '', 'type' => 'misc');

    print_header($course->shortname . ': ' . $strnotes, $course->fullname, build_navigation($nav));

    $showroles = 1;
    $currenttab = 'notes';
    require($CFG->dirroot .'/user/tabs.php');

    $strsitenotes = get_string('sitenotes', 'notes');
    $strcoursenotes = get_string('coursenotes', 'notes');
    $strpersonalnotes = get_string('personalnotes', 'notes');
    $straddnewnote = get_string('addnewnote', 'notes');

    print_box_start();

    if ($courseid != SITEID) {
        //echo '<a href="#sitenotes">' . $strsitenotes . '</a> | <a href="#coursenotes">' . $strcoursenotes . '</a> | <a href="#personalnotes">' . $strpersonalnotes . '</a>';
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $addid = has_capability('moodle/notes:manage', $context) ? $courseid : 0;
        $view = has_capability('moodle/notes:view', $context);
        note_print_notes('<a name="sitenotes"></a>' . $strsitenotes, $addid, $view, 0, $userid, NOTES_STATE_SITE, 0);
        note_print_notes('<a name="coursenotes"></a>' . $strcoursenotes. ' ('.$course->fullname.')', $addid, $view, $courseid, $userid, NOTES_STATE_PUBLIC, 0);
        note_print_notes('<a name="personalnotes"></a>' . $strpersonalnotes, $addid, $view, $courseid, $userid, NOTES_STATE_DRAFT, $USER->id);

    } else {  // Normal course
        //echo '<a href="#sitenotes">' . $strsitenotes . '</a> | <a href="#coursenotes">' . $strcoursenotes . '</a>';
        $view = has_capability('moodle/notes:view', get_context_instance(CONTEXT_SYSTEM));
        note_print_notes('<a name="sitenotes"></a>' . $strsitenotes, 0, $view, 0, $userid, NOTES_STATE_SITE, 0);
        echo '<a name="coursenotes"></a>';

        if (!empty($userid)) {
            $courses = get_my_courses($userid);
            foreach($courses as $c) {
                $header = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $c->id . '">' . $c->fullname . '</a>';
                if (has_capability('moodle/notes:manage', get_context_instance(CONTEXT_COURSE, $c->id))) {
                    $addid = $c->id;
                } else {
                    $addid = 0;
                }
                note_print_notes($header, $addid, $view, $c->id, $userid, NOTES_STATE_PUBLIC, 0);
            }
        }
    }

    print_box_end();

    print_footer($course);
?>

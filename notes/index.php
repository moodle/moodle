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
 * file index.php
 * index page to view notes.
 * if a course id is specified then the entries from that course are shown
 * if a user id is specified only notes related to that user are shown
 */
require_once('../config.php');
require_once('lib.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid     = optional_param('course', SITEID, PARAM_INT);
$userid       = optional_param('user', 0, PARAM_INT);
$filtertype   = optional_param('filtertype', '', PARAM_ALPHA);
$filterselect = optional_param('filterselect', 0, PARAM_INT);

if (empty($CFG->enablenotes)) {
    print_error('notesdisabled', 'notes');
}

$url = new moodle_url('/notes/index.php');
if ($courseid != SITEID) {
    $url->param('course', $courseid);
}
if ($userid !== 0) {
    $url->param('user', $userid);
}
$PAGE->set_url($url);

// Tabs compatibility.
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

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
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
    $user = $USER;
}

require_login($course);

// Output HTML.
if ($course->id == SITEID) {
    $coursecontext = context_system::instance();
} else {
    $coursecontext = context_course::instance($course->id);
}

require_capability('moodle/notes:view', $coursecontext);
$systemcontext = context_system::instance();

// Trigger event.
note_view($coursecontext, $userid);

$strnotes = get_string('notes', 'notes');
if ($userid && $course->id == SITEID) {
    $PAGE->set_context(context_user::instance($user->id));
    $PAGE->navigation->extend_for_user($user);
    // If we are looking at our own notes, then change focus to 'my notes'.
    if ($userid == $USER->id) {
        $notenode = $PAGE->navigation->find('notes', null)->make_inactive();
    }

    $notesurl = new moodle_url('/notes/index.php', array('user' => $userid));
    $PAGE->navbar->add(get_string('notes', 'notes'), $notesurl);
} else if ($course->id != SITEID) {
    $notenode = $PAGE->navigation->find('currentcoursenotes', null)->make_inactive();

    $notesurl = new moodle_url('/notes/index.php', array('user' => $userid, 'course' => $courseid));
    $PAGE->navbar->add(get_string('notes', 'notes'), $notesurl);

    $PAGE->set_context(context_course::instance($courseid));
} else {
    $link = null;
    if (course_can_view_participants($coursecontext) || course_can_view_participants($systemcontext)) {
        $link = new moodle_url('/user/index.php', array('id' => $course->id));
    }
}

$PAGE->set_pagelayout('incourse');
$PAGE->set_title($course->fullname);
if ($course->id == SITEID) {
    $PAGE->set_heading(fullname($user));
} else {
    $PAGE->set_heading($course->fullname);
}

echo $OUTPUT->header();

if ($course->id != SITEID) {
    $headerinfo = array('heading' => fullname($user), 'user' => $user);
    echo $OUTPUT->context_header($headerinfo, 2);
}

echo $OUTPUT->heading($strnotes);

$strsitenotes = get_string('sitenotes', 'notes');
$strcoursenotes = get_string('coursenotes', 'notes');
$strpersonalnotes = get_string('personalnotes', 'notes');
$straddnewnote = get_string('addnewnote', 'notes');

echo $OUTPUT->box_start();

if ($courseid != SITEID) {
    $context = context_course::instance($courseid);
    $addid = has_capability('moodle/notes:manage', $context) ? $courseid : 0;
    $view = has_capability('moodle/notes:view', $context);
    $fullname = format_string($course->fullname, true, array('context' => $context));
    note_print_notes(
        '<a name="sitenotes"></a>' . $strsitenotes,
        $addid,
        $view,
        0,
        $userid,
        NOTES_STATE_SITE,
        0
    );
    note_print_notes(
        '<a name="coursenotes"></a>' . $strcoursenotes. ' ('.$fullname.')',
        $addid,
        $view,
        $courseid,
        $userid,
        NOTES_STATE_PUBLIC,
        0
    );
    note_print_notes(
        '<a name="personalnotes"></a>' . $strpersonalnotes,
        $addid,
        $view,
        $courseid,
        $userid,
        NOTES_STATE_DRAFT,
        $USER->id
    );

} else {  // Normal course.
    $view = has_capability('moodle/notes:view', context_system::instance());
    note_print_notes('<a name="sitenotes"></a>' . $strsitenotes, 0, $view, 0, $userid, NOTES_STATE_SITE, 0);
    echo '<a name="coursenotes"></a>';

    if (!empty($userid)) {
        $courses = enrol_get_users_courses($userid);
        foreach ($courses as $c) {
            $ccontext = context_course::instance($c->id);
            $cfullname = format_string($c->fullname, true, array('context' => $ccontext));
            $header = '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $c->id . '">' . $cfullname . '</a>';
            $viewcoursenotes = has_capability('moodle/notes:view', $ccontext);
            if (has_capability('moodle/notes:manage', $ccontext)) {
                $addid = $c->id;
            } else {
                $addid = 0;
            }
            note_print_notes($header, $addid, $viewcoursenotes, $c->id, $userid, NOTES_STATE_PUBLIC, 0);
        }
    }
}

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

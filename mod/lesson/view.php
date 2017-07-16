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
 * This page prints a particular instance of lesson
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_once($CFG->libdir . '/grade/constants.php');

$id      = required_param('id', PARAM_INT);             // Course Module ID
$pageid  = optional_param('pageid', null, PARAM_INT);   // Lesson Page ID
$edit    = optional_param('edit', -1, PARAM_BOOL);
$userpassword = optional_param('userpassword','',PARAM_RAW);
$backtocourse = optional_param('backtocourse', false, PARAM_RAW);

$cm = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST), $cm, $course);

require_login($course, false, $cm);

if ($backtocourse) {
    redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
}

// Apply overrides.
$lesson->update_effective_access($USER->id);

$url = new moodle_url('/mod/lesson/view.php', array('id'=>$id));
if ($pageid !== null) {
    $url->param('pageid', $pageid);
}
$PAGE->set_url($url);
$PAGE->force_settings_menu();

$context = $lesson->context;
$canmanage = $lesson->can_manage();

$lessonoutput = $PAGE->get_renderer('mod_lesson');

$reviewmode = $lesson->is_in_review_mode();

if ($lesson->usepassword && !empty($userpassword)) {
    require_sesskey();
}

// Check these for students only TODO: Find a better method for doing this!
if ($timerestriction = $lesson->get_time_restriction_status()) {  // Deadline restrictions.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('notavailable'));
    echo $lessonoutput->lesson_inaccessible(get_string($timerestriction->reason, 'lesson', userdate($timerestriction->time)));
    echo $lessonoutput->footer();
    exit();
} else if ($passwordrestriction = $lesson->get_password_restriction_status($userpassword)) { // Password protected lesson code.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('passwordprotectedlesson', 'lesson', format_string($lesson->name)));
    echo $lessonoutput->login_prompt($lesson, $userpassword !== '');
    echo $lessonoutput->footer();
    exit();
} else if ($dependenciesrestriction = $lesson->get_dependencies_restriction_status()) { // Check for dependencies.
    echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('completethefollowingconditions', 'lesson', format_string($lesson->name)));
    echo $lessonoutput->dependancy_errors($dependenciesrestriction->dependentlesson, $dependenciesrestriction->errors);
    echo $lessonoutput->footer();
    exit();
}

// This is called if a student leaves during a lesson.
if ($pageid == LESSON_UNSEENBRANCHPAGE) {
    $pageid = lesson_unseen_question_jump($lesson, $USER->id, $pageid);
}

// To avoid multiple calls, store the magic property firstpage.
$lessonfirstpage = $lesson->firstpage;
$lessonfirstpageid = $lessonfirstpage ? $lessonfirstpage->id : false;

// display individual pages and their sets of answers
// if pageid is EOL then the end of the lesson has been reached
// for flow, changed to simple echo for flow styles, michaelp, moved lesson name and page title down
$attemptflag = false;
if (empty($pageid)) {
    // make sure there are pages to view
    if (!$lessonfirstpageid) {
        if (!$canmanage) {
            $lesson->add_message(get_string('lessonnotready2', 'lesson')); // a nice message to the student
        } else {
            if (!$DB->count_records('lesson_pages', array('lessonid'=>$lesson->id))) {
                redirect("$CFG->wwwroot/mod/lesson/edit.php?id=$cm->id"); // no pages - redirect to add pages
            } else {
                $lesson->add_message(get_string('lessonpagelinkingbroken', 'lesson'));  // ok, bad mojo
            }
        }
    }

    // if no pageid given see if the lesson has been started
    $retries = $lesson->count_user_retries($USER->id);
    if ($retries > 0) {
        $attemptflag = true;
    }

    if (isset($USER->modattempts[$lesson->id])) {
        unset($USER->modattempts[$lesson->id]);  // if no pageid, then student is NOT reviewing
    }

    $lastpageseen = $lesson->get_last_page_seen($retries);

    // Check if the lesson was attempted in an external device like the mobile app.
    // This check makes sense only when the lesson allows offline attempts.
    if ($lesson->allowofflineattempts && $timers = $lesson->get_user_timers($USER->id, 'starttime DESC', '*', 0, 1)) {
        $timer = current($timers);
        if (!empty($timer->timemodifiedoffline)) {
            $lasttime = format_time(time() - $timer->timemodifiedoffline);
            $lesson->add_message(get_string('offlinedatamessage', 'lesson', $lasttime), 'warning');
        }
    }

    // Check to see if end of lesson was reached.
    if (($lastpageseen !== false && ($lastpageseen != LESSON_EOL))) {
        // End not reached. Check if the user left.
        if ($lesson->left_during_timed_session($retries)) {

            echo $lessonoutput->header($lesson, $cm, '', false, null, get_string('leftduringtimedsession', 'lesson'));
            if ($lesson->timelimit) {
                if ($lesson->retake) {
                    $continuelink = new single_button(new moodle_url('/mod/lesson/view.php',
                            array('id' => $cm->id, 'pageid' => $lesson->firstpageid, 'startlastseen' => 'no')),
                            get_string('continue', 'lesson'), 'get');

                    echo html_writer::div($lessonoutput->message(get_string('leftduringtimed', 'lesson'), $continuelink),
                            'center leftduring');

                } else {
                    $courselink = new single_button(new moodle_url('/course/view.php',
                            array('id' => $PAGE->course->id)), get_string('returntocourse', 'lesson'), 'get');

                    echo html_writer::div($lessonoutput->message(get_string('leftduringtimednoretake', 'lesson'), $courselink),
                            'center leftduring');
                }
            } else {
                echo $lessonoutput->continue_links($lesson, $lastpageseen);
            }
            echo $lessonoutput->footer();
            exit();
        }
    }

    if ($attemptflag) {
        if (!$lesson->retake) {
            echo $lessonoutput->header($lesson, $cm, 'view', '', null, get_string("noretake", "lesson"));
            $courselink = new single_button(new moodle_url('/course/view.php', array('id'=>$PAGE->course->id)), get_string('returntocourse', 'lesson'), 'get');
            echo $lessonoutput->message(get_string("noretake", "lesson"), $courselink);
            echo $lessonoutput->footer();
            exit();
        }
    }
    // start at the first page
    if (!$pageid = $lessonfirstpageid) {
        echo $lessonoutput->header($lesson, $cm, 'view', '', null);
        // Lesson currently has no content. A message for display has been prepared and will be displayed by the header method
        // of the lesson renderer.
        echo $lessonoutput->footer();
        exit();
    }
    /// This is the code for starting a timed test
    if(!isset($USER->startlesson[$lesson->id]) && !$canmanage) {
        $lesson->start_timer();
    }
}

$currenttab = 'view';
$extraeditbuttons = false;
$lessonpageid = null;
$timer = null;

if ($pageid != LESSON_EOL) {

    $lesson->set_module_viewed();

    $timer = null;
    // This is the code updates the lessontime for a timed test.
    $startlastseen = optional_param('startlastseen', '', PARAM_ALPHA);

    // Check to see if the user can see the left menu.
    if (!$canmanage) {
        $lesson->displayleft = lesson_displayleftif($lesson);

        $continue = ($startlastseen !== '');
        $restart  = ($continue && $startlastseen == 'yes');
        $timer = $lesson->update_timer($continue, $restart);

        // Check time limit.
        if (!$lesson->check_time($timer)) {
            redirect(new moodle_url('/mod/lesson/view.php', array('id' => $cm->id, 'pageid' => LESSON_EOL, 'outoftime' => 'normal')));
            die; // Shouldn't be reached, but make sure.
        }
    }

    list($newpageid, $page, $lessoncontent) = $lesson->prepare_page_and_contents($pageid, $lessonoutput, $reviewmode);

    if (($edit != -1) && $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    $PAGE->set_subpage($page->id);
    $currenttab = 'view';
    $extraeditbuttons = true;
    $lessonpageid = $page->id;
    $extrapagetitle = $page->title;

    lesson_add_fake_blocks($PAGE, $cm, $lesson, $timer);
    echo $lessonoutput->header($lesson, $cm, $currenttab, $extraeditbuttons, $lessonpageid, $extrapagetitle);
    if ($attemptflag) {
        // We are using level 3 header because attempt heading is a sub-heading of lesson title (MDL-30911).
        echo $OUTPUT->heading(get_string('attempt', 'lesson', $retries), 3);
    }
    // This calculates and prints the ongoing score.
    if ($lesson->ongoing && !empty($pageid) && !$reviewmode) {
        echo $lessonoutput->ongoing_score($lesson);
    }
    if ($lesson->displayleft) {
        echo '<a name="maincontent" id="maincontent" title="' . get_string('anchortitle', 'lesson') . '"></a>';
    }
    echo $lessoncontent;
    echo $lessonoutput->progress_bar($lesson);
    echo $lessonoutput->footer();

} else {

    // End of lesson reached work out grade.
    // Used to check to see if the student ran out of time.
    $outoftime = optional_param('outoftime', '', PARAM_ALPHA);

    $data = $lesson->process_eol_page($outoftime);
    $lessoncontent = $lessonoutput->display_eol_page($lesson, $data);

    lesson_add_fake_blocks($PAGE, $cm, $lesson, $timer);
    echo $lessonoutput->header($lesson, $cm, $currenttab, $extraeditbuttons, $lessonpageid, get_string("congratulations", "lesson"));
    echo $lessoncontent;
    echo $lessonoutput->footer();
}

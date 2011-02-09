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
 * This page prints a review of a particular quiz attempt
 *
 * It is used either by the student whose attempts this is, after the attempt,
 * or by a teacher reviewing another's attempt during or afterwards.
 *
 * @package mod
 * @subpackage quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');

$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$showall = optional_param('showall', 0, PARAM_BOOL);

$url = new moodle_url('/mod/quiz/review.php', array('attempt'=>$attemptid));
if ($page !== 0) {
    $url->param('page', $page);
}
if ($showall !== 0) {
    $url->param('showall', $showall);
}
$PAGE->set_url($url);

$attemptobj = quiz_attempt::create($attemptid);

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
$attemptobj->check_review_capability();

// Create an object to manage all the other (non-roles) access rules.
$accessmanager = $attemptobj->get_access_manager(time());
$options = $attemptobj->get_display_options(true);

// Permissions checks for normal users who do not have quiz:viewreports capability.
if (!$attemptobj->has_capability('mod/quiz:viewreports')) {
    // Can't review other users' attempts.
    if (!$attemptobj->is_own_attempt()) {
        throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
    }
    // Can't review during the attempt - send them back to the attempt page.
    if (!$attemptobj->is_finished()) {
        redirect($attemptobj->attempt_url(0, $page));
    }
    // Can't review unless Students may review -> Responses option is turned on.
    if (!$options->attempt) {
        $accessmanager->back_to_view_page($attemptobj->is_preview_user(),
                $accessmanager->cannot_review_message($attemptobj->get_attempt_state()));
    }
}

// Load the questions and states needed by this page.
if ($showall) {
    $questionids = $attemptobj->get_slots();
} else {
    $questionids = $attemptobj->get_slots($page);
}

// Save the flag states, if they are being changed.
if ($options->flags == question_display_options::EDITABLE && optional_param('savingflags', false, PARAM_BOOL)) {
    require_sesskey();
    $attemptobj->save_question_flags();
    redirect($attemptobj->review_url(0, $page, $showall));
}

// Log this review.
add_to_log($attemptobj->get_courseid(), 'quiz', 'review', 'review.php?attempt=' .
        $attemptobj->get_attemptid(), $attemptobj->get_quizid(), $attemptobj->get_cmid());

// Work out appropriate title and whether blocks should be shown
if ($attemptobj->is_preview_user() && $attemptobj->is_own_attempt()) {
    $strreviewtitle = get_string('reviewofpreview', 'quiz');
    navigation_node::override_active_url($attemptobj->start_attempt_url());

} else {
    $strreviewtitle = get_string('reviewofattempt', 'quiz', $attemptobj->get_attempt_number());
    if (empty($attemptobj->get_quiz()->showblocks) && !$attemptobj->is_preview_user()) {
        $PAGE->blocks->show_only_fake_blocks();
    }
}

// Arrange for the navigation to be displayed.
$navbc = $attemptobj->get_navigation_panel('quiz_review_nav_panel', $page, $showall);
$firstregion = reset($PAGE->blocks->get_regions());
$PAGE->blocks->add_fake_block($navbc, $firstregion);

// Print the page header
$headtags = $attemptobj->get_html_head_contributions($page, $showall);
if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
    $accessmanager->setup_secure_page($attemptobj->get_course()->shortname.': '.format_string($attemptobj->get_quiz_name()), $headtags);
} elseif ($accessmanager->safebrowser_required($attemptobj->is_preview_user())) {
    $PAGE->set_title($attemptobj->get_course()->shortname . ': '.format_string($attemptobj->get_quiz_name()));
    $PAGE->set_heading($attemptobj->get_course()->fullname);
    $PAGE->set_cacheable(false);
    echo $OUTPUT->header();
} else {
    $PAGE->navbar->add($strreviewtitle);
    $PAGE->set_title(format_string($attemptobj->get_quiz_name()));
    $PAGE->set_heading($attemptobj->get_course()->fullname);
    echo $OUTPUT->header();
}

// Print heading.
if ($attemptobj->is_preview_user() && $attemptobj->is_own_attempt()) {
    $attemptobj->print_restart_preview_button();
}
echo $OUTPUT->heading($strreviewtitle);

// Summary table start ============================================================================

// Work out some time-related things.
$attempt = $attemptobj->get_attempt();
$quiz = $attemptobj->get_quiz();
$overtime = 0;

if ($attempt->timefinish) {
    if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
        if($quiz->timelimit && $timetaken > ($quiz->timelimit + 60)) {
            $overtime = $timetaken - $quiz->timelimit;
            $overtime = format_time($overtime);
        }
        $timetaken = format_time($timetaken);
    } else {
        $timetaken = "-";
    }
} else {
    $timetaken = get_string('unfinished', 'quiz');
}

// Print summary table about the whole attempt.
// First we assemble all the rows that are appopriate to the current situation in
// an array, then later we only output the table if there are any rows to show.
$rows = array();
if (!$attemptobj->get_quiz()->showuserpicture && $attemptobj->get_userid() != $USER->id) {
    // If showuserpicture is true, the picture is shown elsewhere, so don't repeat it.
    $student = $DB->get_record('user', array('id' => $attemptobj->get_userid()));
    $picture = $OUTPUT->user_picture($student, array('courseid'=>$attemptobj->get_courseid()));
    $rows[] = '<tr><th scope="row" class="cell">' . $picture . '</th><td class="cell"><a href="' .
            $CFG->wwwroot . '/user/view.php?id=' . $student->id . '&amp;course=' . $attemptobj->get_courseid() . '">' .
            fullname($student, true) . '</a></td></tr>';
}
if ($attemptobj->has_capability('mod/quiz:viewreports')) {
    $attemptlist = $attemptobj->links_to_other_attempts($attemptobj->review_url(0, $page, $showall));
    if ($attemptlist) {
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('attempts', 'quiz') .
                '</th><td class="cell">' . $attemptlist . '</td></tr>';
    }
}

// Timing information.
$rows[] = '<tr><th scope="row" class="cell">' . get_string('startedon', 'quiz') .
        '</th><td class="cell">' . userdate($attempt->timestart) . '</td></tr>';
if ($attempt->timefinish) {
    $rows[] = '<tr><th scope="row" class="cell">' . get_string('completedon', 'quiz') . '</th><td class="cell">' .
            userdate($attempt->timefinish) . '</td></tr>';
    $rows[] = '<tr><th scope="row" class="cell">' . get_string('timetaken', 'quiz') . '</th><td class="cell">' .
            $timetaken . '</td></tr>';
}
if (!empty($overtime)) {
    $rows[] = '<tr><th scope="row" class="cell">' . get_string('overdue', 'quiz') . '</th><td class="cell">' . $overtime . '</td></tr>';
}

// Show marks (if the user is allowed to see marks at the moment).
$grade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);
if ($options->marks && quiz_has_grades($quiz)) {

    if (!$attempt->timefinish) {
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('grade') . '</th><td class="cell">' .
                get_string('attemptstillinprogress', 'quiz') . '</td></tr>';

    } else if (is_null($grade)) {
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('grade') . '</th><td class="cell">' .
                quiz_format_grade($quiz, $grade) . '</td></tr>';

    } else {
        // Show raw marks only if they are different from the grade (like on the view page).
        if ($quiz->grade != $quiz->sumgrades) {
            $a = new stdClass;
            $a->grade = quiz_format_grade($quiz, $attempt->sumgrades);
            $a->maxgrade = quiz_format_grade($quiz, $quiz->sumgrades);
            $rows[] = '<tr><th scope="row" class="cell">' . get_string('marks', 'quiz') . '</th><td class="cell">' .
                    get_string('outofshort', 'quiz', $a) . '</td></tr>';
        }

        // Now the scaled grade.
        $a = new stdClass;
        $a->grade = '<b>' . quiz_format_grade($quiz, $grade) . '</b>';
        $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
        if ($quiz->grade != 100) {
            $a->percent = '<b>' . round($attempt->sumgrades * 100 / $quiz->sumgrades, 0) . '</b>';
            $formattedgrade = get_string('outofpercent', 'quiz', $a);
        } else {
            $formattedgrade = get_string('outof', 'quiz', $a);
        }
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('grade') . '</th><td class="cell">' .
                $formattedgrade . '</td></tr>';
    }
}

// Feedback if there is any, and the user is allowed to see it now.
$feedback = $attemptobj->get_overall_feedback($grade);
if ($options->overallfeedback && $feedback) {
    $rows[] = '<tr><th scope="row" class="cell">' . get_string('feedback', 'quiz') .
            '</th><td class="cell">' . $feedback . '</td></tr>';
}

// Now output the summary table, if there are any rows to be shown.
if (!empty($rows)) {
    echo '<table class="generaltable generalbox quizreviewsummary"><tbody>', "\n";
    echo implode("\n", $rows);
    echo "\n</tbody></table>\n";
}

// Summary table end ==============================================================================

// Form for saving flags if necessary.
if ($options->flags == question_display_options::EDITABLE) {
    echo '<form action="' . $attemptobj->review_url(0, $page, $showall) .
            '" method="post"><div>';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
}

// Print all the questions.
if ($showall) {
    $thispage = 'all';
    $lastpage = true;
} else {
    $thispage = $page;
    $lastpage = $attemptobj->is_last_page($page);
}
foreach ($attemptobj->get_slots($thispage) as $slot) {
    echo $attemptobj->render_question($slot, true, $attemptobj->review_url($slot, $page, $showall));
}

// Close form if we opened it.
if ($options->flags == question_display_options::EDITABLE) {
    echo '<div class="submitbtns">' . "\n" .
            '<input type="submit" class="questionflagsavebutton" name="savingflags" value="' .
            get_string('saveflags', 'question') . '" />' .
            "</div>\n" .
            "\n</div></form>\n";
    $PAGE->requires->js_init_call('M.mod_quiz.init_review_form', null, false, quiz_get_js_module());
}

// Print a link to the next page.
echo '<div class="submitbtns">';
if ($lastpage) {
    $accessmanager->print_finish_review_link($attemptobj->is_preview_user());
} else {
    echo link_arrow_right(get_string('next'), $attemptobj->review_url(0, $page + 1));
}
echo '</div>';
echo $OUTPUT->footer();

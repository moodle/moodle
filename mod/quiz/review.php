<?php  // $Id$
/**
 * This page prints a review of a particular quiz attempt
 *
 * @author Martin Dougiamas and many others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $attemptid = required_param('attempt', PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);
    $showall = optional_param('showall', 0, PARAM_BOOL);

    $attemptobj = new quiz_attempt($attemptid);

/// Check login.
    require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());


/// Create an object to manage all the other (non-roles) access rules.
    $accessmanager = $attemptobj->get_access_manager(time());
    $options = $attemptobj->get_review_options();

/// Work out if this is a student viewing their own attempt/teacher previewing,
/// or someone with 'mod/quiz:viewreports' reviewing someone elses attempt.
    $reviewofownattempt = $attemptobj->get_userid() == $USER->id &&
            (!$attemptobj->is_preview_user() || $attemptobj->is_preview());

/// Permissions checks for normal users who do not have quiz:viewreports capability.
    if (!$attemptobj->has_capability('mod/quiz:viewreports')) {
    /// Can't review during the attempt - send them back to the attempt page.
        if (!$attemptobj->is_finished()) {
            redirect($attemptobj->attempt_url(0, $page));
        }
    /// Can't review other users' attempts.
        if (!$reviewofownattempt) {
            quiz_error($quiz, 'reviewnotallowed');
        }
    /// Can't review unless Students may review -> Responses option is turned on.
        if (!$options->responses) {
            $accessmanager->back_to_view_page($attemptobj->is_preview_user(),
                    $accessmanager->cannot_review_message($options));
        }
    }

/// Log this review.
    add_to_log($attemptobj->get_courseid(), 'quiz', 'review', 'review.php?attempt=' .
            $attemptobj->get_attemptid(), $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// load the questions and states needed by this page.
    if ($showall) {
        $questionids = $attemptobj->get_question_ids();
    } else {
        $questionids = $attemptobj->get_question_ids($page);
    } 
    $attemptobj->load_questions($questionids);
    $attemptobj->load_question_states($questionids);

/// Work out appropriate title.
    if ($attemptobj->is_preview_user() && $reviewofownattempt) {
        $strreviewtitle = get_string('reviewofpreview', 'quiz');
    } else {
        $strreviewtitle = get_string('reviewofattempt', 'quiz', $attempt->attempt);
    }

/// Print the page header
    $headtags = $attemptobj->get_html_head_contributions($page);
    if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
        $accessmanager->setup_secure_page($course->shortname.': '.format_string($quiz->name), $headtags);
    } else {
        print_header_simple(format_string($attemptobj->get_quiz_name()), '', $attemptobj->navigation($strreviewtitle),
                '', $headtags, true, $attemptobj->update_module_button());
    }
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print tabs if they should be there.
    if ($attemptobj->is_preview_user()) {
        if ($reviewofownattempt) {
            $currenttab = 'preview';
        } else {
            $currenttab = 'reports';
            $mode = '';
        }
        include('tabs.php');
    }

/// Print heading.
    print_heading(format_string($quiz->name));
    if ($attemptobj->is_preview_user() && $reviewofownattempt) {
        $attemptobj->print_restart_preview_button();
    }
    print_heading($strreviewtitle);

/// Finish review link.
    if ($reviewofownattempt) {
        $accessmanager->print_finish_review_link($attemptobj->is_preview_user());
    }

/// Summary table start ============================================================================

/// Work out some time-related things.
    $attempt = $attemptobj->get_attempt();
    $quiz = $attemptobj->get_quiz();
    $timelimit = $quiz->timelimit * 60;
    $overtime = 0;

    if ($attempt->timefinish) {
        if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
            if($timelimit && $timetaken > ($timelimit + 60)) {
                $overtime = $timetaken - $timelimit;
                $overtime = format_time($overtime);
            }
            $timetaken = format_time($timetaken);
        } else {
            $timetaken = "-";
        }
    } else {
        $timetaken = get_string('unfinished', 'quiz');
    }

/// Print summary table about the whole attempt.
/// First we assemble all the rows that are appopriate to the current situation in
/// an array, then later we only output the table if there are any rows to show.
    $rows = array();
    if ($attempt->userid <> $USER->id) {
        $student = $DB->get_record('user', array('id' => $attempt->userid));
        $picture = print_user_picture($student, $course->id, $student->picture, false, true);
        $rows[] = '<tr><th scope="row" class="cell">' . $picture . '</th><td class="cell"><a href="' .
                $CFG->wwwroot . '/user/view.php?id=' . $student->id . '&amp;course=' . $course->id . '">' .
                fullname($student, true) . '</a></td></tr>';
    }
    if (has_capability('mod/quiz:viewreports', $context) &&
            count($attempts = $DB->get_records_select('quiz_attempts', "quiz = ? AND userid = ?", array($quiz->id, $attempt->userid), 'attempt ASC')) > 1) {
    /// List of all this user's attempts for people who can see reports.
        $attemptlist = array();
        foreach ($attempts as $at) {
            if ($at->id == $attempt->id) {
                $attemptlist[] = '<strong>' . $at->attempt . '</strong>';
            } else {
                $attemptlist[] = '<a href="' . $attemptobj->review_url(0, $page, $showall, $at->id) .
                        '">' . $at->attempt . '</a>';
            }
        }
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('attempts', 'quiz') .
                '</th><td class="cell">' . implode(', ', $attemptlist) . '</td></tr>';
    }

/// Timing information.
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

/// Show scores (if the user is allowed to see scores at the moment).
    $grade = quiz_rescale_grade($attempt->sumgrades, $quiz);
    if ($options->scores) {
        if ($quiz->grade and $quiz->sumgrades) {
            if($overtime) {
                $result->sumgrades = "0";
                $result->grade = "0.0";
            }

        /// Show raw marks only if they are different from the grade (like on the view page.
            if ($quiz->grade != $quiz->sumgrades) {
                $a = new stdClass;
                $a->grade = round($attempt->sumgrades, $CFG->quiz_decimalpoints);
                $a->maxgrade = $quiz->sumgrades;
                $rows[] = '<tr><th scope="row" class="cell">' . get_string('marks', 'quiz') . '</th><td class="cell">' .
                        get_string('outofshort', 'quiz', $a) . '</td></tr>';
            }

        /// Now the scaled grade.
            $a = new stdClass;
            $a->grade = '<b>' . $grade . '</b>';
            $a->maxgrade = $quiz->grade;
            $a->percent = '<b>' . round(($attempt->sumgrades/$quiz->sumgrades)*100, 0) . '</b>';
            $rows[] = '<tr><th scope="row" class="cell">' . get_string('grade') . '</th><td class="cell">' .
                    get_string('outofpercent', 'quiz', $a) . '</td></tr>';
        }
    }

/// Feedback if there is any, and the user is allowed to see it now.
    $feedback = quiz_feedback_for_grade($grade, $attempt->quiz);
    if ($options->overallfeedback && $feedback) {
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('feedback', 'quiz') .
                '</th><td class="cell">' . $feedback . '</td></tr>';
    }

/// Now output the summary table, if there are any rows to be shown.
    if (!empty($rows)) {
        echo '<table class="generaltable generalbox quizreviewsummary"><tbody>', "\n";
        echo implode("\n", $rows);
        echo "\n</tbody></table>\n";
    }

/// Summary table end ==============================================================================

/// Print the navigation panel if required
    // TODO!!!
    print_paging_bar($attemptobj->get_num_pages(), $page, 1, 'review.php?attempt='.$attempt->id.'&amp;');
    echo '<div class="controls"><a href="review.php?attempt='.$attempt->id.'&amp;showall=true">';
    print_string('showall', 'quiz');
    echo '</a></div>';

/// Print all the questions
    if ($showall) {
        $page = 'all';
    }
    foreach ($attemptobj->get_question_ids($page) as $id) {
        $attemptobj->print_question($id);
    }

    // print javascript button to close the window, if necessary
    if ($reviewofownattempt) {
        $accessmanager->print_finish_review_link($attemptobj->is_preview_user());
    }

    if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
        print_footer('empty');
    } else {
        print_footer($course);
    }
?>

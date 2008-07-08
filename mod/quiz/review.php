<?php  // $Id$
/**
 * This page prints a review of a particular quiz attempt
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once('../../config.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $attemptid = required_param('attempt', PARAM_INT);    // A particular attempt ID for review
    $page = optional_param('page', 0, PARAM_INT); // The required page
    $showall = optional_param('showall', 0, PARAM_BOOL);

    if (!$attempt = quiz_load_attempt($attemptid)) {
        print_error('invalidattemptid', 'quiz');
    }
    if (! $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz))) {
        print_error('invalidquizid', 'quiz');
    }
    if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
        print_error("invalidcoursemodule");
    }
    if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
        print_error("invalidcoursemodule");
    }

/// Check login and get contexts.
    require_login($course->id, false, $cm);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $canpreview = has_capability('mod/quiz:preview', get_context_instance(CONTEXT_MODULE, $cm->id));

/// Create an object to manage all the other (non-roles) access rules.
    $timenow = time();
    $accessmanager = new quiz_access_manager(new quiz($quiz, $cm, $course), $timenow,
            has_capability('mod/quiz:ignoretimelimits', $context, NULL, false));
    $options = quiz_get_reviewoptions($quiz, $attempt, $context);

/// Work out if this is a student viewing their own attempt/teacher previewing,
/// or someone with 'mod/quiz:viewreports' reviewing someone elses attempt.
    $reviewofownattempt = $attempt->userid == $USER->id && (!$canpreview || $attempt->preview);

/// Permissions checks for normal users who do not have quiz:viewreports capability.
    if (!has_capability('mod/quiz:viewreports', $context)) {
    /// Can't review during the attempt - send them back to the attempt page.
        if (!$attempt->timefinish) {
            redirect($CFG->wwwroot . '/mod/quiz/attempt.php?q=' . $quiz->id);
        }
    /// Can't review other users' attempts.
        if (!$reviewofownattempt) {
            quiz_error($quiz, 'reviewnotallowed');
        }
    /// Can't review unless Students may review -> Responses option is turned on.
        if (!$options->responses) {
            $accessmanager->back_to_view_page($canpreview,
                    $accessmanager->cannot_review_message($options));
        }
    }

/// Log this review.
    add_to_log($course->id, "quiz", "review", "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", "$cm->id");

/// load the questions needed by page
    if ($showall) {
        $questionlist = quiz_questions_in_quiz($attempt->layout);
    } else {
        $questionlist = quiz_questions_on_page($attempt->layout, $page);
    }
    $pagequestions = explode(',', $questionlist);
    $questions = question_load_questions($questionlist, 'qqi.grade AS maxgrade, qqi.id AS instance',
            '{quiz_question_instances} qqi ON qqi.quiz = ' . $quiz->id . ' AND q.id = qqi.question');
    if (is_string($questions)) {
        quiz_error($quiz, 'loadingquestionsfailed', $questions);
    }

/// Restore the question sessions to their most recent states creating new sessions where required.
    if (!$states = get_question_states($questions, $quiz, $attempt)) {
        print_error('cannotrestore', 'quiz');
    }

/// Work out appropriate title.
    if ($canpreview && $reviewofownattempt) {
        $strreviewtitle = get_string('reviewofpreview', 'quiz');
    } else {
        $strreviewtitle = get_string('reviewofattempt', 'quiz', $attempt->attempt);
    }

/// Print the page header
    $headtags = get_html_head_contributions($pagequestions, $questions, $states);
    if ($accessmanager->securewindow_required($canpreview)) {
        $accessmanager->setup_secure_page($course->shortname.': '.format_string($quiz->name), $headtags);
    } else {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        get_string('reviewofattempt', 'quiz', $attempt->attempt);
        $navigation = build_navigation($strreviewtitle, $cm);
        print_header_simple(format_string($quiz->name), "", $navigation, "", $headtags, true, $strupdatemodule);
    }
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print tabs if they should be there.
    if ($canpreview) {
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
    if ($canpreview && $reviewofownattempt) {
        $attemptobj = new quiz_attempt($attemptid);
        $attemptobj->print_restart_preview_button();
    }
    print_heading($strreviewtitle);

/// Finish review link.
    if ($reviewofownattempt) {
        $accessmanager->print_finish_review_link($canpreview);
    }

/// Work out some time-related things.
    $timelimit = (int)$quiz->timelimit * 60;
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
        $urloptions = '';
        if ($showall) {
            $urloptions .= '&amp;showall=true';
        } else if ($page > 0) {
            $urloptions .= '&amp;page=' . $page;
        }
        $attemptlist = array();
        foreach ($attempts as $at) {
            if ($at->id == $attempt->id) {
                $attemptlist[] = '<strong>' . $at->attempt . '</strong>';
            } else {
                $attemptlist[] = '<a href="review.php?attempt=' . $at->id . $urloptions . ' ">' . $at->attempt . '</a>';
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

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1 and !$showall) {
        print_paging_bar($numpages, $page, 1, 'review.php?attempt='.$attempt->id.'&amp;');
        echo '<div class="controls"><a href="review.php?attempt='.$attempt->id.'&amp;showall=true">';
        print_string('showall', 'quiz');
        echo '</a></div>';
    }

/// Print all the questions
    $number = quiz_first_questionnumber($attempt->layout, $questionlist);
    foreach ($pagequestions as $i) {
        if (!isset($questions[$i])) {
            print_simple_box_start('center', '90%');
            echo '<strong><font size="+1">' . $number . '</font></strong><br />';
            notify(get_string('errormissingquestion', 'quiz', $i));
            print_simple_box_end();
            $number++; // Just guessing that the missing question would have lenght 1
            continue;
        }
        $options->validation = QUESTION_EVENTVALIDATE === $states[$i]->event;
        $options->history = ($canpreview and !$attempt->preview) ? 'all' : 'graded';
        // Print the question
        print_question($questions[$i], $states[$i], $number, $quiz, $options);
        $number += $questions[$i]->length;
    }

    // Print the navigation panel if required
    if ($numpages > 1 and !$showall) {
        print_paging_bar($numpages, $page, 1, 'review.php?attempt='.$attempt->id.'&amp;');
    }

    // print javascript button to close the window, if necessary
    if ($reviewofownattempt) {
        $accessmanager->print_finish_review_link($canpreview);
    }

    if ($accessmanager->securewindow_required($canpreview)) {
        print_footer('empty');
    } else {
        print_footer($course);
    }
?>

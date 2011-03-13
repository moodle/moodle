<?php
/**
 * This page prints a review of a particular quiz attempt
 *
 * @author Martin Dougiamas and many others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
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

/// Check login.
    require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
    $attemptobj->check_review_capability();

/// Create an object to manage all the other (non-roles) access rules.
    $accessmanager = $attemptobj->get_access_manager(time());
    $options = $attemptobj->get_review_options();

    // Check permissions.
    if ($attemptobj->is_own_attempt()) {
        if (!$attemptobj->is_finished()) {
            redirect($attemptobj->attempt_url(0, $page));
        } else if (!$options->responses) {
            $accessmanager->back_to_view_page($attemptobj->is_preview_user(),
                    $accessmanager->cannot_review_message($options));
        }

    } else if (!$attemptobj->is_review_allowed()) {
        throw new moodle_quiz_exception($attemptobj, 'noreviewattempt');
    }

/// Load the questions and states needed by this page.
    if ($showall) {
        $questionids = $attemptobj->get_question_ids();
    } else {
        $questionids = $attemptobj->get_question_ids($page);
    }
    $attemptobj->load_questions($questionids);
    $attemptobj->load_question_states($questionids);

/// Save the flag states, if they are being changed.
    if ($options->flags == QUESTION_FLAGSEDITABLE && optional_param('savingflags', false, PARAM_BOOL)) {
        require_sesskey();
        $formdata = data_submitted();

        question_save_flags($formdata, $attemptid, $questionids);
        redirect($attemptobj->review_url(0, $page, $showall));
    }

/// Log this review.
    add_to_log($attemptobj->get_courseid(), 'quiz', 'review', 'review.php?attempt=' .
            $attemptobj->get_attemptid(), $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// Work out appropriate title and whether blocks should be shown
    if ($attemptobj->is_preview_user() && $attemptobj->is_own_attempt()) {
        // Normal blocks
        $strreviewtitle = get_string('reviewofpreview', 'quiz');
        navigation_node::override_active_url($attemptobj->start_attempt_url());

    } else {
        $strreviewtitle = get_string('reviewofattempt', 'quiz', $attemptobj->get_attempt_number());
        if (empty($attemptobj->get_quiz()->showblocks) && !$attemptobj->is_preview_user()) {
            // Only show pretend blocks
            $PAGE->blocks->show_only_fake_blocks();
        }
    }

    // Initialise the JavaScript.
    $headtags = $attemptobj->get_html_head_contributions($page);

    // Arrange for the navigation to be displayed.
    $navbc = $attemptobj->get_navigation_panel('quiz_review_nav_panel', $page, $showall);
    $firstregion = reset($PAGE->blocks->get_regions());
    $PAGE->blocks->add_fake_block($navbc, $firstregion);

/// Print the page header
    $headtags = $attemptobj->get_html_head_contributions($page);
    if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
        $accessmanager->setup_secure_page($attemptobj->get_course()->shortname.': '.format_string($attemptobj->get_quiz_name()), $headtags);
    } elseif ($accessmanager->safebrowser_required($attemptobj->is_preview_user())) {
        $PAGE->set_title($attemptobj->get_course()->shortname . ': '.format_string($attemptobj->get_quiz_name()));
        $PAGE->set_heading($attemptobj->get_course()->fullname);
        $PAGE->set_cacheable(false);
        echo $OUTPUT->header();
    } else {
        $attemptobj->navigation($strreviewtitle);
        $PAGE->set_title(format_string($attemptobj->get_quiz_name()));
        $PAGE->set_heading($attemptobj->get_course()->fullname);
        echo $OUTPUT->header();
    }

/// Print heading.
    if ($attemptobj->is_preview_user() && $attemptobj->is_own_attempt()) {
        $attemptobj->print_restart_preview_button();
    }
    echo $OUTPUT->heading($strreviewtitle);

/// Summary table start ============================================================================

/// Work out some time-related things.
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

/// Print summary table about the whole attempt.
/// First we assemble all the rows that are appopriate to the current situation in
/// an array, then later we only output the table if there are any rows to show.
    $rows = array();
    if (!$attemptobj->get_quiz()->showuserpicture && $attemptobj->get_userid() <> $USER->id) {
    /// If showuserpicture is true, the picture is shown elsewhere, so don't repeat it.
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
    $grade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);
    if ($options->scores) {
        if (quiz_has_grades($quiz)) {
            if($overtime) {
                $result->sumgrades = "0";
                $result->grade = "0.0";
            }

        /// Show raw marks only if they are different from the grade (like on the view page.
            if ($quiz->grade != $quiz->sumgrades) {
                $a = new stdClass;
                $a->grade = quiz_format_grade($quiz, $attempt->sumgrades);
                $a->maxgrade = quiz_format_grade($quiz, $quiz->sumgrades);
                $rows[] = '<tr><th scope="row" class="cell">' . get_string('marks', 'quiz') . '</th><td class="cell">' .
                        get_string('outofshort', 'quiz', $a) . '</td></tr>';
            }

        /// Now the scaled grade.
            $a = new stdClass;
            $a->grade = '<b>' . quiz_format_grade($quiz, $grade) . '</b>';
            $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
            $a->percent = '<b>' . round(($attempt->sumgrades/$quiz->sumgrades)*100, 0) . '</b>';
            $rows[] = '<tr><th scope="row" class="cell">' . get_string('grade') . '</th><td class="cell">' .
                    get_string('outofpercent', 'quiz', $a) . '</td></tr>';
        }
    }

/// Feedback if there is any, and the user is allowed to see it now.
    $feedback = $attemptobj->get_overall_feedback($grade);
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

/// Form for saving flags if necessary.
    if ($options->flags == QUESTION_FLAGSEDITABLE) {
        echo '<form action="' . s($attemptobj->review_url(0, $page, $showall)) .
                '" method="post" class="questionflagsaveform"><div>';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    }

/// Print all the questions.
    if ($showall) {
        $thispage = 'all';
        $lastpage = true;
    } else {
        $thispage = $page;
        $lastpage = $attemptobj->is_last_page($page);
    }
    foreach ($attemptobj->get_question_ids($thispage) as $id) {
        $attemptobj->print_question($id, true, $attemptobj->review_url($id, $page, $showall));
    }

/// Close form if we opened it.
    if ($options->flags == QUESTION_FLAGSEDITABLE) {
        echo '<div class="submitbtns">' . "\n" .
                '<input type="submit" class="questionflagsavebutton" name="savingflags" value="' .
                get_string('saveflags', 'question') . '" />' .
                "</div>\n" .
                "\n</div></form>\n";
        $PAGE->requires->js_init_call('M.mod_quiz.init_review_form', null, false, quiz_get_js_module());
    }

/// Print a link to the next page.
    echo '<div class="submitbtns">';
    if ($lastpage) {
        $accessmanager->print_finish_review_link($attemptobj->is_preview_user());
    } else {
        echo link_arrow_right(get_string('next'), s($attemptobj->review_url(0, $page + 1)));
    }
    echo "</div>";

    echo $OUTPUT->footer();


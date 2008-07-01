<?php  // $Id$
/**
 * This page prints a particular instance of quiz
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

/// remember the current time as the time any responses were submitted
/// (so as to make sure students don't get penalized for slow processing on this page)
    $timenow = time();

/// Get submitted parameters.
    $id = optional_param('id', 0, PARAM_INT);               // Course Module ID
    $q = optional_param('q', 0, PARAM_INT);                 // or quiz ID
    $page = optional_param('page', 0, PARAM_INT);
    $questionids = optional_param('questionids', '');
    $finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
    $timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.
    $forcenew = optional_param('forcenew', false, PARAM_BOOL); // Teacher has requested new preview

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
            print_error("coursemisconf");
        }
        if (! $quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else {
        if (! $quiz = $DB->get_record('quiz', array('id' => $q))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
            print_error('invalidcourseid');
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    }

/// We treat automatically closed attempts just like normally closed attempts
    if ($timeup) {
        $finishattempt = 1;
    }

/// Check login and get contexts.
    require_login($course->id, false, $cm);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $canpreview = has_capability('mod/quiz:preview', $context);

/// Create an object to manage all the other (non-roles) access rules.
    $accessmanager = new quiz_access_manager($quiz, $timenow,
            has_capability('mod/quiz:ignoretimelimits', $context, NULL, false));
    if ($canpreview && $forcenew) {
        $accessmanager->clear_password_access();
    }

/// if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions && has_capability('mod/quiz:manage', $context)) {
        redirect($CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $cm->id);
    }

/// Check capabilites.
    if (!$canpreview) {
        require_capability('mod/quiz:attempt', $context);
    }
/// We intentionally do not check otehr access rules until after we have processed
/// any submitted responses (which would be sesskey protected). This is so that when
/// someone submits close to the exact moment when the quiz closes, there responses are not lost.

/// Load attempt or create a new attempt if there is no unfinished one

/// Check to see if a new preview was requested.
    if ($canpreview && $forcenew) {
    /// Teacher wants a new preview, so we set a finish time on the
    /// current attempt (if any). It will then automatically be deleted below
        $DB->set_field('quiz_attempts', 'timefinish', $timenow, array('quiz' => $quiz->id, 'userid' => $USER->id));
    }

/// Look for an existing attempt.
    $newattempt = false;
    $lastattempt = quiz_get_latest_attempt_by_user($quiz->id, $USER->id);

    if ($lastattempt && !$lastattempt->timefinish) {
    /// Continuation of an attempt.
        $attempt = $lastattempt;
        $lastattemptid = false;

    /// Log it, but only if some time has elapsed.
        if (($timenow - $attempt->timemodified) > QUIZ_CONTINUE_ATTEMPT_LOG_INTERVAL) {
        /// This action used to be 'continue attempt' but the database field has only 15 characters.
            add_to_log($course->id, 'quiz', 'continue attemp', "review.php?attempt=$attempt->id",
                    "$quiz->id", $cm->id);
        }

    } else {
    /// Start a new attempt.
        $newattempt = true;

    /// Get number for the next or unfinished attempt
        if ($lastattempt && !$lastattempt->preview && !$canpreview) {
            $attemptnumber = $lastattempt->attempt + 1;
            $lastattemptid = $lastattempt->id;
        } else {
            $lastattempt = false;
            $lastattemptid = false;
            $attemptnumber = 1;
        }

    /// Check access.
        $messages = $accessmanager->prevent_access() +
                $accessmanager->prevent_new_attempt($attemptnumber - 1, $lastattempt);
        if (!$canpreview && $messages) {
            //TODO: need more detailed error info
            print_error('attempterror', 'quiz', $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz->id);
        }
        $accessmanager->do_password_check($canpreview);

    /// Delete any previous preview attempts belonging to this user.
        if ($oldattempts = $DB->get_records_select('quiz_attempts', "quiz = ?
                AND userid = ? AND preview = 1", array($quiz->id, $USER->id))) {
            foreach ($oldattempts as $oldattempt) {
                quiz_delete_attempt($oldattempt, $quiz);
            }
        }

    /// Create the new attempt and initialize the question sessions
        $attempt = quiz_create_attempt($quiz, $attemptnumber, $lastattempt, $timenow, $canpreview);

    /// Save the attempt in the database.
        if (!$attempt->id = $DB->insert_record('quiz_attempts', $attempt)) {
            quiz_error($quiz, 'newattemptfail');
        }

    /// Log the new attempt.
        if ($attempt->preview) {
            add_to_log($course->id, 'quiz', 'preview', "attempt.php?id=$cm->id",
                    "$quiz->id", $cm->id);
        } else {
            add_to_log($course->id, 'quiz', 'attempt', "review.php?attempt=$attempt->id",
                    "$quiz->id", $cm->id);
        }
    }

/// This shouldn't really happen, just for robustness
    if (!$attempt->timestart) {
        debugging('timestart was not set for this attempt. That should be impossible.', DEBUG_DEVELOPER);
        $attempt->timestart = $timenow - 1;
    }

/// Load all the questions and states needed by this script

/// Get the list of questions needed by this page.
    $pagelist = quiz_questions_on_page($attempt->layout, $page);

    if ($newattempt || $finishattempt) {
        $questionlist = quiz_questions_in_quiz($attempt->layout);
    } else {
        $questionlist = $pagelist;
    }

/// Add all questions that are on the submitted form
    if ($questionids) {
        $questionlist .= ','.$questionids;
    }

    if (!$questionlist) {
        quiz_error($quiz, 'noquestionsfound');
    }

    $questions = question_load_questions($questionlist, 'qqi.grade AS maxgrade, qqi.id AS instance',
            '{quiz_question_instances} qqi ON qqi.quiz = ' . $quiz->id . ' AND q.id = qqi.question');
    if (is_string($questions)) {
        quiz_error($quiz, 'loadingquestionsfailed', $questions);
    }

/// Restore the question sessions to their most recent states creating new sessions where required.
    if (!$states = get_question_states($questions, $quiz, $attempt, $lastattemptid)) {
        print_error('cannotrestore', 'quiz');
    }

/// If we are starting a new attempt, save all the newly created states.
    if ($newattempt) {
        foreach ($questions as $i => $question) {
            save_question_session($questions[$i], $states[$i]);
        }
    }

/// Process form data /////////////////////////////////////////////////

    if ($responses = data_submitted() and empty($responses->quizpassword)) {

    /// Set the default event. This can be overruled by individual buttons.
        if (array_key_exists('markall', $responses)) {
            $event = QUESTION_EVENTSUBMIT;
        } else if ($finishattempt) {
            $event = QUESTION_EVENTCLOSE;
        } else {
            $event = QUESTION_EVENTSAVE;
        }

    /// Unset any variables we know are not responses
        unset($responses->id);
        unset($responses->q);
        unset($responses->oldpage);
        unset($responses->newpage);
        unset($responses->review);
        unset($responses->questionids);
        unset($responses->saveattempt); // responses get saved anway
        unset($responses->finishattempt); // same as $finishattempt
        unset($responses->markall);
        unset($responses->forcenewattempt);

    /// Extract the responses. $actions will be an array indexed by the questions ids.
        $actions = question_extract_responses($questions, $responses, $event);

    /// Process each question in turn
        $questionidarray = explode(',', $questionids);
        $success = true;
        foreach($questionidarray as $i) {
            if (!isset($actions[$i])) {
                $actions[$i]->responses = array('' => '');
                $actions[$i]->event = QUESTION_EVENTOPEN;
            }
            $actions[$i]->timestamp = $timenow;
            if (question_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt)) {
                save_question_session($questions[$i], $states[$i]);
            } else {
                $success = false;
            }
        }

        if (!$success) {
            $pagebit = '';
            if ($page) {
                $pagebit = '&amp;page=' . $page;
            }
            print_error('errorprocessingresponses', 'question',
                    $CFG->wwwroot . '/mod/quiz/attempt.php?q=' . $quiz->id . $pagebit);
        }

        $attempt->timemodified = $timenow;
        if (!$DB->update_record('quiz_attempts', $attempt)) {
            quiz_error($quiz, 'saveattemptfailed');
        }
    }

/// Finish attempt if requested
    if ($finishattempt) {

    /// Set the attempt to be finished
        $attempt->timefinish = $timenow;

    /// Move each question to the closed state.
        $success = true;
        foreach ($questions as $key => $question) {
            $action->event = QUESTION_EVENTCLOSE;
            $action->responses = $states[$key]->responses;
            $action->timestamp = $states[$key]->timestamp;
            if (question_process_responses($question, $closestates[$key], $action, $quiz, $attempt)) {
                save_question_session($question, $closestates[$key]);
            } else {
                $success = false;
            }
        }

        if (!$success) {
            $pagebit = '';
            if ($page) {
                $pagebit = '&amp;page=' . $page;
            }
            print_error('errorprocessingresponses', 'question',
                    $CFG->wwwroot . '/mod/quiz/attempt.php?q=' . $quiz->id . $pagebit);
        }

    /// Log the end of this attempt.
        add_to_log($course->id, 'quiz', 'close attempt', "review.php?attempt=$attempt->id",
                "$quiz->id", $cm->id);

    /// Update the quiz attempt record.
        if (!$DB->update_record('quiz_attempts', $attempt)) {
            quiz_error($quiz, 'saveattemptfailed');
        }

        if (!$attempt->preview) {
        /// Record this user's best grade (if this is not a preview).
            quiz_save_best_grade($quiz);

        /// Send any notification emails (if this is not a preview).
            quiz_send_notification_emails($course, $quiz, $attempt, $context, $cm);
        }

    /// Clear the password check flag in the session.
        $accessmanager->clear_password_access();

    /// Send the user to the review page.
        redirect($CFG->wwwroot . '/mod/quiz/review.php?attempt='.$attempt->id, 0);
    }

/// Now is the right time to check access (unless we are starting a new attempt, and did it above).
    if (!$newattempt) {
        $messages = $accessmanager->prevent_access();
        if (!$canpreview && $messages) {
            //TODO: need more detailed error info
            print_error('attempterror', 'quiz', $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz->id);
        }
        $accessmanager->do_password_check($canpreview);
    }

/// Print the quiz page ////////////////////////////////////////////////////////

    // Print the page header
    require_js($CFG->wwwroot . '/mod/quiz/quiz.js');
    $pagequestions = explode(',', $pagelist);
    $strattemptnum = get_string('attempt', 'quiz', $attempt->attempt);
    $headtags = get_html_head_contributions($pagequestions, $questions, $states);
    if ($accessmanager->securewindow_required($canpreview)) {
        $accessmanager->setup_secure_page($course->shortname.': '.format_string($quiz->name), $headtags);
    } else {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        $navigation = build_navigation($strattemptnum, $cm);
        print_header_simple(format_string($quiz->name), "", $navigation, "", $headtags, true, $strupdatemodule);
    }
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    if ($canpreview) {
    /// Show the tab bar.
        $currenttab = 'preview';
        include('tabs.php');

    /// Heading and tab bar.
        print_heading(get_string('previewquiz', 'quiz', format_string($quiz->name)));
        print_restart_preview_button($quiz);

    /// Inform teachers of any restrictions that would apply to students at this point.
        if ($messages) {
            print_box_start('quizaccessnotices');
            print_heading(get_string('accessnoticesheader', 'quiz'), '', 3);
            $accessmanager->print_messages($messages);
            print_box_end();
        }
    } else {
    /// Just a heading.
        if ($quiz->attempts != 1) {
            print_heading(format_string($quiz->name).' - '.$strattemptnum);
        } else {
            print_heading(format_string($quiz->name));
        }
    }

    // Start the form
    echo '<form id="responseform" method="post" action="attempt.php?q=', s($quiz->id), '&amp;page=', s($page),
            '" enctype="multipart/form-data"' .
            ' onclick="this.autocomplete=\'off\'" onkeypress="return check_enter(event);">', "\n";
    if($quiz->timelimit > 0) {
        // Make sure javascript is enabled for time limited quizzes
        ?>
        <script type="text/javascript">
            // Do nothing, but you have to have a script tag before a noscript tag.
        </script>
        <noscript>
        <div>
        <?php print_heading(get_string('noscript', 'quiz')); ?>
        </div>
        </noscript>
        <?php
    }
    echo '<div>';

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1) {
        quiz_print_navigation_panel($page, $numpages);
    }

/// Print all the questions
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        $options = quiz_get_renderoptions($quiz->review, $states[$i]);
        // Print the question
        print_question($questions[$i], $states[$i], $number, $quiz, $options);
        save_question_session($questions[$i], $states[$i]);
        $number += $questions[$i]->length;
    }

/// Print the submit buttons
    $strconfirmattempt = get_string("confirmclose", "quiz");
    $onclick = "return confirm('$strconfirmattempt')";
    echo "<div class=\"submitbtns mdl-align\">\n";

    echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
    if ($quiz->optionflags & QUESTION_ADAPTIVE) {
        echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
    }
    echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";

    echo "</div>";

    // Print the navigation panel if required
    if ($numpages > 1) {
        quiz_print_navigation_panel($page, $numpages);
    }

    // Finish the form
    echo '</div>';
    echo '<input type="hidden" name="timeup" id="timeup" value="0" />';

    // Add a hidden field with questionids. Do this at the end of the form, so
    // if you navigate before the form has finished loading, it does not wipe all
    // the student's answers.
    echo '<input type="hidden" name="questionids" value="'.$pagelist."\" />\n";

    echo "</form>\n";

    // Finish the page
    $accessmanager->show_attempt_timer_if_needed($attempt, time());
    if ($accessmanager->securewindow_required($canpreview)) {
        print_footer('empty');
    } else {
        print_footer($course);
    }
?>

<?php
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

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/// Look for old-style URLs, such as may be in the logs, and redirect them to startattemtp.php
    if ($id = optional_param('id', 0, PARAM_INTEGER)) {
        redirect($CFG->wwwroot . '/mod/quiz/startattempt.php?cmid=' . $id . '&sesskey=' . sesskey());
    } else if ($qid = optional_param('q', 0, PARAM_INTEGER)) {
        if (!$cm = get_coursemodule_from_instance('quiz', $qid)) {
            print_error('invalidquizid', 'quiz');
        }
        redirect($CFG->wwwroot . '/mod/quiz/startattempt.php?cmid=' . $cm->id . '&sesskey=' . sesskey());
    }

/// Get submitted parameters.
    $attemptid = required_param('attempt', PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);

    $url = new moodle_url('/mod/quiz/attempt.php', array('attempt'=>$attemptid));
    if ($page !== 0) {
        $url->param('page', $page);
    }
    $PAGE->set_url($url);

    $attemptobj = quiz_attempt::create($attemptid);

/// Check login.
    require_login($attemptobj->get_course(), false, $attemptobj->get_cm());

/// Check that this attempt belongs to this user.
    if ($attemptobj->get_userid() != $USER->id) {
        if ($attemptobj->has_capability('mod/quiz:viewreports')) {
            redirect($attemptobj->review_url(0, $page));
        } else {
            quiz_error($attemptobj->get_quiz(), 'notyourattempt');
        }
    }

/// Check capabilites and block settings
    if (!$attemptobj->is_preview_user()) {
        $attemptobj->require_capability('mod/quiz:attempt');
        if (empty($attemptobj->get_quiz()->showblocks)) {
            $PAGE->blocks->show_only_fake_blocks();
        }
    }

/// If the attempt is already closed, send them to the review page.
    if ($attemptobj->is_finished()) {
        redirect($attemptobj->review_url(0, $page));
    }

/// Check the access rules.
    $accessmanager = $attemptobj->get_access_manager(time());
    $messages = $accessmanager->prevent_access();
    if (!$attemptobj->is_preview_user() && $messages) {
        print_error('attempterror', 'quiz', $quizobj->view_url(),
                $accessmanager->print_messages($messages, true));
    }
    $accessmanager->do_password_check($attemptobj->is_preview_user());

/// This action used to be 'continue attempt' but the database field has only 15 characters.
    add_to_log($attemptobj->get_courseid(), 'quiz', 'continue attemp',
            'review.php?attempt=' . $attemptobj->get_attemptid(),
            $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// Get the list of questions needed by this page.
    $questionids = $attemptobj->get_question_ids($page);

/// Check.
    if (empty($questionids)) {
        quiz_error($quiz, 'noquestionsfound');
    }

/// Load those questions and the associated states.
    $attemptobj->load_questions($questionids);
    $attemptobj->load_question_states($questionids);

/// Print the quiz page ////////////////////////////////////////////////////////
    $PAGE->requires->js('/lib/overlib/overlib.js', true);
    $PAGE->requires->js('/lib/overlib/overlib_cssstyle.js', true);

    

    // Arrange for the navigation to be displayed.
    $navbc = $attemptobj->get_navigation_panel('quiz_attempt_nav_panel', $page);
    $firstregion = reset($PAGE->blocks->get_regions());
    $PAGE->blocks->add_pretend_block($navbc, $firstregion);

    // Print the page header
    $title = get_string('attempt', 'quiz', $attemptobj->get_attempt_number());
    $headtags = $attemptobj->get_html_head_contributions($page);
    $PAGE->set_heading($attemptobj->get_course()->fullname);
    if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
        $accessmanager->setup_secure_page($attemptobj->get_course()->shortname . ': ' .
                format_string($attemptobj->get_quiz_name()), $headtags);
    } elseif ($accessmanager->safebrowser_required($attemptobj->is_preview_user())) {
        $PAGE->set_title($attemptobj->get_course()->shortname . ': '.format_string($attemptobj->get_quiz_name()));
        $PAGE->set_cacheable(false);
        echo $OUTPUT->header();
    } else {
        $PAGE->set_title(format_string($attemptobj->get_quiz_name()));
        echo $OUTPUT->header();
    }
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    if ($attemptobj->is_preview_user()) {

        $quiz = $attemptobj->get_quiz();

    /// Heading and tab bar.
        echo $OUTPUT->heading(get_string('previewquiz', 'quiz', format_string($quiz->name)));
        $attemptobj->print_restart_preview_button();

    /// Inform teachers of any restrictions that would apply to students at this point.
        if ($messages) {
            echo $OUTPUT->box_start('quizaccessnotices');
            echo $OUTPUT->heading(get_string('accessnoticesheader', 'quiz'), 3);
            $accessmanager->print_messages($messages);
            echo $OUTPUT->box_end();
        }
    }

    // Start the form
    echo '<form id="responseform" method="post" action="', s($attemptobj->processattempt_url()),
            '" enctype="multipart/form-data" accept-charset="utf-8">', "\n";

    // A quiz page with a lot of questions can take a long time to load, and we
    // want the protection afforded by init_quiz_form immediately, so include the
    // JS now.
    echo html_writer::script(js_writer::function_call('init_quiz_form'));
    echo '<div>';

/// Print all the questions
    foreach ($attemptobj->get_question_ids($page) as $id) {
        $attemptobj->print_question($id, false, $attemptobj->attempt_url($id, $page));
    }

/// Print a link to the next page.
    echo '<div class="submitbtns">';
    if ($attemptobj->is_last_page($page)) {
        $nextpage = -1;
    } else {
        $nextpage = $page + 1;
    }
    echo '<input type="submit" value="' . get_string('next') . '" />';
    echo "</div>";

    // Some hidden fields to trach what is going on.
    echo '<input type="hidden" name="attempt" value="' . $attemptobj->get_attemptid() . '" />';
    echo '<input type="hidden" name="nextpage" id="nextpagehiddeninput" value="' . $nextpage . '" />';
    echo '<input type="hidden" name="timeup" id="timeup" value="0" />';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

    // Add a hidden field with questionids. Do this at the end of the form, so
    // if you navigate before the form has finished loading, it does not wipe all
    // the student's answers.
    echo '<input type="hidden" name="questionids" value="' .
            implode(',', $attemptobj->get_question_ids($page)) . "\" />\n";

    // Finish the form
    echo '</div>';
    echo "</form>\n";

    // Finish the page
    $accessmanager->show_attempt_timer_if_needed($attemptobj->get_attempt(), time());
    echo $OUTPUT->footer();


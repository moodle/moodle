<?php  // $Id$
/**
 * This page allows the teacher to enter a manual grade for a particular question.
 * This page is expected to only be used in a popup window.
 *  *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once('../../config.php');
    require_once('locallib.php');

    $attemptid =required_param('attempt', PARAM_INT); // attempt id
    $questionid =required_param('question', PARAM_INT); // question id

    $attemptobj = new quiz_attempt($attemptid);

/// Can only grade finished attempts.
    if (!$attemptobj->is_finished()) {
        print_error('attemptclosed', 'quiz');
    }

/// Check login and permissions.
    require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());
    $attemptobj->require_capability('mod/quiz:grade');

/// Load the questions and states.
    $questionids = array($questionid);
    $attemptobj->load_questions($questionids);
    $attemptobj->load_question_states($questionids);

/// Log this action.
    add_to_log($attemptobj->get_courseid(), 'quiz', 'manualgrade', 'comment.php?attempt=' .
            $attemptobj->get_attemptid() . '&question=' . $questionid,
            $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// Print the page header
    print_header();
    print_heading(format_string($attemptobj->get_question($questionid)->name));

/// Process any data that was submitted.
    if ($data = data_submitted() and confirm_sesskey()) {
        $error = $attemptobj->process_comment($questionid,
                $data->response['comment'], $data->response['grade']);

    /// If success, notify and print a close button.
        if (!is_string($error)) {
            notify(get_string('changessaved'), 'notifysuccess');
            close_window_button('closewindow', false, true);
            print_footer();
            exit;
        }

    /// Otherwise, display the error and fall throug to re-display the form.
        notify($error);
    }

/// Print the comment form.
    echo '<form method="post" action="' . $CFG->wwwroot . '/mod/quiz/comment.php">';
    $attemptobj->question_print_comment_fields($questionid, 'response');
    echo '<input type="hidden" name="attempt" value="' . $attemptobj->get_uniqueid() . '" />';
    echo '<input type="hidden" name="question" value="' . $questionid . '" />';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';
    echo '<input type="submit" name="submit" value="' . get_string('save', 'quiz') . '" />';
    echo '</form>';

/// End of the page.
    print_footer();
?>

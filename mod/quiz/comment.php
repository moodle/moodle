<?php
/**
 * This page allows the teacher to enter a manual grade for a particular question.
 * This page is expected to only be used in a popup window.
 *  *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once('../../config.php');
    require_once('locallib.php');

    $attemptid = required_param('attempt', PARAM_INT); // attempt id
    $questionid = required_param('question', PARAM_INT); // question id

    $PAGE->set_url('/mod/quiz/comment.php', array('attempt'=>$attemptid, 'question'=>$questionid));

    $attemptobj = quiz_attempt::create($attemptid);

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
    $PAGE->set_pagelayout('popup');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($attemptobj->get_question($questionid)->name));

/// Process any data that was submitted.
    if ($data = data_submitted() and confirm_sesskey()) {
        $error = $attemptobj->process_comment($questionid,
                $data->response['comment'], FORMAT_HTML, $data->response['grade']);

    /// If success, notify and print a close button.
        if (!is_string($error)) {
            echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
            close_window(2, true);
        }

    /// Otherwise, display the error and fall throug to re-display the form.
        echo $OUTPUT->notification($error);
    }

/// Print the comment form.
    echo '<form method="post" class="mform" id="manualgradingform" action="' . $CFG->wwwroot . '/mod/quiz/comment.php">';
    $attemptobj->question_print_comment_fields($questionid, 'response');
?>
<div>
    <input type="hidden" name="attempt" value="<?php echo $attemptobj->get_uniqueid(); ?>" />
    <input type="hidden" name="question" value="<?php echo $questionid; ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
</div>
<fieldset class="hidden">
    <div>
        <div class="fitem">
            <div class="fitemtitle">
                <div class="fgrouplabel"><label> </label></div>
            </div>
            <fieldset class="felement fgroup">
                <input id="id_submitbutton" type="submit" name="submit" value="<?php print_string('save', 'quiz'); ?>"/>
                <input id="id_cancel" type="button" value="<?php print_string('cancel'); ?>" onclick="close_window"/>
            </fieldset>
        </div>
    </div>
</fieldset>
<?php
    echo '</form>';

/// End of the page.
    echo $OUTPUT->footer();
?>

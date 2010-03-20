<?php
/**
 * This page prints a summary of a quiz attempt before it is submitted.
 *
 * @author Tim Hunt others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$attemptid = required_param('attempt', PARAM_INT); // The attempt to summarise.

$PAGE->set_url('/mod/quiz/summary.php', array('attempt'=>$attemptid));

$attemptobj = quiz_attempt::create($attemptid);

/// Check login.
require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());

/// If this is not our own attempt, display an error.
if ($attemptobj->get_userid() != $USER->id) {
    print_error('notyourattempt', 'quiz', $attemptobj->view_url());
}

/// If the attempt is alreadyuj closed, redirect them to the review page.
if ($attemptobj->is_finished()) {
    redirect($attemptobj->review_url());
}

/// Check access.
$accessmanager = $attemptobj->get_access_manager(time());
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $attemptobj->view_url(),
            $accessmanager->print_messages($messages, true));
}
$accessmanager->do_password_check($attemptobj->is_preview_user());

/// Log this page view.
add_to_log($attemptobj->get_courseid(), 'quiz', 'view summary', 'summary.php?attempt=' . $attemptobj->get_attemptid(),
        $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// Load the questions and states.
$attemptobj->load_questions();
$attemptobj->load_question_states();

/// Print the page header
$PAGE->requires->js('/mod/quiz/quiz.js');
$title = get_string('summaryofattempt', 'quiz');
if ($accessmanager->securewindow_required($attemptobj->is_preview_user())) {
    $accessmanager->setup_secure_page($attemptobj->get_course()->shortname . ': ' .
            format_string($attemptobj->get_quiz_name()), '');
} elseif ($accessmanager->safebrowser_required($attemptobj->is_preview_user())) {
    $PAGE->set_title($attemptobj->get_course()->shortname . ': '.format_string($attemptobj->get_quiz_name()));
    $PAGE->set_cacheable(false);
    echo $OUTPUT->header();
} else {
    $attemptobj->navigation($title);
    $PAGE->set_title(format_string($attemptobj->get_quiz_name()));
    echo $OUTPUT->header();
}

/// Print tabs if they should be there.
if ($attemptobj->is_preview_user()) {
    $currenttab = 'preview';
    include('tabs.php');
}

/// Print heading.
echo $OUTPUT->heading(format_string($attemptobj->get_quiz_name()));
if ($attemptobj->is_preview_user()) {
    $attemptobj->print_restart_preview_button();
}
echo $OUTPUT->heading($title);

/// Prepare the summary table header
$table = new html_table();
$table->attributes['class'] = 'generaltable quizsummaryofattempt boxaligncenter';
$table->head = array(get_string('question', 'quiz'), get_string('status', 'quiz'));
$table->align = array('left', 'left');
$table->size = array('', '');
$scorescolumn = $attemptobj->get_review_options()->scores;
if ($scorescolumn) {
    $table->head[] = get_string('marks', 'quiz');
    $table->align[] = 'left';
    $table->size[] = '';
}
$table->data = array();

/// Get the summary info for each question.
$questionids = $attemptobj->get_question_ids();
foreach ($attemptobj->get_question_iterator() as $number => $question) {
    if ($question->length == 0) {
        continue;
    }
    $flag = '';
    if ($attemptobj->is_question_flagged($question->id)) {
        $flag = ' <img src="' . $OUTPUT->pix_url('i/flagged') . '" alt="' .
                get_string('flagged', 'question') . '" class="questionflag" />';
    }
    $row = array('<a href="' . s($attemptobj->attempt_url($question->id)) . '">' . $number . $flag . '</a>',
            get_string($attemptobj->get_question_status($question->id), 'quiz'));
    if ($scorescolumn) {
        $row[] = $attemptobj->get_question_score($question->id);
    }
    $table->data[] = $row;
}

/// Print the summary table.
echo html_writer::table($table);

/// countdown timer
echo $attemptobj->get_timer_html();

/// Finish attempt button.
echo $OUTPUT->container_start('submitbtns mdl-align');
$options = array(
    'attempt' => $attemptobj->get_attemptid(),
    'finishattempt' => 1,
    'timeup' => 0,
    'questionids' => '',
    'sesskey' => sesskey(),
);

$button = new single_button(new moodle_url($attemptobj->processattempt_url(), $options), get_string('finishattempt', 'quiz'));
$button->id = 'responseform';
$button->add_confirm_action(get_string('confirmclose', 'quiz'));

echo $OUTPUT->render($button);
echo $OUTPUT->container_end();

/// Finish the page
$accessmanager->show_attempt_timer_if_needed($attemptobj->get_attempt(), time());
echo $OUTPUT->footer();



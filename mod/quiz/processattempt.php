<?php
/**
 * This page deals with processing responses during an attempt at a quiz.
 *
 * People will normally arrive here from a form submission on attempt.php or
 * summary.php, and once the responses are processed, they will be redirected to
 * attempt.php or summary.php.
 *
 * This code used to be near the top of attempt.php, if you are looking for CVS history.
 *
 * @author Tim Hunt.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/// Remember the current time as the time any responses were submitted
/// (so as to make sure students don't get penalized for slow processing on this page)
$timenow = time();

/// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$nextpage = optional_param('nextpage', 0, PARAM_INT);
$submittedquestionids = required_param('questionids', PARAM_SEQUENCE);
$finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
$timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.

$attemptobj = quiz_attempt::create($attemptid);

/// Set $nexturl now. It will be updated if a particular question was sumbitted in
/// adaptive mode.
if ($nextpage == -1) {
    $nexturl = $attemptobj->summary_url();
} else {
    $nexturl = $attemptobj->attempt_url(0, $nextpage);
}

/// We treat automatically closed attempts just like normally closed attempts
if ($timeup) {
    $finishattempt = 1;
}

/// Check login.
require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());
require_sesskey();

/// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    quiz_error($attemptobj->get_quiz(), 'notyourattempt');
}

/// Check capabilities.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}

/// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    quiz_error($attemptobj->get_quiz(), 'attemptalreadyclosed');
}

/// Don't log - we will end with a redirect to a page that is logged.

/// Get the list of questions needed by this page.
if (!empty($submittedquestionids)) {
    $submittedquestionids = explode(',', $submittedquestionids);
} else {
    $submittedquestionids = array();
}
if ($finishattempt) {
    $questionids = $attemptobj->get_question_ids();
} else {
    $questionids = $submittedquestionids;
}

/// Load those questions we need, and just the submitted states for now.
$attemptobj->load_questions($questionids);
if (!empty($submittedquestionids)) {
    $attemptobj->load_question_states($submittedquestionids);
}

/// Process the responses /////////////////////////////////////////////////
if (!$responses = data_submitted()) {
    quiz_error($attemptobj->get_quiz(), 'nodatasubmitted');
}

/// Set the default event. This can be overruled by individual buttons.
if ($finishattempt) {
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
unset($responses->finishattempt); // same as $finishattempt
unset($responses->forcenewattempt);

/// Extract the responses. $actions will be an array indexed by the questions ids.
$actions = question_extract_responses($attemptobj->get_questions($submittedquestionids),
        $responses, $event);

/// Process each question in turn
$success = true;
$attempt = $attemptobj->get_attempt();
foreach($submittedquestionids as $id) {
    if (!isset($actions[$id])) {
        $actions[$id]->responses = array('' => '');
        $actions[$id]->event = QUESTION_EVENTOPEN;
    }
    $actions[$id]->timestamp = $timenow;

/// If a particular question was submitted, update the nexturl to go back to that question.
    if ($actions[$id]->event == QUESTION_EVENTSUBMIT) {
        $nexturl = $attemptobj->attempt_url($id);
    }

    $state = $attemptobj->get_question_state($id);
    if (question_process_responses($attemptobj->get_question($id),
            $state, $actions[$id], $attemptobj->get_quiz(), $attempt)) {
        save_question_session($attemptobj->get_question($id), $state);
    } else {
        $success = false;
    }
}

if (!$success) {
    print_error('errorprocessingresponses', 'question', $attemptobj->attempt_url(0, $page));
}

/// If we do not have to finish the attempts (if we are only processing responses)
/// save the attempt and redirect to the next page.
if (!$finishattempt) {
    $attempt->timemodified = $timenow;
    $DB->update_record('quiz_attempts', $attempt);

    redirect($nexturl);
}

/// We have been asked to finish attempt, so do that //////////////////////

/// Now load the state of every question, reloading the ones we messed around
/// with above.
$attemptobj->preload_question_states();
$attemptobj->load_question_states();

/// Move each question to the closed state.
$success = true;
$attempt = $attemptobj->get_attempt();
foreach ($attemptobj->get_questions() as $id => $question) {
    $state = $attemptobj->get_question_state($id);
    $action = new stdClass;
    $action->event = QUESTION_EVENTCLOSE;
    $action->responses = $state->responses;
    $action->responses['_flagged'] = $state->flagged;
    $action->timestamp = $state->timestamp;
    if (question_process_responses($attemptobj->get_question($id),
            $state, $action, $attemptobj->get_quiz(), $attempt)) {
        save_question_session($attemptobj->get_question($id), $state);
    } else {
        $success = false;
    }
}

if (!$success) {
    print_error('errorprocessingresponses', 'question', $attemptobj->attempt_url(0, $page));
}

/// Log the end of this attempt.
add_to_log($attemptobj->get_courseid(), 'quiz', 'close attempt',
        'review.php?attempt=' . $attemptobj->get_attemptid(),
        $attemptobj->get_quizid(), $attemptobj->get_cmid());

/// Update the quiz attempt record.
$attempt->timemodified = $timenow;
$attempt->timefinish = $timenow;
$DB->update_record('quiz_attempts', $attempt);

if (!$attempt->preview) {
/// Record this user's best grade (if this is not a preview).
    quiz_save_best_grade($attemptobj->get_quiz());

/// Send any notification emails (if this is not a preview).
    $attemptobj->quiz_send_notification_emails();
}

/// Clear the password check flag in the session.
$accessmanager = $attemptobj->get_access_manager($timenow);
$accessmanager->clear_password_access();

/// Trigger event
$eventdata = new stdClass();
$eventdata->component  = 'mod_quiz';
$eventdata->course     = $attemptobj->get_courseid();
$eventdata->quiz       = $attemptobj->get_quizid();
$eventdata->cm         = $attemptobj->get_cmid();
$eventdata->user       = $USER;
$eventdata->attempt    = $attemptobj->get_attemptid();
events_trigger('quiz_attempt_processed', $eventdata);

/// Send the user to the review page.
redirect($attemptobj->review_url());

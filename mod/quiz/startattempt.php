<?php
/**
 * This page deals with starting a new attempt at a quiz.
 *
 * Normally, it will end up redirecting to attempt.php - unless a password form is displayed.
 *
 * This code used to be at the top of attempt.php, if you are looking for CVS history.
 *
 * @author Tim Hunt.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/// Get submitted parameters.
$id = required_param('cmid', PARAM_INT); // Course Module ID
$forcenew = optional_param('forcenew', false, PARAM_BOOL); // Used to force a new preview

if (!$cm = get_coursemodule_from_id('quiz', $id)) {
    print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error("coursemisconf");
}
if (!$quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
    print_error('invalidcoursemodule');
}

$quizobj = quiz::create($quiz->id, $USER->id);
// This script should only ever be posted to, so set page URL to the view page.
$PAGE->set_url($quizobj->view_url());

/// Check login and sesskey.
require_login($quizobj->get_courseid(), false, $quizobj->get_cm());
if (!confirm_sesskey()) {
    throw new moodle_exception('confirmsesskeybad', 'error', $quizobj->view_url());
}
$PAGE->set_pagelayout('base');

/// if no questions have been set up yet redirect to edit.php
if (!$quizobj->get_question_ids() && $quizobj->has_capability('mod/quiz:manage')) {
    redirect($quizobj->edit_url());
}

/// Create an object to manage all the other (non-roles) access rules.
$accessmanager = $quizobj->get_access_manager(time());
if ($quizobj->is_preview_user() && $forcenew) {
    $accessmanager->clear_password_access();
}


/// Check capabilities.
if (!$quizobj->is_preview_user()) {
    $quizobj->require_capability('mod/quiz:attempt');
}

/// Check to see if a new preview was requested.
if ($quizobj->is_preview_user() && $forcenew) {
/// To force the creation of a new preview, we set a finish time on the
/// current attempt (if any). It will then automatically be deleted below
    $DB->set_field('quiz_attempts', 'timefinish', time(), array('quiz' => $quiz->id, 'userid' => $USER->id));
}

/// Look for an existing attempt.
$lastattempt = quiz_get_latest_attempt_by_user($quiz->id, $USER->id);

if ($lastattempt && !$lastattempt->timefinish) {
/// Continuation of an attempt - check password then redirect.
    $accessmanager->do_password_check($quizobj->is_preview_user());
    redirect($quizobj->attempt_url($lastattempt->id));
}

/// Get number for the next or unfinished attempt
if ($lastattempt && !$lastattempt->preview && !$quizobj->is_preview_user()) {
    $lastattemptid = $lastattempt->uniqueid;
    $attemptnumber = $lastattempt->attempt + 1;
} else {
    $lastattempt = false;
    $lastattemptid = false;
    $attemptnumber = 1;
}

/// Check access.
$messages = $accessmanager->prevent_access() +
        $accessmanager->prevent_new_attempt($attemptnumber - 1, $lastattempt);
if (!$quizobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $quizobj->view_url(),
            $accessmanager->print_messages($messages, true));
}
$accessmanager->do_password_check($quizobj->is_preview_user());

/// Delete any previous preview attempts belonging to this user.
quiz_delete_previews($quiz, $USER->id);

/// Create the new attempt and initialize the question sessions
$attempt = quiz_create_attempt($quiz, $attemptnumber, $lastattempt, time(), $quizobj->is_preview_user());

/// Save the attempt in the database.
$attempt->id = $DB->insert_record('quiz_attempts', $attempt);

/// Log the new attempt.
if ($attempt->preview) {
    add_to_log($course->id, 'quiz', 'preview', 'view.php?id=' . $quizobj->get_cmid(),
            $quizobj->get_quizid(), $quizobj->get_cmid());
} else {
    add_to_log($course->id, 'quiz', 'attempt', 'review.php?attempt=' . $attempt->id,
            $quizobj->get_quizid(), $quizobj->get_cmid());
}

/// Fully load all the questions in this quiz.
$quizobj->preload_questions();
$quizobj->load_questions();

/// Create initial states for all questions in this quiz.
if (!$quiz->attemptonlast) {
    $lastattemptid = false;
}
if (!$states = get_question_states($quizobj->get_questions(), $quizobj->get_quiz(), $attempt, $lastattemptid)) {
    print_error('cannotrestore', 'quiz');
}

/// Save all the newly created states.
foreach ($quizobj->get_questions() as $i => $question) {
    save_question_session($question, $states[$i]);
}
/// Trigger event
$eventdata = new stdClass();
$eventdata->component  = 'mod_quiz';
$eventdata->course     = $quizobj->get_courseid();
$eventdata->quiz       = $quizobj->get_quizid();
$eventdata->cm         = $quizobj->get_cmid();
$eventdata->user       = $USER;
$eventdata->attempt    = $attempt->id;
events_trigger('quiz_attempt_started', $eventdata);

/// Redirect to the attempt page.
redirect($quizobj->attempt_url($attempt->id));

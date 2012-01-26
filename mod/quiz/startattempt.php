<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script deals with starting a new attempt at a quiz.
 *
 * Normally, it will end up redirecting to attempt.php - unless a password form is displayed.
 *
 * This code used to be at the top of attempt.php, if you are looking for CVS history.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Get submitted parameters.
$id = required_param('cmid', PARAM_INT); // Course module id
$forcenew = optional_param('forcenew', false, PARAM_BOOL); // Used to force a new preview
$page = optional_param('page', 0, PARAM_INT); // Page to jump to in the attempt.

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

// Check login and sesskey.
require_login($quizobj->get_courseid(), false, $quizobj->get_cm());
require_sesskey();
$PAGE->set_pagelayout('base');

// if no questions have been set up yet redirect to edit.php or display an error.
if (!$quizobj->has_questions()) {
    if ($quizobj->has_capability('mod/quiz:manage')) {
        redirect($quizobj->edit_url());
    } else {
        print_error('cannotstartnoquestions', 'quiz', $quizobj->view_url());
    }
}

// Create an object to manage all the other (non-roles) access rules.
$accessmanager = $quizobj->get_access_manager(time());
if ($quizobj->is_preview_user() && $forcenew) {
    $accessmanager->clear_password_access();
}

// Check capabilities.
if (!$quizobj->is_preview_user()) {
    $quizobj->require_capability('mod/quiz:attempt');
}

// Check to see if a new preview was requested.
if ($quizobj->is_preview_user() && $forcenew) {
    // To force the creation of a new preview, we set a finish time on the
    // current attempt (if any). It will then automatically be deleted below
    $DB->set_field('quiz_attempts', 'timefinish', time(),
            array('quiz' => $quiz->id, 'userid' => $USER->id));
}

// Look for an existing attempt.
$attempts = quiz_get_user_attempts($quiz->id, $USER->id, 'all', true);
$lastattempt = end($attempts);

// If an in-progress attempt exists, check password then redirect to it.
if ($lastattempt && !$lastattempt->timefinish) {
    $accessmanager->do_password_check($quizobj->is_preview_user());
    redirect($quizobj->attempt_url($lastattempt->id, $page));
}

// Get number for the next or unfinished attempt
if ($lastattempt && !$lastattempt->preview && !$quizobj->is_preview_user()) {
    $attemptnumber = $lastattempt->attempt + 1;
} else {
    $lastattempt = false;
    $attemptnumber = 1;
}

// Check access.
$messages = $accessmanager->prevent_access() +
        $accessmanager->prevent_new_attempt(count($attempts), $lastattempt);
if (!$quizobj->is_preview_user() && $messages) {
    $output = $PAGE->get_renderer('mod_quiz');
    print_error('attempterror', 'quiz', $quizobj->view_url(),
            $output->access_messages($messages));
}
$accessmanager->do_password_check($quizobj->is_preview_user());

// Delete any previous preview attempts belonging to this user.
quiz_delete_previews($quiz, $USER->id);

$quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
$quba->set_preferred_behaviour($quiz->preferredbehaviour);

// Create the new attempt and initialize the question sessions
$attempt = quiz_create_attempt($quiz, $attemptnumber, $lastattempt, time(),
        $quizobj->is_preview_user());

if (!($quiz->attemptonlast && $lastattempt)) {
    // Starting a normal, new, quiz attempt.

    // Fully load all the questions in this quiz.
    $quizobj->preload_questions();
    $quizobj->load_questions();

    // Add them all to the $quba.
    $idstoslots = array();
    $questionsinuse = array_keys($quizobj->get_questions());
    foreach ($quizobj->get_questions() as $i => $questiondata) {
        if ($questiondata->qtype != 'random') {
            if (!$quiz->shuffleanswers) {
                $questiondata->options->shuffleanswers = false;
            }
            $question = question_bank::make_question($questiondata);

        } else {
            $question = question_bank::get_qtype('random')->choose_other_question(
                    $questiondata, $questionsinuse, $quiz->shuffleanswers);
            if (is_null($question)) {
                throw new moodle_exception('notenoughrandomquestions', 'quiz',
                        $quizobj->view_url(), $questiondata);
            }
        }

        $idstoslots[$i] = $quba->add_question($question, $questiondata->maxmark);
        $questionsinuse[] = $question->id;
    }

    // Start all the questions.
    if ($attempt->preview) {
        $variantoffset = rand(1, 100);
    } else {
        $variantoffset = $attemptnumber;
    }
    $quba->start_all_questions(
            new question_variant_pseudorandom_no_repeats_strategy($variantoffset),
            time());

    // Update attempt layout.
    $newlayout = array();
    foreach (explode(',', $attempt->layout) as $qid) {
        if ($qid != 0) {
            $newlayout[] = $idstoslots[$qid];
        } else {
            $newlayout[] = 0;
        }
    }
    $attempt->layout = implode(',', $newlayout);

} else {
    // Starting a subsequent attempt in each attempt builds on last mode.

    $oldquba = question_engine::load_questions_usage_by_activity($lastattempt->uniqueid);

    $oldnumberstonew = array();
    foreach ($oldquba->get_attempt_iterator() as $oldslot => $oldqa) {
        $newslot = $quba->add_question($oldqa->get_question(), $oldqa->get_max_mark());

        $quba->start_question_based_on($newslot, $oldqa);

        $oldnumberstonew[$oldslot] = $newslot;
    }

    // Update attempt layout.
    $newlayout = array();
    foreach (explode(',', $lastattempt->layout) as $oldslot) {
        if ($oldslot != 0) {
            $newlayout[] = $oldnumberstonew[$oldslot];
        } else {
            $newlayout[] = 0;
        }
    }
    $attempt->layout = implode(',', $newlayout);
}

// Save the attempt in the database.
$transaction = $DB->start_delegated_transaction();
question_engine::save_questions_usage_by_activity($quba);
$attempt->uniqueid = $quba->get_id();
$attempt->id = $DB->insert_record('quiz_attempts', $attempt);

// Log the new attempt.
if ($attempt->preview) {
    add_to_log($course->id, 'quiz', 'preview', 'view.php?id=' . $quizobj->get_cmid(),
            $quizobj->get_quizid(), $quizobj->get_cmid());
} else {
    add_to_log($course->id, 'quiz', 'attempt', 'review.php?attempt=' . $attempt->id,
            $quizobj->get_quizid(), $quizobj->get_cmid());
}

// Trigger event
$eventdata = new stdClass();
$eventdata->component = 'mod_quiz';
$eventdata->attemptid = $attempt->id;
$eventdata->timestart = $attempt->timestart;
$eventdata->userid    = $attempt->userid;
$eventdata->quizid    = $quizobj->get_quizid();
$eventdata->cmid      = $quizobj->get_cmid();
$eventdata->courseid  = $quizobj->get_courseid();
events_trigger('quiz_attempt_started', $eventdata);

$transaction->allow_commit();

// Redirect to the attempt page.
redirect($quizobj->attempt_url($attempt->id, $page));

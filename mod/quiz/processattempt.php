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
 * This page deals with processing responses during an attempt at a quiz.
 *
 * People will normally arrive here from a form submission on attempt.php or
 * summary.php, and once the responses are processed, they will be redirected to
 * attempt.php or summary.php.
 *
 * This code used to be near the top of attempt.php, if you are looking for CVS history.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Remember the current time as the time any responses were submitted
// (so as to make sure students don't get penalized for slow processing on this page)
$timenow = time();

// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$next = optional_param('next', false, PARAM_BOOL);
$thispage = optional_param('thispage', 0, PARAM_INT);
$nextpage = optional_param('nextpage', 0, PARAM_INT);
$finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
$timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.
$scrollpos = optional_param('scrollpos', '', PARAM_RAW);

$transaction = $DB->start_delegated_transaction();
$attemptobj = quiz_attempt::create($attemptid);

// Set $nexturl now.
if ($next) {
    $page = $nextpage;
} else {
    $page = $thispage;
}
if ($page == -1) {
    $nexturl = $attemptobj->summary_url();
} else {
    $nexturl = $attemptobj->attempt_url(null, $page);
    if ($scrollpos !== '') {
        $nexturl->param('scrollpos', $scrollpos);
    }
}

// We treat automatically closed attempts just like normally closed attempts
if ($timeup) {
    $finishattempt = 1;
}

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());
require_sesskey();

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
}

// Check capabilities.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(),
            'attemptalreadyclosed', null, $attemptobj->review_url());
}

// Don't log - we will end with a redirect to a page that is logged.

if (!$finishattempt) {
    // Just process the responses for this page and go to the next page.
    try {
        $attemptobj->process_all_actions($timenow);

    } catch (question_out_of_sequence_exception $e) {
        print_error('submissionoutofsequencefriendlymessage', 'question',
                $attemptobj->attempt_url(null, $thispage));

    } catch (Exception $e) {
        // This sucks, if we display our own custom error message, there is no way
        // to display the original stack trace.
        $debuginfo = '';
        if (!empty($e->debuginfo)) {
            $debuginfo = $e->debuginfo;
        }
        print_error('errorprocessingresponses', 'question',
                $attemptobj->attempt_url(null, $thispage), $e->getMessage(), $debuginfo);
    }

    $transaction->allow_commit();
    redirect($nexturl);
}

// Otherwise, we have been asked to finish attempt, so do that.

// Log the end of this attempt.
add_to_log($attemptobj->get_courseid(), 'quiz', 'close attempt',
        'review.php?attempt=' . $attemptobj->get_attemptid(),
        $attemptobj->get_quizid(), $attemptobj->get_cmid());

// Update the quiz attempt record.
try {
    $attemptobj->finish_attempt($timenow);

} catch (question_out_of_sequence_exception $e) {
    print_error('submissionoutofsequencefriendlymessage', 'question',
            $attemptobj->attempt_url(null, $thispage));

} catch (Exception $e) {
    // This sucks, if we display our own custom error message, there is no way
    // to display the original stack trace.
    $debuginfo = '';
    if (!empty($e->debuginfo)) {
        $debuginfo = $e->debuginfo;
    }
    print_error('errorprocessingresponses', 'question',
            $attemptobj->attempt_url(null, $thispage), $e->getMessage(), $debuginfo);
}

// Send the user to the review page.
$transaction->allow_commit();
redirect($attemptobj->review_url());

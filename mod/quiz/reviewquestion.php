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
 * This page prints a review of a particular question attempt.
 * This page is expected to only be used in a popup window.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once('locallib.php');

$attemptid = required_param('attempt', PARAM_INT); // attempt id
$slot = required_param('slot', PARAM_INT); // question number in usage
$seq = optional_param('step', null, PARAM_INT); // sequence number

$baseurl = new moodle_url('/mod/quiz/reviewquestion.php',
        array('attempt' => $attemptid, 'slot' => $slot));
$currenturl = new moodle_url($baseurl);
if ($seq !== 0) {
    $currenturl->param('step', $seq);
}
$PAGE->set_url($currenturl);

$attemptobj = quiz_attempt::create($attemptid);

// Check login.
require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());
$attemptobj->check_review_capability();

$accessmanager = $attemptobj->get_access_manager(time());
$options = $attemptobj->get_display_options(true);

$PAGE->set_pagelayout('popup');
$output = $PAGE->get_renderer('mod_quiz');

// Check permissions.
if ($attemptobj->is_own_attempt()) {
    if (!$attemptobj->is_finished()) {
        echo $output->review_question_not_allowed(get_string('cannotreviewopen', 'quiz'));
        die();
    } else if (!$options->attempt) {
        echo $output->review_question_not_allowed(
                $accessmanager->cannot_review_message($attemptobj->get_review_options()));
        die();
    }

} else if (!$attemptobj->is_review_allowed()) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'noreviewattempt');
}

// Prepare summary informat about this question attempt.
$summarydata = array();

// Quiz name.
$summarydata['quizname'] = array(
    'title'   => get_string('modulename', 'quiz'),
    'content' => format_string($attemptobj->get_quiz_name()),
);

// Question name.
$summarydata['questionname'] = array(
    'title'   => get_string('question', 'quiz'),
    'content' => $attemptobj->get_question_name($slot),
);

// Other attempts at the quiz.
if ($attemptobj->has_capability('mod/quiz:viewreports')) {
    $attemptlist = $attemptobj->links_to_other_attempts($baseurl);
    if ($attemptlist) {
        $summarydata['attemptlist'] = array(
            'title'   => get_string('attempts', 'quiz'),
            'content' => $attemptlist,
        );
    }
}

// Timestamp of this action.
$timestamp = $attemptobj->get_question_action_time($slot);
if ($timestamp) {
    $summarydata['timestamp'] = array(
        'title'   => get_string('completedon', 'quiz'),
        'content' => userdate($timestamp),
    );
}

echo $output->review_question_page($attemptobj, $slot, $seq,
        $attemptobj->get_display_options(true), $summarydata);

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
$PAGE->set_pagelayout('popup');

$attemptobj = quiz_attempt::create($attemptid);

// Check login.
require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());
$attemptobj->check_review_capability();

echo $OUTPUT->header();

// Check permissions.
if ($attemptobj->is_own_attempt()) {
    if (!$attemptobj->is_finished()) {
        echo $OUTPUT->notification(get_string('cannotreviewopen', 'quiz'));
        echo $OUTPUT->close_window_button();
        echo $OUTPUT->footer();
        die();
    } else if (!$options->responses) {
        $accessmanager = $attemptobj->get_access_manager(time());
        echo $OUTPUT->notification($accessmanager->cannot_review_message(
                $attemptobj->get_review_options()));
        echo $OUTPUT->close_window_button();
        echo $OUTPUT->footer();
        die();
    }

} else if (!$attemptobj->is_review_allowed()) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'noreviewattempt');
}

// Quiz name.
$rows[] = '<tr><th scope="row" class="cell">' . get_string('modulename', 'quiz') .
        '</th><td class="cell">' . format_string($attemptobj->get_quiz_name()) . '</td></tr>';

// Question name.
$rows[] = '<tr><th scope="row" class="cell">' . get_string('question', 'quiz') .
        '</th><td class="cell">' . format_string(
        $attemptobj->get_question_name($slot)) . '</td></tr>';

// Other attempts at the quiz.
if ($attemptobj->has_capability('mod/quiz:viewreports')) {
    $attemptlist = $attemptobj->links_to_other_attempts($baseurl);
    if ($attemptlist) {
        $rows[] = '<tr><th scope="row" class="cell">' . get_string('attempts', 'quiz') .
                '</th><td class="cell">' . $attemptlist . '</td></tr>';
    }
}

// Timestamp of this action.
$timestamp = $attemptobj->get_question_action_time($slot);
if ($timestamp) {
    $rows[] = '<tr><th scope="row" class="cell">' . get_string('completedon', 'quiz') .
            '</th><td class="cell">' . userdate($timestamp) . '</td></tr>';
}

// Now output the summary table, if there are any rows to be shown.
if (!empty($rows)) {
    echo '<table class="generaltable generalbox quizreviewsummary"><tbody>', "\n";
    echo implode("\n", $rows);
    echo "\n</tbody></table>\n";
}

// Print the question in the requested state.
if (!is_null($seq)) {
    echo $attemptobj->render_question_at_step($slot, $seq, true, $currenturl);
} else {
    echo $attemptobj->render_question($slot, true, $currenturl);
}

// Finish the page
echo $OUTPUT->footer();

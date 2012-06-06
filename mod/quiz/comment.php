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
 * This page allows the teacher to enter a manual grade for a particular question.
 * This page is expected to only be used in a popup window.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  gustav delius 2006
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('locallib.php');

$attemptid = required_param('attempt', PARAM_INT); // attempt id
$slot = required_param('slot', PARAM_INT); // question number in attempt

$PAGE->set_url('/mod/quiz/comment.php', array('attempt' => $attemptid, 'slot' => $slot));

$attemptobj = quiz_attempt::create($attemptid);

// Can only grade finished attempts.
if (!$attemptobj->is_finished()) {
    print_error('attemptclosed', 'quiz');
}

// Check login and permissions.
require_login($attemptobj->get_courseid(), false, $attemptobj->get_cm());
$attemptobj->require_capability('mod/quiz:grade');

// Log this action.
add_to_log($attemptobj->get_courseid(), 'quiz', 'manualgrade', 'comment.php?attempt=' .
        $attemptobj->get_attemptid() . '&slot=' . $slot,
        $attemptobj->get_quizid(), $attemptobj->get_cmid());

// Print the page header
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($attemptobj->get_question_name($slot)));

// Process any data that was submitted.
if (data_submitted() && confirm_sesskey()) {
    if (optional_param('submit', false, PARAM_BOOL) && question_behaviour::is_manual_grade_in_range($attemptobj->get_uniqueid(), $slot)) {
        $transaction = $DB->start_delegated_transaction();
        $attemptobj->process_all_actions(time());
        $transaction->allow_commit();
        echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
        close_window(2, true);
        die;
    }
}

// Print the comment form.
echo '<form method="post" class="mform" id="manualgradingform" action="' .
        $CFG->wwwroot . '/mod/quiz/comment.php">';
echo $attemptobj->render_question_for_commenting($slot);
?>
<div>
    <input type="hidden" name="attempt" value="<?php echo $attemptobj->get_attemptid(); ?>" />
    <input type="hidden" name="slot" value="<?php echo $slot; ?>" />
    <input type="hidden" name="slots" value="<?php echo $slot; ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey(); ?>" />
</div>
<fieldset class="hidden">
    <div>
        <div class="fitem">
            <div class="fitemtitle">
                <div class="fgrouplabel"><label> </label></div>
            </div>
            <fieldset class="felement fgroup">
                <input id="id_submitbutton" type="submit" name="submit" value="<?php
                        print_string('save', 'quiz'); ?>"/>
            </fieldset>
        </div>
    </div>
</fieldset>
<?php
echo '</form>';
$PAGE->requires->js_init_call('M.mod_quiz.init_comment_popup', null, false, quiz_get_js_module());

// End of the page.
echo $OUTPUT->footer();

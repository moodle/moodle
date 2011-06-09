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
 * Upgrade helper code for the quiz module.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2006 Eloy Lafuente (stronk7)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade states for an attempt to Moodle 1.5 model
 *
 * Any state that does not yet have its timestamp set to nonzero has not yet been
 * upgraded from Moodle 1.4. The reason these are still around is that for large
 * sites it would have taken too long to upgrade all states at once. This function
 * sets the timestamp field and creates an entry in the question_sessions table.
 * @param object $attempt  The attempt whose states need upgrading
 */
function quiz_upgrade_very_old_question_sessions($attempt) {
    global $DB;
    // The old quiz model only allowed a single response per quiz attempt so that there will be
    // only one state record per question for this attempt.

    // We set the timestamp of all states to the timemodified field of the attempt.
    $DB->execute("UPDATE {question_states} SET timestamp = ? WHERE attempt = ?",
            array($attempt->timemodified, $attempt->uniqueid));

    // For each state we create an entry in the question_sessions table, with both newest and
    // newgraded pointing to this state.
    // Actually we only do this for states whose question is actually listed in $attempt->layout.
    // We do not do it for states associated to wrapped questions like for example the questions
    // used by a RANDOM question
    $session = new stdClass();
    $session->attemptid = $attempt->uniqueid;
    $session->sumpenalty = 0;
    $session->manualcomment = '';
    $session->manualcommentformat = FORMAT_HTML;
    $session->flagged = 0;

    $questionlist = str_replace(',0', '', quiz_clean_layout($layout, true));
    if (!$questionlist) {
        return;
    }
    list($usql, $question_params) = $DB->get_in_or_equal(explode(',', $questionlist));
    $params = array_merge(array($attempt->uniqueid), $question_params);

    if ($states = $DB->get_records_select('question_states',
            "attempt = ? AND question $usql", $params)) {
        foreach ($states as $state) {
            $session->newgraded = $state->id;
            $session->newest = $state->id;
            $session->questionid = $state->question;
            $DB->insert_record('question_sessions', $session, false);
        }
    }
}

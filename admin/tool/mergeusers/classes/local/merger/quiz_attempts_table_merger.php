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

namespace tool_mergeusers\local\merger;

defined('MOODLE_INTERNAL') || die();

use coding_exception;
use dml_exception;
use mod_quiz\quiz_settings;
use moodle_database;

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * table_merger to process quiz_attempts table.
 *
 * Quiz attempts are a complex entity in that they also span multiple tables into the question engine
 * and so, if both users have attempted a quiz, quiz_attempts and quiz_grades tables have to be updated
 * correspondingly.
 *
 *
 * There are 3 possible ways for quiz attempts to occur:
 *
 * 1.  The old user only attempts the quiz
 *      - In this case the quiz attempt is transferred over through the $recordsToModify array
 *      - Normal merging on compound index will process it naturally,
 *        using $toid to set the userid in the quiz grades table.
 * 2. The new user only attempts the quiz
 *      - In this case it won't matter, no processing is needed
 * 3. Both users attempt the quiz. There are 4 different kind of actions to perform:
 *      - ACTION_REMAIN: Nothing is done: no deletion, no update; quiz attempts remain related to each user.
 *      - ACTION_RENUMBER: Moves attempts from old user to be the first attempts of the new user.
 *        Quiz operations are performed to normalize this new scenario.
 *      - ACTION_DELETE_FROM_SOURCE: Deletes quiz_attempts records from the old user attempts.
 *        This means that the attemps to have into account will be only the last ones (those made
 *        with the new user). Behavior suggested by John Hoopes (well, John proposed do nothing
 *        with those attempts, leaving them related to the old user; we ).
 *      - ACTION_DELETE_FROM_TARGET: Deletes quiz_attempts records from the new user attempts.
 *        This means that the old user's attempts are leaved, and removed those from the new user
 *        as if the new user was cheating. Behaviour suggested by Nicolas Dunand.
 *
 * @package   tool_mergeusers
 * @author    John Hoopes <hoopes@wisc.edu>
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2014 University of Wisconsin - Madison
 * @copyright 2014 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_attempts_table_merger extends generic_table_merger {
    /** @var string When cleaning up records, this action deletes records from old user. */
    const ACTION_DELETE_FROM_SOURCE = 'delete_fromid';
    /** @var string When cleaning up records, this action deletes records from new user. */
    const ACTION_DELETE_FROM_TARGET = 'delete_toid';
    /** @var string When cleaning up records, this action does not delete records,
     * but renumbers attempts. */
    const ACTION_RENUMBER = 'renumber';
    /** @var string Quiz attempts to remain related to each user, without merging nor deleting them. */
    const ACTION_REMAIN = 'remain';

    /** @var string current defined action. */
    protected $action;

    /**
     * Loads the current action from settings to perform when cleaning records.
     *
     * @throws dml_exception
     */
    public function __construct() {
        parent::__construct();
        $this->action = get_config('tool_mergeusers', 'quizattemptsaction');
    }

    /**
     * This table_merger processes quiz_attempts accordingly, regrading when
     * necessary. So that tables quiz_grades and quiz_grades_history
     * have to be omitted from processing by other table_mergers.
     *
     * @return array
     */
    public function get_tables_to_skip(): array {
        return ['quiz_grades', 'quiz_grades_history'];
    }

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $logs list of action performed.
     * @param array $errors list of error messages.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function merge($data, &$logs, &$errors): void {
        switch ($this->action) {
            case self::ACTION_REMAIN:
                $tables = $data['tableName'] . ', ' . implode(', ', $this->get_tables_to_skip());
                $logs[] = get_string('qa_action_remain_log', 'tool_mergeusers', $tables);
                break;
            case self::ACTION_DELETE_FROM_SOURCE:
                parent::merge($data, $logs, $errors);
                break;
            case self::ACTION_DELETE_FROM_TARGET:
                $newdata = $data;
                $newdata['fromid'] = $data['toid'];
                $newdata['toid'] = $data['fromid'];
                parent::merge($data, $logs, $errors);
                break;
            case self::ACTION_RENUMBER:
                $this->renumber($data, $logs, $errors);
                break;
        }
    }

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errorMessages and $actionLog,
     * by having the union of all attempts and being renumbered by
     * the timestart of each attempt.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $actionlogs list of action performed.
     * @param array $errormessages list of error messages.
     * @throws dml_exception
     * @throws coding_exception
     */
    protected function renumber($data, &$actionlogs, &$errormessages) {
        global $CFG, $DB;

        $tablename = $CFG->prefix . $data['tableName'];

        // We want to find all quiz attempts made from both users if any.
        $sql = "
            SELECT *
            FROM " . $tablename . "
            WHERE userid IN (?, ?)
            ORDER BY quiz ASC, timestart ASC
        ";

        $allattempts = $DB->get_records_sql($sql, [$data['fromid'], $data['toid']]);

        // When there are attempts, check what we have to do with them.
        if ($allattempts) {
            $toid = $data['toid'];
            $update = [
                'UPDATE ' . $tablename . ' SET ',
                ' WHERE id = ',
            ];

            // List of quiz ids necessary to recalculate.
            $quizzes = [];
            // List of attempts organized by quiz id.
            $attemptsbyquiz = [];
            // List of users that have attempts per quiz.
            $userids = [];

            // Organize all attempts by quiz and userid.
            foreach ($allattempts as $attempt) {
                $attemptsbyquiz[$attempt->quiz][] = $attempt;
                $userids[$attempt->quiz][$attempt->userid] = $attempt->userid;
            }

            // Processing attempts quiz by quiz.
            foreach ($attemptsbyquiz as $quiz => $attempts) {
                // Do nothing when there is only the target user.
                if (count($userids[$quiz]) === 1 && isset($userids[$quiz][$toid])) {
                    // All attempts are for the target user only; do nothing.
                    continue;
                }

                // Now we know that we have to gather all attempts and renumber them
                // by their timestart.
                //
                // In order to prevent key collisions for (userid, quiz and attempt),
                // we adopt the following procedure:
                //
                // 1. Renumber all attempts updating their attempt to $max + $nattempt.
                // 2. Update all above attempts to subtract $max to their attempt value.
                //
                // In step 1. we have $max set to the total number of attempts from both
                // users, and $nattempt is just an incremental value.
                //
                // In step 2. we renumber all attempts to start from 1 by just subtracting
                // the $max value to their attempt column.
                //
                //
                // Total number of attempts from both users.
                $max = count($attempts);
                // Update the list of quiz ids to be recalculated its grade.
                $quizzes[$quiz] = $quiz;
                // Number of attempt when renumbering.
                $nattempt = 1;

                // Renumber all attempts and updating userid when necessary.
                // All attempts have an offset of $max in their attempt column.
                foreach ($attempts as $attempt) {
                    $sets = [];
                    if ($attempt->userid != $toid) {
                        $sets[] = 'userid = ' . $toid;
                    }
                    $sets[] = 'attempt = ' . ($max + $nattempt);

                    $updatesql = $update[0] . implode(', ', $sets) . $update[1] . $attempt->id;
                    if ($DB->execute($updatesql)) {
                        $actionlogs[] = $updatesql;
                    } else {
                        $errormessages[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                            ': ' . $DB->get_last_error();
                    }

                    $nattempt++;
                    unset($sets); // Free mem.
                }

                // Remove the offset of $max from their attempt column to make them start by 1 as expected.
                $updateall = "UPDATE " . $tablename .
                    " SET attempt = attempt - $max " .
                    " WHERE quiz = $quiz AND userid = $toid";

                if ($DB->execute($updateall)) {
                    $actionlogs[] = $updateall;
                } else {
                    $errormessages[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                        ': ' . $DB->get_last_error();
                }
            }

            // Recalculate grades for updated quizzes.
            $this->update_all_quizzes($data, $quizzes, $actionlogs);
        }
    }

    /**
     * Overriding the default implementation to add a final task: updateQuizzes.
     *
     * @param array $data array with details of merging.
     * @param array $recordstomodify list of record ids to update with $toid.
     * @param string $fieldname field name of the table to update.
     * @param array $logs list of performed actions.
     * @param array $errors list of error messages.
     * @return void
     */
    protected function update_all_records(
        array $data,
        array $recordstomodify,
        string $fieldname,
        array &$logs,
        array &$errors,
    ): void {
        parent::update_all_records($data, $recordstomodify, $fieldname, $logs, $errors);
        $this->update_all_quizzes($data, $recordstomodify, $logs);
    }

    /**
     * Recalculate grades for any affected quiz.
     *
     * @param array $data array with attributes, like 'tableName'
     * @param array $ids ids of the table to be updated, and so, to update quiz grades.
     * @param array $logs list of logs of performed actions.
     * @return void
     */
    protected function update_all_quizzes(array $data, array $ids, array &$logs): void {
        if (empty($ids)) {
            // If no ids... do nothing.
            return;
        }

        $chunks = array_chunk($ids, static::CHUNK_SIZE);
        foreach ($chunks as $chunk) {
            $this->update_quizzes($chunk, $logs);
        }
    }

    /**
     * Updates the quizzes involved on the merge.
     *
     * It is supposed to be invoked only by self::update_all_quizzes().
     *
     * @param array $ids list of record ids to update.
     * @param array $logs actions logs list.
     * @return void
     * @see self::update_all_quizzes()
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function update_quizzes(array $ids, array &$logs): void {
        global $DB;

        $idsstr = "'" . implode("', '", $ids) . "'";

        $sqlquizzes = "
            SELECT * FROM {quiz} q
                    WHERE id IN ($idsstr)
        ";

        $quizzes = $DB->get_records_sql($sqlquizzes);

        if ($quizzes) {
            $logs[] = get_string('qa_grades', 'tool_mergeusers', implode(', ', array_keys($quizzes)));
            foreach ($quizzes as $quiz) {
                // See https://moodle.org/mod/forum/discuss.php?d=258979.
                // Recalculate grades for affected quizzes.
                $quizobj = quiz_settings::create($quiz->id);
                $quizobj->get_grade_calculator()->recompute_all_final_grades();
            }
        }
    }
}

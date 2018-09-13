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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once $CFG->dirroot . '/mod/quiz/lib.php';
require_once $CFG->dirroot . '/mod/quiz/locallib.php';

/**
 * TableMerger to process quiz_attempts table.
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
 * @package     tool
 * @subpackage  mergeusers
 * @author      John Hoopes <hoopes@wisc.edu>, 2014 University of Wisconsin - Madison
 * @author      Jordi Pujol-Ahulló <jordi.pujol@urv.cat>,  SREd, Universitat Rovira i Virgili
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class QuizAttemptsMerger extends GenericTableMerger
{

    /** @var string When cleaning up records, this action deletes records from old user. */
    const ACTION_DELETE_FROM_SOURCE = 'delete_fromid';

    /** @var string When cleaning up records, this action deletes records from new user. */
    const ACTION_DELETE_FROM_TARGET = 'delete_toid';

    /** @var string When cleaning up records, this action does not delete records,
     * but renumbers attempts. */
    const ACTION_RENUMBER = 'renumber';

    /** @var string Quiz attempts remain related to each user, without merging nor deleting them. */
    const ACTION_REMAIN = 'remain';

    /**
     * @var string current defined action.
     */
    protected $action;

    /**
     * Loads the current action from settings to perform when cleaning records.
     */
    public function __construct()
    {
        $this->action = get_config('tool_mergeusers', 'quizattemptsaction');
    }

    /**
     * This TableMerger processes quiz_attempts accordingly, regrading when 
     * necessary. So that tables quiz_grades and quiz_grades_history 
     * have to be omitted from processing by other TableMergers.
     *
     * @return array
     */
    public function getTablesToSkip()
    {
        return array('quiz_grades', 'quiz_grades_history');
    }

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $actionLog list of action performed.
     * @param array $errorMessages list of error messages.
     */
    public function merge($data, &$actionLog, &$errorMessages)
    {
        switch ($this->action) {
            case self::ACTION_REMAIN:
                $tables = $data['tableName'] . ', ' . implode(', ', $this->getTablesToSkip());
                $actionLog[] = get_string('qa_action_remain_log', 'tool_mergeusers', $tables);
                break;
            case self::ACTION_DELETE_FROM_SOURCE:
                parent::merge($data, $actionLog, $errorMessages);
                break;
            case self::ACTION_DELETE_FROM_TARGET:
                $newdata = $data;
                $newdata['fromid'] = $data['toid'];
                $newdata['toid'] = $data['fromid'];
                parent::merge($data, $actionLog, $errorMessages);
                break;
            case self::ACTION_RENUMBER:
                $this->renumber($data, $actionLog, $errorMessages);
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
     * @param array $actionLog list of action performed.
     * @param array $errorMessages list of error messages.
     */
    protected function renumber($data, &$actionLog, &$errorMessages)
    {
        global $CFG, $DB;

        // we want to find all quiz attempts made from both users if any.
        $sql = "
            SELECT *
            FROM
                {" . $data['tableName'] . "} 
            WHERE
                userid IN (?, ?)
            ORDER BY quiz ASC, timestart ASC
        ";

        $allAttempts = $DB->get_records_sql($sql, array($data['fromid'], $data['toid']));

        // when there are attempts, check what we have to do with them.
        if ($allAttempts) {

            $toid = $data['toid'];
            $update = array(
                'UPDATE ' . $CFG->prefix . $data['tableName'] . ' SET ',
                ' WHERE id = ',
            );

            // list of quiz ids necessary to recalculate.
            $quizzes = array();
            // list of attempts organized by quiz id
            $attemptsByQuiz = array();
            // list of users that have attempts per quiz
            $userids = array();

            // organize all attempts by quiz and userid
            foreach ($allAttempts as $attempt) {
                $attemptsByQuiz[$attempt->quiz][] = $attempt;
                $userids[$attempt->quiz][$attempt->userid] = $attempt->userid;
            }

            // processing attempts quiz by quiz
            foreach ($attemptsByQuiz as $quiz => $attempts) {

                // do nothing when there is only the target user.
                if (count($userids[$quiz]) === 1 && isset($userids[$quiz][$toid])) {
                    // all attempts are for the target user only; do nothing.
                    continue;
                }

                // Now we know that we have to gather all attempts and renumber them
                // by their timestart.
                // 
                // In order to prevent key collisions for (userid, quiz and attempt), 
                // we adopt the following procedure:
                // 
                //   1. Renumber all attempts updating their attempt to $max + $nattempt.
                //   2. Update all above attempts to subtract $max to their attempt value.
                //   
                // In step 1. we have $max set to the total number of attempts from both
                // users, and $nattempt is just an incremental value.
                // 
                // In step 2. we renumber all attempts to start from 1 by just subtracting
                // the $max value to their attempt column.
                // 
                //
                // total number of attempts from both users.
                $max = count($attempts);
                // update the list of quiz ids to be recalculated its grade.
                $quizzes[$quiz] = $quiz;
                // number of attempt when renumbering
                $nattempt = 1;

                // Renumber all attempts and updating userid when necessary.
                // All attempts have an offset of $max in their attempt column.
                foreach ($attempts as $attempt) {

                    $sets = array();
                    if ($attempt->userid != $toid) {
                        $sets[] = 'userid = ' . $toid;
                    }
                    $sets[] = 'attempt = ' . ($max + $nattempt);

                    $updateSql = $update[0] . implode(', ', $sets) . $update[1] . $attempt->id;
                    if (!$DB->execute($updateSql)) {
                        $errorMessages[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                                ': ' . $DB->get_last_error();
                    }

                    $actionLog[] = $updateSql;
                    $nattempt++;
                    unset($sets); // free mem
                }

                // Remove the offset of $max from their attempt column to make
                // them start by 1 as expected.
                $updateAll = "UPDATE {$CFG->prefix}{$data['tableName']} " .
                        "SET attempt = attempt - $max " .
                        "WHERE quiz = $quiz AND userid = $toid";

                if (!$DB->execute($updateAll)) {
                    $errorMessages[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                            ': ' . $DB->get_last_error();
                }

                $actionLog[] = $updateAll;
            }

            // recalculate grades for updated quizzes.
            $this->updateQuizzes($data, $quizzes, $actionLog);
        }
    }

    /**
     * Overriding the default implementation to add a final task: updateQuizzes.
     * 
     * @param array $data array with details of merging.
     * @param array $recordsToModify list of record ids to update with $toid.
     * @param string $fieldName field name of the table to update.
     * @param array $actionLog list of performed actions.
     * @param array $errorMessages list of error messages.
     */
    protected function updateRecords($data, $recordsToModify, $fieldName, &$actionLog, &$errorMessages)
    {
        parent::updateRecords($data, $recordsToModify, $fieldName, $actionLog, $errorMessages);
        $this->updateQuizzes($data, $recordsToModify, $actionLog);
    }

    /**
     * Recalculate grades for any affected quiz.
     * @global moodle_database $DB
     * @param array $data array with attributes, like 'tableName'
     * @param array $ids ids of the table to be updated, and so, to update quiz grades.
     */
    protected function updateQuizzes($data, $ids, &$actionLog)
    {
        if (empty($ids)) {
            // if no ids... do nothing.
            return;
        }
        global $DB;

        $idsstr = "'" . implode("', '", $ids) . "'";

        $sqlQuizzes = "
            SELECT * FROM {quiz} q
                    WHERE id IN ($idsstr)
        ";

        $quizzes = $DB->get_records_sql($sqlQuizzes);

        if ($quizzes) {
            $actionLog[] = get_string('qa_grades', 'tool_mergeusers', implode(', ', array_keys($quizzes)));
            foreach ($quizzes as $quiz) {
                // https://moodle.org/mod/forum/discuss.php?d=258979
                // recalculate grades for affected quizzes.
                quiz_update_all_final_grades($quiz);
            }
        }
    }

}

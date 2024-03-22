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

namespace mod_quiz;

use coding_exception;
use mod_quiz\event\quiz_grade_updated;
use mod_quiz\hook\structure_modified;
use question_engine_data_mapper;
use stdClass;

/**
 * This class contains all the logic for computing the grade of a quiz.
 *
 * There are two sorts of calculation which need to be done. For a single
 * attempt, we need to compute the total attempt score from score for each question.
 * And for a quiz user, we need to compute the final grade from all the separate attempt grades.
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_calculator {

    /** @var float a number that is effectively zero. Used to avoid division-by-zero or underflow problems. */
    const ALMOST_ZERO = 0.000005;

    /** @var quiz_settings the quiz for which this instance computes grades. */
    protected $quizobj;

    /**
     * Constructor. Recommended way to get an instance is $quizobj->get_grade_calculator();
     *
     * @param quiz_settings $quizobj
     */
    protected function __construct(quiz_settings $quizobj) {
        $this->quizobj = $quizobj;
    }

    /**
     * Factory. The recommended way to get an instance is $quizobj->get_grade_calculator();
     *
     * @param quiz_settings $quizobj settings of a quiz.
     * @return grade_calculator instance of this class for the given quiz.
     */
    public static function create(quiz_settings $quizobj): grade_calculator {
        return new self($quizobj);
    }

    /**
     * Update the sumgrades field of the quiz.
     *
     * This needs to be called whenever the grading structure of the quiz is changed.
     * For example if a question is added or removed, or a question weight is changed.
     *
     * You should call {@see quiz_delete_previews()} before you call this function.
     */
    public function recompute_quiz_sumgrades(): void {
        global $DB;
        $quiz = $this->quizobj->get_quiz();

        // Update sumgrades in the database.
        $DB->execute("
                UPDATE {quiz}
                   SET sumgrades = COALESCE((
                        SELECT SUM(maxmark)
                          FROM {quiz_slots}
                         WHERE quizid = {quiz}.id
                       ), 0)
                 WHERE id = ?
             ", [$quiz->id]);

        // Update the value in memory.
        $quiz->sumgrades = $DB->get_field('quiz', 'sumgrades', ['id' => $quiz->id]);

        if ($quiz->sumgrades < self::ALMOST_ZERO && quiz_has_attempts($quiz->id)) {
            // If the quiz has been attempted, and the sumgrades has been
            // set to 0, then we must also set the maximum possible grade to 0, or
            // we will get a divide by zero error.
            self::update_quiz_maximum_grade(0);
        }

        // This class callback is deprecated, and will be removed in Moodle 4.8 (MDL-80327).
        // Use the structure_modified hook instead.
        $callbackclasses = \core_component::get_plugin_list_with_class('quiz', 'quiz_structure_modified');
        foreach ($callbackclasses as $callbackclass) {
            component_class_callback($callbackclass, 'callback', [$quiz->id], null, true);
        }

        \core\hook\manager::get_instance()->dispatch(new structure_modified($this->quizobj->get_structure()));
    }

    /**
     * Update the sumgrades field of attempts at this quiz.
     */
    public function recompute_all_attempt_sumgrades(): void {
        global $DB;
        $dm = new question_engine_data_mapper();
        $timenow = time();

        $DB->execute("
                UPDATE {quiz_attempts}
                   SET timemodified = :timenow,
                       sumgrades = (
                           {$dm->sum_usage_marks_subquery('uniqueid')}
                       )
                 WHERE quiz = :quizid AND state = :finishedstate
            ", [
                'timenow' => $timenow,
                'quizid' => $this->quizobj->get_quizid(),
                'finishedstate' => quiz_attempt::FINISHED
            ]);
    }

    /**
     * Update the final grade at this quiz for a particular student.
     *
     * That is, given the quiz settings, and all the attempts this user has made,
     * compute their final grade for the quiz, as shown in the gradebook.
     *
     * The $attempts parameter is for efficiency. If you already have the data for
     * all this user's attempts loaded (for example from {@see quiz_get_user_attempts()}
     * or because you are looping through a large recordset fetched in one efficient query,
     * then you can pass that data here to save DB queries.
     *
     * @param int|null $userid The userid to calculate the grade for. Defaults to the current user.
     * @param array $attempts if you already have this user's attempt records loaded, pass them here to save queries.
     */
    public function recompute_final_grade(?int $userid = null, array $attempts = []): void {
        global $DB, $USER;
        $quiz = $this->quizobj->get_quiz();

        if (empty($userid)) {
            $userid = $USER->id;
        }

        if (!$attempts) {
            // Get all the attempts made by the user.
            $attempts = quiz_get_user_attempts($quiz->id, $userid);
        }

        // Calculate the best grade.
        $bestgrade = $this->compute_final_grade_from_attempts($attempts);
        $bestgrade = quiz_rescale_grade($bestgrade, $quiz, false);

        // Save the best grade in the database.
        if (is_null($bestgrade)) {
            $DB->delete_records('quiz_grades', ['quiz' => $quiz->id, 'userid' => $userid]);

        } else if ($grade = $DB->get_record('quiz_grades',
                ['quiz' => $quiz->id, 'userid' => $userid])) {
            $grade->grade = $bestgrade;
            $grade->timemodified = time();
            $DB->update_record('quiz_grades', $grade);

        } else {
            $grade = new stdClass();
            $grade->quiz = $quiz->id;
            $grade->userid = $userid;
            $grade->grade = $bestgrade;
            $grade->timemodified = time();
            $DB->insert_record('quiz_grades', $grade);
        }

        quiz_update_grades($quiz, $userid);
    }

    /**
     * Calculate the overall grade for a quiz given a number of attempts by a particular user.
     *
     * @param array $attempts an array of all the user's attempts at this quiz in order.
     * @return float|null the overall grade, or null if the user does not have a grade.
     */
    protected function compute_final_grade_from_attempts(array $attempts): ?float {

        $grademethod = $this->quizobj->get_quiz()->grademethod;
        switch ($grademethod) {

            case QUIZ_ATTEMPTFIRST:
                $firstattempt = reset($attempts);
                return $firstattempt->sumgrades;

            case QUIZ_ATTEMPTLAST:
                $lastattempt = end($attempts);
                return $lastattempt->sumgrades;

            case QUIZ_GRADEAVERAGE:
                $sum = 0;
                $count = 0;
                foreach ($attempts as $attempt) {
                    if (!is_null($attempt->sumgrades)) {
                        $sum += $attempt->sumgrades;
                        $count++;
                    }
                }
                if ($count == 0) {
                    return null;
                }
                return $sum / $count;

            case QUIZ_GRADEHIGHEST:
                $max = null;
                foreach ($attempts as $attempt) {
                    if ($attempt->sumgrades > $max) {
                        $max = $attempt->sumgrades;
                    }
                }
                return $max;

            default:
                throw new coding_exception('Unrecognised grading method ' . $grademethod);
        }
    }

    /**
     * Update the final grade at this quiz for all students.
     *
     * This function is equivalent to calling {@see recompute_final_grade()} for all
     * users who have attempted the quiz, but is much more efficient.
     */
    public function recompute_all_final_grades(): void {
        global $DB;
        $quiz = $this->quizobj->get_quiz();

        // If the quiz does not contain any graded questions, then there is nothing to do.
        if (!$quiz->sumgrades) {
            return;
        }

        $param = ['iquizid' => $quiz->id, 'istatefinished' => quiz_attempt::FINISHED];
        $firstlastattemptjoin = "JOIN (
                SELECT
                    iquiza.userid,
                    MIN(attempt) AS firstattempt,
                    MAX(attempt) AS lastattempt

                FROM {quiz_attempts} iquiza

                WHERE
                    iquiza.state = :istatefinished AND
                    iquiza.preview = 0 AND
                    iquiza.quiz = :iquizid

                GROUP BY iquiza.userid
            ) first_last_attempts ON first_last_attempts.userid = quiza.userid";

        switch ($quiz->grademethod) {
            case QUIZ_ATTEMPTFIRST:
                // Because of the where clause, there will only be one row, but we
                // must still use an aggregate function.
                $select = 'MAX(quiza.sumgrades)';
                $join = $firstlastattemptjoin;
                $where = 'quiza.attempt = first_last_attempts.firstattempt AND';
                break;

            case QUIZ_ATTEMPTLAST:
                // Because of the where clause, there will only be one row, but we
                // must still use an aggregate function.
                $select = 'MAX(quiza.sumgrades)';
                $join = $firstlastattemptjoin;
                $where = 'quiza.attempt = first_last_attempts.lastattempt AND';
                break;

            case QUIZ_GRADEAVERAGE:
                $select = 'AVG(quiza.sumgrades)';
                $join = '';
                $where = '';
                break;

            default:
            case QUIZ_GRADEHIGHEST:
                $select = 'MAX(quiza.sumgrades)';
                $join = '';
                $where = '';
                break;
        }

        if ($quiz->sumgrades >= self::ALMOST_ZERO) {
            $finalgrade = $select . ' * ' . ($quiz->grade / $quiz->sumgrades);
        } else {
            $finalgrade = '0';
        }
        $param['quizid'] = $quiz->id;
        $param['quizid2'] = $quiz->id;
        $param['quizid3'] = $quiz->id;
        $param['quizid4'] = $quiz->id;
        $param['statefinished'] = quiz_attempt::FINISHED;
        $param['statefinished2'] = quiz_attempt::FINISHED;
        $param['almostzero'] = self::ALMOST_ZERO;
        $finalgradesubquery = "
                SELECT quiza.userid, $finalgrade AS newgrade
                FROM {quiz_attempts} quiza
                $join
                WHERE
                    $where
                    quiza.state = :statefinished AND
                    quiza.preview = 0 AND
                    quiza.quiz = :quizid3
                GROUP BY quiza.userid";

        $changedgrades = $DB->get_records_sql("
                SELECT users.userid, qg.id, qg.grade, newgrades.newgrade

                FROM (
                    SELECT userid
                    FROM {quiz_grades} qg
                    WHERE quiz = :quizid
                UNION
                    SELECT DISTINCT userid
                    FROM {quiz_attempts} quiza2
                    WHERE
                        quiza2.state = :statefinished2 AND
                        quiza2.preview = 0 AND
                        quiza2.quiz = :quizid2
                ) users

                LEFT JOIN {quiz_grades} qg ON qg.userid = users.userid AND qg.quiz = :quizid4

                LEFT JOIN (
                    $finalgradesubquery
                ) newgrades ON newgrades.userid = users.userid

                WHERE
                    ABS(newgrades.newgrade - qg.grade) > :almostzero OR
                    ((newgrades.newgrade IS NULL OR qg.grade IS NULL) AND NOT
                              (newgrades.newgrade IS NULL AND qg.grade IS NULL))",
                    // The mess on the previous line is detecting where the value is
                    // NULL in one column, and NOT NULL in the other, but SQL does
                    // not have an XOR operator, and MS SQL server can't cope with
                    // (newgrades.newgrade IS NULL) <> (qg.grade IS NULL).
                $param);

        $timenow = time();
        $todelete = [];
        foreach ($changedgrades as $changedgrade) {

            if (is_null($changedgrade->newgrade)) {
                $todelete[] = $changedgrade->userid;

            } else if (is_null($changedgrade->grade)) {
                $toinsert = new stdClass();
                $toinsert->quiz = $quiz->id;
                $toinsert->userid = $changedgrade->userid;
                $toinsert->timemodified = $timenow;
                $toinsert->grade = $changedgrade->newgrade;
                $DB->insert_record('quiz_grades', $toinsert);

            } else {
                $toupdate = new stdClass();
                $toupdate->id = $changedgrade->id;
                $toupdate->grade = $changedgrade->newgrade;
                $toupdate->timemodified = $timenow;
                $DB->update_record('quiz_grades', $toupdate);
            }
        }

        if (!empty($todelete)) {
            list($test, $params) = $DB->get_in_or_equal($todelete);
            $DB->delete_records_select('quiz_grades', 'quiz = ? AND userid ' . $test,
                    array_merge([$quiz->id], $params));
        }
    }

    /**
     * Update the quiz setting for the grade the quiz is out of.
     *
     * This function will update the data in quiz_grades and quiz_feedback, and
     * pass the new grades on to the gradebook.
     *
     * @param float $newgrade the new maximum grade for the quiz.
     */
    public function update_quiz_maximum_grade(float $newgrade): void {
        global $DB;
        $quiz = $this->quizobj->get_quiz();

        // This is potentially expensive, so only do it if necessary.
        if (abs($quiz->grade - $newgrade) < self::ALMOST_ZERO) {
            // Nothing to do.
            return;
        }

        // Use a transaction.
        $transaction = $DB->start_delegated_transaction();

        // Update the quiz table.
        $oldgrade = $quiz->grade;
        $quiz->grade = $newgrade;
        $timemodified = time();
        $DB->update_record('quiz', (object) [
            'id' => $quiz->id,
            'grade' => $newgrade,
            'timemodified' => $timemodified,
        ]);

        // Rescale the grade of all quiz attempts.
        if ($oldgrade < $newgrade) {
            // The new total is bigger, so we need to recompute fully to avoid underflow problems.
            $this->recompute_all_final_grades();

        } else {
            // New total smaller, so we can rescale the grades efficiently.
            $DB->execute("
                    UPDATE {quiz_grades}
                       SET grade = ? * grade, timemodified = ?
                     WHERE quiz = ?
            ", [$newgrade / $oldgrade, $timemodified, $quiz->id]);
        }

        // Rescale the overall feedback boundaries.
        if ($oldgrade > self::ALMOST_ZERO) {
            // Update the quiz_feedback table.
            $factor = $newgrade / $oldgrade;
            $DB->execute("
                    UPDATE {quiz_feedback}
                    SET mingrade = ? * mingrade, maxgrade = ? * maxgrade
                    WHERE quizid = ?
            ", [$factor, $factor, $quiz->id]);
        }

        // Update grade item and send all grades to gradebook.
        quiz_grade_item_update($quiz);
        quiz_update_grades($quiz);

        // Log quiz grade updated event.
        quiz_grade_updated::create([
            'context' => $this->quizobj->get_context(),
            'objectid' => $quiz->id,
            'other' => [
                'oldgrade' => $oldgrade + 0, // Remove trailing 0s.
                'newgrade' => $newgrade,
            ]
        ])->trigger();

        $transaction->allow_commit();
    }
}

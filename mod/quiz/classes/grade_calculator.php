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

use question_engine_data_mapper;

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
    public function recompute_quiz_sumgrades() {
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
            quiz_set_grade(0, $quiz);
        }
    }

    /**
     * Update the sumgrades field of attempts at this quiz.
     */
    public function recompute_all_attempt_sumgrades() {
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
     * Update the final grade at this quiz for all students.
     *
     * This function is equivalent to calling quiz_save_best_grade for all
     * users, but much more efficient.
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
}

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
     * Factory. Recommended way to get an instance is $quizobj->get_grade_calculator();
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

        if ($quiz->sumgrades < 0.000005 && quiz_has_attempts($quiz->id)) {
            // If the quiz has been attempted, and the sumgrades has been
            // set to 0, then we must also set the maximum possible grade to 0, or
            // we will get a divide by zero error.
            quiz_set_grade(0, $quiz);
        }
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
}

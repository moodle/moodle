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

namespace quiz_statistics\task;

use quiz_attempt;
use quiz;
use quiz_statistics_report;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');

/**
 * Re-calculate question statistics.
 *
 * @package    quiz_statistics
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recalculate extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('recalculatetask', 'quiz_statistics');
    }

    public function execute() {
        global $DB;
        // TODO: MDL-75197, add quizid in quiz_statistics so that it is simpler to find quizzes for stats calculation.
        // Only calculate stats for quizzes which have recently finished attempt.
        $sql = "
            SELECT qa.quiz, MAX(qa.timefinish) as timefinish
              FROM {quiz_attempts} qa
             WHERE qa.preview = 0
               AND qa.state = :quizstatefinished
          GROUP BY qa.quiz
        ";

        $params = [
            "quizstatefinished" => quiz_attempt::FINISHED,
        ];

        $latestattempts = $DB->get_records_sql($sql, $params);

        foreach ($latestattempts as $attempt) {
            $quizobj = quiz::create($attempt->quiz);
            $quiz = $quizobj->get_quiz();
            // Hash code for question stats option in question bank.
            $qubaids = quiz_statistics_qubaids_condition($quiz->id, new \core\dml\sql_join(), $quiz->grademethod);

            // Check if there is any existing question stats, and it has been calculated after latest quiz attempt.
            $records = $DB->get_records_select(
                'quiz_statistics',
                'hashcode = :hashcode AND timemodified > :timefinish',
                [
                    'hashcode' => $qubaids->get_hash_code(),
                    'timefinish' => $attempt->timefinish
                ]
            );

            if (empty($records)) {
                $report = new quiz_statistics_report();
                // Clear old cache.
                $report->clear_cached_data($qubaids);
                // Calculate new stats.
                $report->calculate_questions_stats_for_question_bank($quiz->id);
            }
        }
        return true;
    }
}

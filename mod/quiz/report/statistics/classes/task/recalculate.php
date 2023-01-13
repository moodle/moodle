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
    /** @var int the maximum length of time one instance of this task will run. */
    const TIME_LIMIT = 3600;

    public function get_name(): string {
        return get_string('recalculatetask', 'quiz_statistics');
    }

    public function execute(): void {
        global $DB;
        $stoptime = time() + self::TIME_LIMIT;
        $dateformat = get_string('strftimedatetimeshortaccurate', 'core_langconfig');

        // TODO: MDL-75197, add quizid in quiz_statistics so that it is simpler to find quizzes for stats calculation.
        // Only calculate stats for quizzes which have recently finished attempt.
        $sql = "
            SELECT q.id AS quizid,
                   q.name AS quizname,
                   c.id AS courseid,
                   c.shortname AS courseshortname,
                   MAX(qa.timefinish) AS mostrecentattempttime,
                   COUNT(1) AS numberofattempts
              FROM {quiz_attempts} qa
              JOIN {quiz} q ON q.id = qa.quiz
              JOIN {course} c ON c.id = q.course
             WHERE qa.preview = 0
               AND qa.state = :quizstatefinished
          GROUP BY q.id, q.name, c.id, c.shortname
        ";

        $params = [
            "quizstatefinished" => quiz_attempt::FINISHED,
        ];

        $latestattempts = $DB->get_records_sql($sql, $params);

        $anyexception = null;
        foreach ($latestattempts as $attempt) {
            if (time() >= $stoptime) {
                mtrace("This task has been running for more than " .
                        format_time(self::TIME_LIMIT) . " so stopping this execution.");
                break;
            }

            mtrace("  Examining quiz '$attempt->quizname' ($attempt->quizid) in course " .
                    "$attempt->courseshortname ($attempt->courseid) with most recent attempt at " .
                    userdate($attempt->mostrecentattempttime, $dateformat) . ".");
            $quizobj = quiz::create($attempt->quizid);
            $quiz = $quizobj->get_quiz();
            // Hash code for question stats option in question bank.
            $qubaids = quiz_statistics_qubaids_condition($quiz->id, new \core\dml\sql_join(), $quiz->grademethod);

            // Check if there is any existing question stats, and it has been calculated after latest quiz attempt.
            $lateststatstime = $DB->get_field('quiz_statistics', 'COALESCE(MAX(timemodified), 0)',
                    ['hashcode' => $qubaids->get_hash_code()]);

            if ($lateststatstime >= $attempt->mostrecentattempttime) {
                mtrace("    Statistics already calculated at " . userdate($lateststatstime, $dateformat) .
                        " so nothing to do for this quiz.");
                continue;
            }

            mtrace("    Calculating statistics for $attempt->numberofattempts attempts, starting at " .
                    userdate(time(), $dateformat) . " ...");
            try {
                $report = new quiz_statistics_report();
                $report->clear_cached_data($qubaids);
                $report->calculate_questions_stats_for_question_bank($quiz->id);
                mtrace("    Calculations completed at " . userdate(time(), $dateformat) . ".");

            } catch (\Throwable $e) {
                // We don't want an exception from one quiz to stop processing of other quizzes.
                mtrace_exception($e);
                $anyexception = $e;
            }
        }

        if ($anyexception) {
            // If there was any error, ensure the task fails.
            throw $anyexception;
        }
    }
}

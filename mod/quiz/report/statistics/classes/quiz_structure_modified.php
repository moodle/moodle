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

namespace quiz_statistics;

use core\dml\sql_join;

/**
 * Clear the statistics cache when the quiz structure is modified.
 *
 * @package   quiz_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_structure_modified {
    /**
     * Clear the statistics cache.
     *
     * @param int $quizid The quiz to clear the cache for.
     * @return void
     */
    public static function callback(int $quizid): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');
        require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');
        $quiz = $DB->get_record('quiz', ['id' => $quizid]);
        if (!$quiz) {
            throw new \coding_exception('Could not find quiz with ID ' . $quizid . '.');
        }
        $qubaids = quiz_statistics_qubaids_condition(
            $quiz->id,
            new sql_join(),
            $quiz->grademethod
        );

        $report = new \quiz_statistics_report();
        $report->clear_cached_data($qubaids);
    }
}

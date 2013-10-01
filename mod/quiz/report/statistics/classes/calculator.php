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
 * Class to calculate and also manage caching of quiz statistics.
 *
 * These quiz statistics calculations are described here :
 *
 * http://docs.moodle.org/dev/Quiz_statistics_calculations#Test_statistics
 *
 * @package    quiz_statistics
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_calculator {

    /**
     * Compute the quiz statistics.
     *
     * @param int   $quizid            the quiz id.
     * @param int   $currentgroup      the current group. 0 for none.
     * @param bool  $useallattempts    use all attempts, or just first attempts.
     * @param array $groupstudents     students in this group.
     * @param int   $p                 number of positions (slots).
     * @param float $sumofmarkvariance sum of mark variance, calculated as part of question statistics
     * @return quiz_statistics_calculated $quizstats The statistics for overall attempt scores.
     */
    public function calculate($quizid, $currentgroup, $useallattempts, $groupstudents, $p, $sumofmarkvariance) {

        $quizstats = $this->attempt_counts_and_averages($quizid, $currentgroup, $useallattempts, $groupstudents);

        $s = $quizstats->s();

        if ($s == 0) {
            return $quizstats;
        }

        // Recalculate sql again this time possibly including test for first attempt.
        list($fromqa, $whereqa, $qaparams) =
            quiz_statistics_attempts_sql($quizid, $currentgroup, $groupstudents, $useallattempts);

        $quizstats->median = $this->median($s, $fromqa, $whereqa, $qaparams);

        if ($s > 1) {

            $powers = $this->sum_of_powers_of_difference_to_mean($quizstats->avg(), $fromqa, $whereqa, $qaparams);

            $quizstats->standarddeviation = sqrt($powers->power2 / ($s - 1));

            // Skewness.
            if ($s > 2) {
                // See http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise#Skewness_and_Kurtosis.
                $m2 = $powers->power2 / $s;
                $m3 = $powers->power3 / $s;
                $m4 = $powers->power4 / $s;

                $k2 = $s * $m2 / ($s - 1);
                $k3 = $s * $s * $m3 / (($s - 1) * ($s - 2));
                if ($k2 != 0) {
                    $quizstats->skewness = $k3 / (pow($k2, 3 / 2));

                    // Kurtosis.
                    if ($s > 3) {
                        $k4 = $s * $s * ((($s + 1) * $m4) - (3 * ($s - 1) * $m2 * $m2)) / (($s - 1) * ($s - 2) * ($s - 3));
                        $quizstats->kurtosis = $k4 / ($k2 * $k2);
                    }

                    if ($p > 1) {
                        $quizstats->cic = (100 * $p / ($p -1)) * (1 - ($sumofmarkvariance / $k2));
                        $quizstats->errorratio = 100 * sqrt(1 - ($quizstats->cic / 100));
                        $quizstats->standarderror = $quizstats->errorratio *
                            $quizstats->standarddeviation / 100;
                    }
                }

            }
        }

        $quizstats->cache(quiz_statistics_qubaids_condition($quizid, $currentgroup, $groupstudents, $useallattempts));

        return $quizstats;
    }

    /** @var integer Time after which statistics are automatically recomputed. */
    const TIME_TO_CACHE = 900; // 15 minutes.

    /**
     * Load cached statistics from the database.
     *
     * @param $qubaids qubaid_condition
     * @return quiz_statistics_calculated The statistics for overall attempt scores or false if not cached.
     */
    public function get_cached($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        $fromdb = $DB->get_record_select('quiz_statistics', 'hashcode = ? AND timemodified > ?',
                                         array($qubaids->get_hash_code(), $timemodified));
        $stats = new quiz_statistics_calculated();
        $stats->populate_from_record($fromdb);
        return $stats;
    }

    /**
     * Find time of non-expired statistics in the database.
     *
     * @param $qubaids qubaid_condition
     * @return integer|boolean Time of cached record that matches this qubaid_condition or false is non found.
     */
    public function get_last_calculated_time($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        return $DB->get_field_select('quiz_statistics', 'timemodified', 'hashcode = ? AND timemodified > ?',
                                         array($qubaids->get_hash_code(), $timemodified));
    }

    /**
     * Calculating count and mean of marks for first and ALL attempts by students.
     *
     * See : http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise
     *                                      #Calculating_MEAN_of_grades_for_all_attempts_by_students
     * @param int $quizid
     * @param int $currentgroup
     * @param bool $useallattempts
     * @param array $groupstudents
     * @return quiz_statistics_calculated containing calculated counts, totals and averages.
     */
    protected function attempt_counts_and_averages($quizid, $currentgroup, $useallattempts, $groupstudents) {
        global $DB;

        $quizstats = new quiz_statistics_calculated($useallattempts);

        list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql($quizid, $currentgroup, $groupstudents, true);

        $attempttotals = $DB->get_records_sql("
                SELECT
                    CASE WHEN attempt = 1 THEN 1 ELSE 0 END AS isfirst,
                    COUNT(1) AS countrecs,
                    SUM(sumgrades) AS total
                FROM $fromqa
                WHERE $whereqa
                GROUP BY CASE WHEN attempt = 1 THEN 1 ELSE 0 END", $qaparams);

        // Above query that returns sums and counts for first attempt and other non first attempts.
        // We want to work out stats for first attempt or ALL attempts.

        if (isset($attempttotals[1])) {
            $quizstats->firstattemptscount = $attempttotals[1]->countrecs;
            $firstattemptstotal = $attempttotals[1]->total;
        } else {
            $quizstats->firstattemptscount = 0;
            $firstattemptstotal = 0;
        }

        if (isset($attempttotals[0])) {
            $quizstats->allattemptscount = $quizstats->firstattemptscount + $attempttotals[0]->countrecs;
            $allattemptstotal = $firstattemptstotal + $attempttotals[0]->total;
        } else {
            $quizstats->allattemptscount = $quizstats->firstattemptscount;
            $allattemptstotal = $firstattemptstotal;
        }

        if ($quizstats->allattemptscount !== 0) {
            $quizstats->allattemptsavg = $allattemptstotal / $quizstats->allattemptscount;
        }

        if ($quizstats->firstattemptscount !== 0) {
            $quizstats->firstattemptsavg = $firstattemptstotal / $quizstats->firstattemptscount;
        }

        return $quizstats;
    }

    /**
     * Median mark.
     *
     * http://docs.moodle.org/dev/Quiz_statistics_calculations#Median_Score
     *
     * @param $s integer count of attempts
     * @param $fromqa string
     * @param $whereqa string
     * @param $qaparams string
     * @return float
     */
    protected function median($s, $fromqa, $whereqa, $qaparams) {
        global $DB;

        if ($s % 2 == 0) {
            // An even number of attempts.
            $limitoffset = $s / 2 - 1;
            $limit = 2;
        } else {
            $limitoffset = floor($s / 2);
            $limit = 1;
        }
        $sql = "SELECT id, sumgrades
                FROM $fromqa
                WHERE $whereqa
                ORDER BY sumgrades";

        $medianmarks = $DB->get_records_sql_menu($sql, $qaparams, $limitoffset, $limit);

        return array_sum($medianmarks) / count($medianmarks);
    }

    /**
     * Fetch the sum of squared, cubed and to the power 4 differences between sumgrade and it's mean.
     *
     * Explanation here : http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise
     *              #Calculating_Standard_Deviation.2C_Skewness_and_Kurtosis_of_grades_for_all_attempts_by_students
     *
     * @param $mean
     * @param $fromqa
     * @param $whereqa
     * @param $qaparams
     * @return object with properties power2, power3, power4
     */
    protected function sum_of_powers_of_difference_to_mean($mean, $fromqa, $whereqa, $qaparams) {
        global $DB;

        $sql = "SELECT
                    SUM(POWER((quiza.sumgrades - $mean), 2)) AS power2,
                    SUM(POWER((quiza.sumgrades - $mean), 3)) AS power3,
                    SUM(POWER((quiza.sumgrades - $mean), 4)) AS power4
                    FROM $fromqa
                    WHERE $whereqa";
        $params = array('mean1' => $mean, 'mean2' => $mean, 'mean3' => $mean) + $qaparams;

        return $DB->get_record_sql($sql, $params, MUST_EXIST);
    }

}

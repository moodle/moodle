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
 * Quiz attempt walk through using data from csv file.
 *
 * @package    quiz_statistics
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/attempt_walkthrough_from_csv_test.php');
require_once($CFG->dirroot . '/mod/quiz/report/default.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');

/**
 * Quiz attempt walk through using data from csv file.
 *
 * @package    quiz_statistics
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_report_statistics_from_steps_testcase extends mod_quiz_attempt_walkthrough_from_csv_testcase {

    /**
     * @var quiz_statistics_report object to do stats calculations.
     */
    protected $report;

    protected function get_full_path_of_csv_file($setname, $test) {
        // Overridden here so that __DIR__ points to the path of this file.
        return  __DIR__."/fixtures/{$setname}{$test}.csv";
    }

    protected $files = array('questions', 'steps', 'results', 'qstats');

    /**
     * Create a quiz add questions to it, walk through quiz attempts and then check results.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable[] of data read from csv file "questionsXX.csv",
     *                                                                                  "stepsXX.csv" and "resultsXX.csv".
     * @dataProvider get_data_for_walkthrough
     */
    public function test_walkthrough_from_csv($quizsettings, $csvdata) {

        // CSV data files for these tests were generated using :
        // https://github.com/jamiepratt/moodle-quiz-tools/tree/master/responsegenerator

        $this->resetAfterTest(true);
        question_bank::get_qtype('random')->clear_caches_before_testing();

        $this->create_quiz($quizsettings, $csvdata['questions']);

        $attemptids = $this->walkthrough_attempts($csvdata['steps']);

        $this->check_attempts_results($csvdata['results'], $attemptids);

        $this->report = new quiz_statistics_report();
        $whichattempts = QUIZ_GRADEAVERAGE;
        $groupstudents = array();
        $questions = $this->report->load_and_initialise_questions_for_calculations($this->quiz);
        list($quizstats, $questionstats, $subquestionstats) =
                        $this->report->get_quiz_and_questions_stats($this->quiz, $whichattempts, $groupstudents, $questions);

        $qubaids = quiz_statistics_qubaids_condition($this->quiz->id, $groupstudents, $whichattempts);

        // We will create some quiz and question stat calculator instances and some response analyser instances, just in order
        // to check the time of the
        $quizcalc = new quiz_statistics_calculator();
        // Should not be a delay of more than one second between the calculation of stats above and here.
        $this->assertTimeCurrent($quizcalc->get_last_calculated_time($qubaids));

        $qcalc = new \core_question\statistics\questions\calculator($questions);
        $this->assertTimeCurrent($qcalc->get_last_calculated_time($qubaids));

        foreach ($questions as $question) {
            if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
                continue;
            }
            $responesstats = new \core_question\statistics\responses\analyser($question);
            $this->assertTimeCurrent($responesstats->get_last_analysed_time($qubaids));
        }

        // These quiz stats and the question stats found in qstats00.csv were calculated independently in spreadsheets which are
        // available in open document or excel format here :
        // https://github.com/jamiepratt/moodle-quiz-tools/tree/master/statsspreadsheet

        // These quiz stats and the position stats here are calculated in stats.xls and stats.ods available, see above github URL.
        $quizstatsexpected = array(
            'median' => 4.5,
            'firstattemptsavg' => 4.617333332,
            'allattemptsavg' => 4.617333332,
            'firstattemptscount' => 25,
            'allattemptscount' => 25,
            'standarddeviation' => 0.8117265554,
            'skewness' => -0.092502502,
            'kurtosis' => -0.7073968557,
            'cic' => -87.2230935542,
            'errorratio' => 136.8294900795,
            'standarderror' => 1.1106813066
        );

        foreach ($quizstatsexpected as $statname => $statvalue) {
            $this->assertEquals($statvalue, $quizstats->$statname, $quizstats->$statname, abs($statvalue) * 1.5e-5);
        }

        for ($rowno = 0; $rowno < $csvdata['qstats']->getRowCount(); $rowno++) {
            $slotqstats = $csvdata['qstats']->getRow($rowno);
            foreach ($slotqstats as $statname => $slotqstat) {
                if ($statname !== 'slot') {
                    $this->assert_stat_equals($questionstats, $subquestionstats, $slotqstats['slot'],
                                              null, null, $statname, (float)$slotqstat);
                }
            }
        }

        $itemstats = array('s' => 12,
                          'effectiveweight' => null,
                          'discriminationindex' => 35.803933,
                          'discriminativeefficiency' => 39.39393939,
                          'sd' => 0.514928651,
                          'facility' => 0.583333333,
                          'maxmark' => 1,
                          'positions' => '1',
                          'slot' => null,
                          'subquestion' => true);
        foreach ($itemstats as $statname => $expected) {
            $this->assert_stat_equals($questionstats, $subquestionstats, 1, null, 'numerical', $statname, $expected);
        }
    }

    /**
     * Check that the stat is as expected within a reasonable tolerance.
     *
     * @param \core_question\statistics\questions\calculated[] $questionstats
     * @param \core_question\statistics\questions\calculated_for_subquestion[] $subquestionstats
     * @param int                                              $slot
     * @param int|null                                         $variant if null then not a variant stat.
     * @param string|null                                      $subqname if null then not an item stat.
     * @param string                                           $statname
     * @param float                                            $expected
     */
    protected function assert_stat_equals($questionstats, $subquestionstats, $slot, $variant, $subqname, $statname, $expected) {

        if ($variant === null && $subqname === null) {
            $actual = $questionstats[$slot]->{$statname};
        } else if ($subqname !== null) {
            $actual = $subquestionstats[$this->randqids[$slot][$subqname]]->{$statname};
        } else {
            $actual = $questionstats[$slot]->variantstats[$variant]->{$statname};
        }
        if (is_bool($expected) || is_string($expected)) {
            $this->assertEquals($expected, $actual, "$statname for slot $slot");
        } else {
            switch ($statname) {
                case 'covariance' :
                case 'discriminationindex' :
                case 'discriminativeefficiency' :
                case 'effectiveweight' :
                    $precision = 1e-5;
                    break;
                default :
                    $precision = 1e-6;
            }
            $delta = abs($expected) * $precision;
            $this->assertEquals(floatval($expected), $actual, "$statname for slot $slot", $delta);
        }
    }
}

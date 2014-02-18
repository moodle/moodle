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

    protected $files = array('questions', 'steps', 'results', 'qstats', 'responsecounts');

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
        list($quizstats, $questionstats) =
                        $this->report->get_all_stats_and_analysis($this->quiz, $whichattempts, $groupstudents, $questions);

        $qubaids = quiz_statistics_qubaids_condition($this->quiz->id, $groupstudents, $whichattempts);

        // We will create some quiz and question stat calculator instances and some response analyser instances, just in order
        // to check the last analysed time then returned.
        $quizcalc = new \quiz_statistics\calculator();
        // Should not be a delay of more than one second between the calculation of stats above and here.
        $this->assertTimeCurrent($quizcalc->get_last_calculated_time($qubaids));

        $qcalc = new \core_question\statistics\questions\calculator($questions);
        $this->assertTimeCurrent($qcalc->get_last_calculated_time($qubaids));

        $expectedvariantcounts = array(2 => array(1 => 6,
                                                    4 => 4,
                                                    5 => 3,
                                                    6 => 4,
                                                    7 => 2,
                                                    8 => 5,
                                                    10 => 1));

        foreach ($questions as $slot => $question) {
            if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
                continue;
            }
            $responesstats = new \core_question\statistics\responses\analyser($question);
            $this->assertTimeCurrent($responesstats->get_last_analysed_time($qubaids));
            $analysis = $responesstats->load_cached($qubaids);
            $variantsnos = $analysis->get_variant_nos();
            if (isset($expectedvariantcounts[$slot])) {
                // Compare contents, ignore ordering of array, using canonicalize parameter of assertEquals.
                $this->assertEquals(array_keys($expectedvariantcounts[$slot]), $variantsnos, '', 0, 10, true);
            } else {
                $this->assertEquals(array(1), $variantsnos);
            }
            $totalspervariantno = array();
            foreach ($variantsnos as $variantno) {

                $subpartids = $analysis->get_subpart_ids($variantno);
                foreach ($subpartids as $subpartid) {
                    if (!isset($totalspervariantno[$subpartid])) {
                        $totalspervariantno[$subpartid] = array();
                    }
                    $totalspervariantno[$subpartid][$variantno] = 0;

                    $subpartanalysis = $analysis->get_analysis_for_subpart($variantno, $subpartid);
                    $classids = $subpartanalysis->get_response_class_ids();
                    foreach ($classids as $classid) {
                        $classanalysis = $subpartanalysis->get_response_class($classid);
                        $actualresponsecounts = $classanalysis->data_for_question_response_table('', '');
                        foreach ($actualresponsecounts as $actualresponsecount) {
                            $totalspervariantno[$subpartid][$variantno] += $actualresponsecount->count;
                        }
                    }
                }
            }
            // Count all counted responses for each part of question and confirm that counted responses, for most question types
            // are the number of attempts at the question for each question part.
            if ($slot != 5) {
                // Slot 5 holds a multi-choice multiple question.
                // Multi-choice multiple is slightly strange. Actual answer counts given for each sub part do not add up to the
                // total attempt count.
                // This is because each option is counted as a sub part and each option can be off or on in each attempt. Off is
                // not counted in response analysis for this question type.
                foreach ($totalspervariantno as $totalpervariantno) {
                    if (isset($expectedvariantcounts[$slot])) {
                        // If we know how many attempts there are at each variant we can check
                        // that we have counted the correct amount of responses for each variant.
                        $this->assertEquals($expectedvariantcounts[$slot],
                                            $totalpervariantno,
                                            "Totals responses do not add up in response analysis for slot {$slot}.",
                                            0,
                                            10,
                                            true);
                    } else {
                        $this->assertEquals(25,
                                            array_sum($totalpervariantno),
                                            "Totals responses do not add up in response analysis for slot {$slot}.");
                    }
                }
            }
        }
        for ($rowno = 0; $rowno < $csvdata['responsecounts']->getRowCount(); $rowno++) {
            $responsecount = $csvdata['responsecounts']->getRow($rowno);
            if ($responsecount['randq'] == '') {
                $question = $questions[$responsecount['slot']];
            } else {
                $qid = $this->randqids[$responsecount['slot']][$responsecount['randq']];
                $question = question_finder::get_instance()->load_question_data($qid);
            }
            $this->assert_response_count_equals($question, $qubaids, $responsecount);
        }

        // These quiz stats and the question stats found in qstats00.csv were calculated independently in spreadsheet which is
        // available in open document or excel format here :
        // https://github.com/jamiepratt/moodle-quiz-tools/tree/master/statsspreadsheet
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
                    $this->assert_stat_equals($questionstats, $slotqstats['slot'], null, null, $statname, (float)$slotqstat);
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
            $this->assert_stat_equals($questionstats, 1, null, 'numerical', $statname, $expected);
        }

        // These variant's stats are calculated in stats_for_variant_1.xls and stats_for_variant_8.xls
        // The calculations in the spreadsheets are the same but applied just to the attempts where the variants appeared.

        $statsforslot2variants = array(1 => array('s' => 6,
                                                    'effectiveweight' => null,
                                                    'discriminationindex' => -10.5999788,
                                                    'discriminativeefficiency' => -14.28571429,
                                                    'sd' => 0.5477225575,
                                                    'facility' => 0.50,
                                                    'maxmark' => 1,
                                                    'variant' => 1,
                                                    'slot' => 2,
                                                    'subquestion' => false),
                                      8 => array('s' => 5,
                                                    'effectiveweight' => null,
                                                    'discriminationindex' => -57.77466679,
                                                    'discriminativeefficiency' => -71.05263241,
                                                    'sd' => 0.547722558,
                                                    'facility' => 0.40,
                                                    'maxmark' => 1,
                                                    'variant' => 8,
                                                    'slot' => 2,
                                                    'subquestion' => false));
        foreach ($statsforslot2variants as $variant => $stats) {
            foreach ($stats as $statname => $expected) {
                $this->assert_stat_equals($questionstats, 2, $variant, null, $statname, $expected);
            }
        }
        foreach ($expectedvariantcounts as $slot => $expectedvariantcount) {
            foreach ($expectedvariantcount as $variantno => $s) {
                $this->assertEquals($s, $questionstats->for_slot($slot, $variantno)->s);
            }
        }
    }

    /**
     * Check that the stat is as expected within a reasonable tolerance.
     *
     * @param \core_question\statistics\questions\all_calculated_for_qubaid_condition $questionstats
     * @param int                                              $slot
     * @param int|null                                         $variant if null then not a variant stat.
     * @param string|null                                      $subqname if null then not an item stat.
     * @param string                                           $statname
     * @param float                                            $expected
     */
    protected function assert_stat_equals($questionstats, $slot, $variant, $subqname, $statname, $expected) {

        if ($variant === null && $subqname === null) {
            $actual = $questionstats->for_slot($slot)->{$statname};
        } else if ($subqname !== null) {
            $actual = $questionstats->for_subq($this->randqids[$slot][$subqname])->{$statname};
        } else {
            $actual = $questionstats->for_slot($slot, $variant)->{$statname};
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

    protected function assert_response_count_equals($question, $qubaids, $responsecount) {
        $responesstats = new \core_question\statistics\responses\analyser($question);
        $analysis = $responesstats->load_cached($qubaids);
        if (!isset($responsecount['subpart'])) {
            $subpart = 1;
        } else {
            $subpart = $responsecount['subpart'];
        }
        list($subpartid, $responseclassid) = $this->get_response_subpart_and_class_id($question,
                                                                                      $subpart,
                                                                                      $responsecount['modelresponse']);

        $subpartanalysis = $analysis->get_analysis_for_subpart($responsecount['variant'], $subpartid);
        $responseclassanalysis = $subpartanalysis->get_response_class($responseclassid);
        $actualresponsecounts = $responseclassanalysis->data_for_question_response_table('', '');
        if ($responsecount['modelresponse'] !== '[NO RESPONSE]') {
            foreach ($actualresponsecounts as $actualresponsecount) {
                if ($actualresponsecount->response == $responsecount['actualresponse']) {
                    $this->assertEquals($responsecount['count'], $actualresponsecount->count);
                    return;
                }
            }
            throw new coding_exception("Actual response '{$responsecount['actualresponse']}' not found.");
        } else {
            $actualresponsecount = array_pop($actualresponsecounts);
            $this->assertEquals($responsecount['count'], $actualresponsecount->count);
        }
    }

    protected function get_response_subpart_and_class_id($question, $subpart, $modelresponse) {
        $qtypeobj = question_bank::get_qtype($question->qtype, false);
        $possibleresponses = $qtypeobj->get_possible_responses($question);
        $possibleresponsesubpartids = array_keys($possibleresponses);
        if (!isset($possibleresponsesubpartids[$subpart - 1])) {
            throw new coding_exception("Subpart '{$subpart}' not found.");
        }
        $subpartid = $possibleresponsesubpartids[$subpart - 1];

        if ($modelresponse == '[NO RESPONSE]') {
            return array($subpartid, null);

        } else if ($modelresponse == '[NO MATCH]') {
            return array($subpartid, 0);
        }

        $modelresponses = array();
        foreach ($possibleresponses[$subpartid] as $responseclassid => $subpartpossibleresponse) {
            $modelresponses[$responseclassid] = $subpartpossibleresponse->responseclass;
        }
        $this->assertContains($modelresponse, $modelresponses);
        $responseclassid = array_search($modelresponse, $modelresponses);
        return array($subpartid, $responseclassid);
    }

}

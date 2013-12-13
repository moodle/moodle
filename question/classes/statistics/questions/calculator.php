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
 * Question statistics calculator class. Used in the quiz statistics report but also available for use elsewhere.
 *
 * @package    core
 * @subpackage questionbank
 * @copyright  2013 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * This class has methods to compute the question statistics from the raw data.
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculator {

    /**
     * @var calculated[]
     */
    public $questionstats = array();

    /**
     * @var calculated_for_subquestion[]
     */
    public $subquestionstats = array();

    /**
     * @var float
     */
    protected $sumofmarkvariance = 0;

    protected $randomselectors = array();

    /**
     * Constructor.
     *
     * @param object[] questions to analyze, keyed by slot, also analyses sub questions for random questions.
     *                              we expect some extra fields - slot, maxmark and number on the full question data objects.
     */
    public function __construct($questions) {
        foreach ($questions as $slot => $question) {
            $this->questionstats[$slot] = new calculated();
            $this->questionstats[$slot]->questionid = $question->id;
            $this->questionstats[$slot]->question = $question;
            $this->questionstats[$slot]->slot = $slot;
            $this->questionstats[$slot]->positions = $question->number;
            $this->questionstats[$slot]->maxmark = $question->maxmark;
            $this->questionstats[$slot]->randomguessscore = $this->get_random_guess_score($question);
        }
    }

    /**
     * @param $qubaids \qubaid_condition
     * @return array containing two arrays calculated[] and calculated_for_subquestion[].
     */
    public function calculate($qubaids) {
        set_time_limit(0);

        list($lateststeps, $summarks) = $this->get_latest_steps($qubaids);

        if ($lateststeps) {

            // Compute the statistics of position, and for random questions, work
            // out which questions appear in which positions.
            foreach ($lateststeps as $step) {
                $this->initial_steps_walker($step, $this->questionstats[$step->slot], $summarks);

                // If this is a random question what is the real item being used?
                if ($step->questionid != $this->questionstats[$step->slot]->questionid) {
                    if (!isset($this->subquestionstats[$step->questionid])) {
                        $this->subquestionstats[$step->questionid] = new calculated_for_subquestion();
                        $this->subquestionstats[$step->questionid]->questionid = $step->questionid;
                        $this->subquestionstats[$step->questionid]->maxmark = $step->maxmark;
                    } else if ($this->subquestionstats[$step->questionid]->maxmark != $step->maxmark) {
                        $this->subquestionstats[$step->questionid]->differentweights = true;
                    }

                    $this->initial_steps_walker($step, $this->subquestionstats[$step->questionid], $summarks, false);

                    $number = $this->questionstats[$step->slot]->question->number;
                    $this->subquestionstats[$step->questionid]->usedin[$number] = $number;

                    $randomselectorstring = $this->questionstats[$step->slot]->question->category. '/'
                                                                    .$this->questionstats[$step->slot]->question->questiontext;
                    if (!isset($this->randomselectors[$randomselectorstring])) {
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$step->questionid] = $step->questionid;
                }
            }

            foreach ($this->randomselectors as $key => $notused) {
                ksort($this->randomselectors[$key]);
            }

            // Compute the statistics of question id, if we need any.
            $subquestions = question_load_questions(array_keys($this->subquestionstats));
            foreach ($subquestions as $qid => $subquestion) {
                $this->subquestionstats[$qid]->question = $subquestion;
                $this->subquestionstats[$qid]->question->maxmark = $this->subquestionstats[$qid]->maxmark;
                $this->subquestionstats[$qid]->randomguessscore = $this->get_random_guess_score($subquestion);

                $this->initial_question_walker($this->subquestionstats[$qid]);

                if ($this->subquestionstats[$qid]->differentweights) {
                    // TODO output here really sucks, but throwing is too severe.
                    global $OUTPUT;
                    $name = $this->subquestionstats[$qid]->question->name;
                    echo $OUTPUT->notification( get_string('erroritemappearsmorethanoncewithdifferentweight',
                                                            'quiz_statistics', $name));
                }

                if ($this->subquestionstats[$qid]->usedin) {
                    sort($this->subquestionstats[$qid]->usedin, SORT_NUMERIC);
                    $this->subquestionstats[$qid]->positions = implode(',', $this->subquestionstats[$qid]->usedin);
                } else {
                    $this->subquestionstats[$qid]->positions = '';
                }
            }

            // Finish computing the averages, and put the subquestion data into the
            // corresponding questions.

            // This cannot be a foreach loop because we need to have both
            // $question and $nextquestion available, but apart from that it is
            // foreach ($this->questions as $qid => $question).
            reset($this->questionstats);
            while (list($slot, $questionstat) = each($this->questionstats)) {
                $nextquestionstats = current($this->questionstats);

                $this->initial_question_walker($questionstat);

                if ($questionstat->question->qtype == 'random') {
                    $randomselectorstring = $questionstat->question->category .'/'. $questionstat->question->questiontext;
                    if ($nextquestionstats && $nextquestionstats->question->qtype == 'random') {
                        $nextrandomselectorstring  =
                            $nextquestionstats->question->category .'/'. $nextquestionstats->question->questiontext;
                        if ($randomselectorstring == $nextrandomselectorstring) {
                            continue; // Next loop iteration.
                        }
                    }
                    if (isset($this->randomselectors[$randomselectorstring])) {
                        $questionstat->subquestions = implode(',', $this->randomselectors[$randomselectorstring]);
                    }
                }
            }

            // Go through the records one more time.
            foreach ($lateststeps as $step) {
                $this->secondary_steps_walker($step, $this->questionstats[$step->slot], $summarks);

                if ($this->questionstats[$step->slot]->subquestions) {
                    $this->secondary_steps_walker($step, $this->subquestionstats[$step->questionid], $summarks);
                }
            }

            $sumofcovariancewithoverallmark = 0;
            foreach ($this->questionstats as $questionstat) {
                $this->secondary_question_walker($questionstat);

                $this->sumofmarkvariance += $questionstat->markvariance;

                if ($questionstat->covariancewithoverallmark >= 0) {
                    $sumofcovariancewithoverallmark += sqrt($questionstat->covariancewithoverallmark);
                }
            }

            foreach ($this->subquestionstats as $subquestionstat) {
                $this->secondary_question_walker($subquestionstat);
            }

            foreach ($this->questionstats as $questionstat) {
                if ($sumofcovariancewithoverallmark) {
                    if ($questionstat->negcovar) {
                        $questionstat->effectiveweight = null;
                    } else {
                        $questionstat->effectiveweight = 100 * sqrt($questionstat->covariancewithoverallmark) /
                            $sumofcovariancewithoverallmark;
                    }
                } else {
                    $questionstat->effectiveweight = null;
                }
            }
            $this->cache_stats($qubaids);
        }
        return array($this->questionstats, $this->subquestionstats);
    }

    /**
     * Load cached statistics from the database.
     *
     * @param $qubaids \qubaid_condition
     * @return array containing two arrays calculated[] and calculated_for_subquestion[].
     */
    public function get_cached($qubaids) {
        global $DB;
        $timemodified = time() - self::TIME_TO_CACHE;
        $questionstatrecs = $DB->get_records_select('question_statistics', 'hashcode = ? AND timemodified > ?',
                                         array($qubaids->get_hash_code(), $timemodified));

        $questionids = array();
        foreach ($questionstatrecs as $fromdb) {
            if (!$fromdb->slot) {
                $questionids[] = $fromdb->questionid;
            }
        }
        $subquestions = question_load_questions($questionids);
        foreach ($questionstatrecs as $fromdb) {
            if ($fromdb->slot) {
                $this->questionstats[$fromdb->slot]->populate_from_record($fromdb);
                // Array created in constructor and populated from question.
            } else {
                $this->subquestionstats[$fromdb->questionid] = new calculated_for_subquestion();
                $this->subquestionstats[$fromdb->questionid]->populate_from_record($fromdb);
                $this->subquestionstats[$fromdb->questionid]->question = $subquestions[$fromdb->questionid];
            }
        }
        return array($this->questionstats, $this->subquestionstats);
    }

    /**
     * Find time of non-expired statistics in the database.
     *
     * @param $qubaids \qubaid_condition
     * @return integer|boolean Time of cached record that matches this qubaid_condition or false is non found.
     */
    public function get_last_calculated_time($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        return $DB->get_field_select('question_statistics', 'timemodified', 'hashcode = ? AND timemodified > ?',
                                     array($qubaids->get_hash_code(), $timemodified), IGNORE_MULTIPLE);
    }

    /** @var integer Time after which statistics are automatically recomputed. */
    const TIME_TO_CACHE = 900; // 15 minutes.

    /**
     * Used when computing Coefficient of Internal Consistency by quiz statistics.
     *
     * @return float
     */
    public function get_sum_of_mark_variance() {
        return $this->sumofmarkvariance;
    }

    /**
     * @param $qubaids \qubaid_condition
     * @return array with two items
     *              - $lateststeps array of latest step data for the question usages
     *              - $summarks    array of total marks for each usage, indexed by usage id
     */
    protected function get_latest_steps($qubaids) {
        $dm = new \question_engine_data_mapper();

        $fields = "    qas.id,
    qa.questionusageid,
    qa.questionid,
    qa.slot,
    qa.maxmark,
    qas.fraction * qa.maxmark as mark";

        $lateststeps = $dm->load_questions_usages_latest_steps($qubaids, array_keys($this->questionstats), $fields);
        $summarks = array();
        if ($lateststeps) {
            foreach ($lateststeps as $step) {
                if (!isset($summarks[$step->questionusageid])) {
                    $summarks[$step->questionusageid] = 0;
                }
                $summarks[$step->questionusageid] += $step->mark;
            }
        }

        return array($lateststeps, $summarks);
    }

    /**
     * Update $stats->totalmarks, $stats->markarray, $stats->totalothermarks
     * and $stats->othermarksarray to include another state.
     *
     * @param object $step         the state to add to the statistics.
     * @param calculated $stats        the question statistics we are accumulating.
     * @param array  $summarks     of the sum of marks for each question usage, indexed by question usage id
     * @param bool   $positionstat whether this is a statistic of position of question.
     */
    protected function initial_steps_walker($step, $stats, $summarks, $positionstat = true) {
        $stats->s++;
        $stats->totalmarks += $step->mark;
        $stats->markarray[] = $step->mark;

        if ($positionstat) {
            $stats->totalothermarks += $summarks[$step->questionusageid] - $step->mark;
            $stats->othermarksarray[] = $summarks[$step->questionusageid] - $step->mark;

        } else {
            $stats->totalothermarks += $summarks[$step->questionusageid];
            $stats->othermarksarray[] = $summarks[$step->questionusageid];
        }
    }

    /**
     * Perform some computations on the per-question statistics calculations after
     * we have been through all the states.
     *
     * @param calculated $stats question stats to update.
     */
    protected function initial_question_walker($stats) {
        $stats->markaverage = $stats->totalmarks / $stats->s;

        if ($stats->maxmark != 0) {
            $stats->facility = $stats->markaverage / $stats->maxmark;
        } else {
            $stats->facility = null;
        }

        $stats->othermarkaverage = $stats->totalothermarks / $stats->s;

        $stats->summarksaverage = $stats->totalsummarks / $stats->s;

        sort($stats->markarray, SORT_NUMERIC);
        sort($stats->othermarksarray, SORT_NUMERIC);
    }

    /**
     * Now we know the averages, accumulate the date needed to compute the higher
     * moments of the question scores.
     *
     * @param object $step        the state to add to the statistics.
     * @param calculated $stats       the question statistics we are accumulating.
     * @param array  $summarks    of the sum of marks for each question usage, indexed by question usage id
     */
    protected function secondary_steps_walker($step, $stats, $summarks) {
        $markdifference = $step->mark - $stats->markaverage;
        if ($stats->subquestion) {
            $othermarkdifference = $summarks[$step->questionusageid] - $stats->othermarkaverage;
        } else {
            $othermarkdifference = $summarks[$step->questionusageid] - $step->mark - $stats->othermarkaverage;
        }
        $overallmarkdifference = $summarks[$step->questionusageid] - $stats->summarksaverage;

        $sortedmarkdifference = array_shift($stats->markarray) - $stats->markaverage;
        $sortedothermarkdifference = array_shift($stats->othermarksarray) - $stats->othermarkaverage;

        $stats->markvariancesum += pow($markdifference, 2);
        $stats->othermarkvariancesum += pow($othermarkdifference, 2);
        $stats->covariancesum += $markdifference * $othermarkdifference;
        $stats->covariancemaxsum += $sortedmarkdifference * $sortedothermarkdifference;
        $stats->covariancewithoverallmarksum += $markdifference * $overallmarkdifference;
    }

    /**
     * Perform more per-question statistics calculations.
     *
     * @param calculated $stats question stats to update.
     */
    protected function secondary_question_walker($stats) {

        if ($stats->s > 1) {
            $stats->markvariance = $stats->markvariancesum / ($stats->s - 1);
            $stats->othermarkvariance = $stats->othermarkvariancesum / ($stats->s - 1);
            $stats->covariance = $stats->covariancesum / ($stats->s - 1);
            $stats->covariancemax = $stats->covariancemaxsum / ($stats->s - 1);
            $stats->covariancewithoverallmark = $stats->covariancewithoverallmarksum /
                ($stats->s - 1);
            $stats->sd = sqrt($stats->markvariancesum / ($stats->s - 1));

            if ($stats->covariancewithoverallmark >= 0) {
                $stats->negcovar = 0;
            } else {
                $stats->negcovar = 1;
            }
        } else {
            $stats->markvariance = null;
            $stats->othermarkvariance = null;
            $stats->covariance = null;
            $stats->covariancemax = null;
            $stats->covariancewithoverallmark = null;
            $stats->sd = null;
            $stats->negcovar = 0;
        }

        if ($stats->markvariance * $stats->othermarkvariance) {
            $stats->discriminationindex = 100 * $stats->covariance /
                sqrt($stats->markvariance * $stats->othermarkvariance);
        } else {
            $stats->discriminationindex = null;
        }

        if ($stats->covariancemax) {
            $stats->discriminativeefficiency = 100 * $stats->covariance /
                $stats->covariancemax;
        } else {
            $stats->discriminativeefficiency = null;
        }
    }

    /**
     * @param object $questiondata
     * @return number the random guess score for this question.
     */
    protected function get_random_guess_score($questiondata) {
        return \question_bank::get_qtype(
            $questiondata->qtype, false)->get_random_guess_score($questiondata);
    }

    /**
     * @param $qubaids \qubaid_condition
     */
    protected function cache_stats($qubaids) {
        foreach ($this->questionstats as $questionstat) {
            $questionstat->cache($qubaids);
        }

        foreach ($this->subquestionstats as $subquestionstat) {
            $subquestionstat->cache($qubaids);
        }
    }

}

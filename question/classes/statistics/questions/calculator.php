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
     * @var all_calculated_for_qubaid_condition all the stats calculated for slots and sub-questions and variants of those
     *                                                  questions.
     */
    protected $stats;

    /**
     * @var float
     */
    protected $sumofmarkvariance = 0;

    /**
     * @var array[] keyed by a string representing the pool of questions that this random question draws from.
     *              string as returned from {@link \core_question\statistics\questions\calculated::random_selector_string}
     */
    protected $randomselectors = array();

    /**
     * @var \progress_trace
     */
    protected $progress;

    /**
     * @var string The class name of the class to instantiate to store statistics calculated.
     */
    protected $statscollectionclassname = '\core_question\statistics\questions\all_calculated_for_qubaid_condition';

    /**
     * Constructor.
     *
     * @param object[] questions to analyze, keyed by slot, also analyses sub questions for random questions.
     *                              we expect some extra fields - slot, maxmark and number on the full question data objects.
     * @param \core\progress\base|null $progress the element to send progress messages to, default is {@link \core\progress\none}.
     */
    public function __construct($questions, $progress = null) {

        if ($progress === null) {
            $progress = new \core\progress\none();
        }
        $this->progress = $progress;
        $this->stats = new $this->statscollectionclassname();
        foreach ($questions as $slot => $question) {
            $this->stats->initialise_for_slot($slot, $question);
            $this->stats->for_slot($slot)->randomguessscore = $this->get_random_guess_score($question);
        }
    }

    /**
     * Calculate the stats.
     *
     * @param \qubaid_condition $qubaids Which question usages to calculate the stats for?
     * @return all_calculated_for_qubaid_condition The calculated stats.
     */
    public function calculate($qubaids) {

        $this->progress->start_progress('', 6);

        list($lateststeps, $summarks) = $this->get_latest_steps($qubaids);

        if ($lateststeps) {
            $this->progress->start_progress('', count($lateststeps), 1);
            // Compute the statistics of position, and for random questions, work
            // out which questions appear in which positions.
            foreach ($lateststeps as $step) {

                $this->progress->increment_progress();

                $israndomquestion = ($step->questionid != $this->stats->for_slot($step->slot)->questionid);
                $breakdownvariants = !$israndomquestion && $this->stats->for_slot($step->slot)->break_down_by_variant();
                // If this is a variant we have not seen before create a place to store stats calculations for this variant.
                if ($breakdownvariants && !$this->stats->has_slot($step->slot, $step->variant)) {
                    $question = $this->stats->for_slot($step->slot)->question;
                    $this->stats->initialise_for_slot($step->slot, $question, $step->variant);
                    $this->stats->for_slot($step->slot, $step->variant)->randomguessscore =
                                                                                    $this->get_random_guess_score($question);
                }

                // Step data walker for main question.
                $this->initial_steps_walker($step, $this->stats->for_slot($step->slot), $summarks, true, $breakdownvariants);

                // If this is a random question do the calculations for sub question stats.
                if ($israndomquestion) {
                    if (!$this->stats->has_subq($step->questionid)) {
                        $this->stats->initialise_for_subq($step);
                    } else if ($this->stats->for_subq($step->questionid)->maxmark != $step->maxmark) {
                        $this->stats->for_subq($step->questionid)->differentweights = true;
                    }

                    // If this is a variant of this subq we have not seen before create a place to store stats calculations for it.
                    if (!$this->stats->has_subq($step->questionid, $step->variant)) {
                        $this->stats->initialise_for_subq($step, $step->variant);
                    }

                    $this->initial_steps_walker($step, $this->stats->for_subq($step->questionid), $summarks, false);

                    // Extra stuff we need to do in this loop for subqs to keep track of where they need to be displayed later.

                    $number = $this->stats->for_slot($step->slot)->question->number;
                    $this->stats->for_subq($step->questionid)->usedin[$number] = $number;

                    // Keep track of which random questions are actually selected from each pool of questions that random
                    // questions are pulled from.
                    $randomselectorstring = $this->stats->for_slot($step->slot)->random_selector_string();
                    if (!isset($this->randomselectors[$randomselectorstring])) {
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$step->questionid] = $step->questionid;
                }
            }
            $this->progress->end_progress();

            foreach ($this->randomselectors as $key => $notused) {
                ksort($this->randomselectors[$key]);
                $this->randomselectors[$key] = implode(',', $this->randomselectors[$key]);
            }

            $this->stats->subquestions = question_load_questions($this->stats->get_all_subq_ids());
            // Compute the statistics for sub questions, if there are any.
            $this->progress->start_progress('', count($this->stats->subquestions), 1);
            foreach ($this->stats->subquestions as $qid => $subquestion) {
                $this->progress->increment_progress();
                $subquestion->maxmark = $this->stats->for_subq($qid)->maxmark;
                $this->stats->for_subq($qid)->question = $subquestion;
                $this->stats->for_subq($qid)->randomguessscore = $this->get_random_guess_score($subquestion);

                if ($variants = $this->stats->for_subq($qid)->get_variants()) {
                    foreach ($variants as $variant) {
                        $this->stats->for_subq($qid, $variant)->question = $subquestion;
                        $this->stats->for_subq($qid, $variant)->randomguessscore = $this->get_random_guess_score($subquestion);
                    }
                    $this->stats->for_subq($qid)->sort_variants();
                }
                $this->initial_question_walker($this->stats->for_subq($qid));

                if ($this->stats->for_subq($qid)->usedin) {
                    sort($this->stats->for_subq($qid)->usedin, SORT_NUMERIC);
                    $this->stats->for_subq($qid)->positions = implode(',', $this->stats->for_subq($qid)->usedin);
                } else {
                    $this->stats->for_subq($qid)->positions = '';
                }
            }
            $this->progress->end_progress();

            // Finish computing the averages, and put the sub-question data into the
            // corresponding questions.
            $slots = $this->stats->get_all_slots();
            $totalnumberofslots = count($slots);
            $maxindex = $totalnumberofslots - 1;
            $this->progress->start_progress('', $totalnumberofslots, 1);
            foreach ($slots as $index => $slot) {
                $this->stats->for_slot($slot)->sort_variants();
                $this->progress->increment_progress();
                $nextslotindex = $index + 1;
                $nextslot = ($nextslotindex > $maxindex) ? false : $slots[$nextslotindex];

                $this->initial_question_walker($this->stats->for_slot($slot));

                // The rest of this loop is to finish working out where randomly selected question stats should be displayed.
                if ($this->stats->for_slot($slot)->question->qtype == 'random') {
                    $randomselectorstring = $this->stats->for_slot($slot)->random_selector_string();
                    if ($nextslot &&  ($randomselectorstring == $this->stats->for_slot($nextslot)->random_selector_string())) {
                        continue; // Next loop iteration.
                    }
                    if (isset($this->randomselectors[$randomselectorstring])) {
                        $this->stats->for_slot($slot)->subquestions = $this->randomselectors[$randomselectorstring];
                    }
                }
            }
            $this->progress->end_progress();

            // Go through the records one more time.
            $this->progress->start_progress('', count($lateststeps), 1);
            foreach ($lateststeps as $step) {
                $this->progress->increment_progress();
                $israndomquestion = ($this->stats->for_slot($step->slot)->question->qtype == 'random');
                $this->secondary_steps_walker($step, $this->stats->for_slot($step->slot), $summarks);

                if ($israndomquestion) {
                    $this->secondary_steps_walker($step, $this->stats->for_subq($step->questionid), $summarks);
                }
            }
            $this->progress->end_progress();

            $slots = $this->stats->get_all_slots();
            $this->progress->start_progress('', count($slots), 1);
            $sumofcovariancewithoverallmark = 0;
            foreach ($this->stats->get_all_slots() as $slot) {
                $this->progress->increment_progress();
                $this->secondary_question_walker($this->stats->for_slot($slot));

                $this->sumofmarkvariance += $this->stats->for_slot($slot)->markvariance;

                $covariancewithoverallmark = $this->stats->for_slot($slot)->covariancewithoverallmark;
                if (null !== $covariancewithoverallmark && $covariancewithoverallmark >= 0) {
                    $sumofcovariancewithoverallmark += sqrt($covariancewithoverallmark);
                }
            }
            $this->progress->end_progress();

            $subqids = $this->stats->get_all_subq_ids();
            $this->progress->start_progress('', count($subqids), 1);
            foreach ($subqids as $subqid) {
                $this->progress->increment_progress();
                $this->secondary_question_walker($this->stats->for_subq($subqid));
            }
            $this->progress->end_progress();

            foreach ($this->stats->get_all_slots() as $slot) {
                if ($sumofcovariancewithoverallmark) {
                    if ($this->stats->for_slot($slot)->negcovar) {
                        $this->stats->for_slot($slot)->effectiveweight = null;
                    } else {
                        $this->stats->for_slot($slot)->effectiveweight =
                                                        100 * sqrt($this->stats->for_slot($slot)->covariancewithoverallmark) /
                                                        $sumofcovariancewithoverallmark;
                    }
                } else {
                    $this->stats->for_slot($slot)->effectiveweight = null;
                }
            }
            $this->stats->cache($qubaids);
        }
        // All finished.
        $this->progress->end_progress();
        return $this->stats;
    }

    /**
     * Used when computing Coefficient of Internal Consistency by quiz statistics.
     *
     * @return float
     */
    public function get_sum_of_mark_variance() {
        return $this->sumofmarkvariance;
    }

    /**
     * Get the latest step data from the db, from which we will calculate stats.
     *
     * @param \qubaid_condition $qubaids Which question usages to get the latest steps for?
     * @return array with two items
     *              - $lateststeps array of latest step data for the question usages
     *              - $summarks    array of total marks for each usage, indexed by usage id
     */
    protected function get_latest_steps($qubaids) {
        $dm = new \question_engine_data_mapper();

        $fields = "    qas.id,
    qa.questionusageid,
    qa.questionid,
    qa.variant,
    qa.slot,
    qa.maxmark,
    qas.fraction * qa.maxmark as mark";

        $lateststeps = $dm->load_questions_usages_latest_steps($qubaids, $this->stats->get_all_slots(), $fields);
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
     * Calculating the stats is a four step process.
     *
     * We loop through all 'last step' data first.
     *
     * Update $stats->totalmarks, $stats->markarray, $stats->totalothermarks
     * and $stats->othermarksarray to include another state.
     *
     * @param object     $step         the state to add to the statistics.
     * @param calculated $stats        the question statistics we are accumulating.
     * @param array      $summarks     of the sum of marks for each question usage, indexed by question usage id
     * @param bool       $positionstat whether this is a statistic of position of question.
     * @param bool       $dovariantalso do we also want to do the same calculations for this variant?
     */
    protected function initial_steps_walker($step, $stats, $summarks, $positionstat = true, $dovariantalso = true) {
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
        if ($dovariantalso) {
            $this->initial_steps_walker($step, $stats->variantstats[$step->variant], $summarks, $positionstat, false);
        }
    }

    /**
     * Then loop through all questions for the first time.
     *
     * Perform some computations on the per-question statistics calculations after
     * we have been through all the step data.
     *
     * @param calculated $stats question stats to update.
     */
    protected function initial_question_walker($stats) {
        if ($stats->s != 0) {
            $stats->markaverage = $stats->totalmarks / $stats->s;
            $stats->othermarkaverage = $stats->totalothermarks / $stats->s;
            $stats->summarksaverage = $stats->totalsummarks / $stats->s;
        } else {
            $stats->markaverage = 0;
            $stats->othermarkaverage = 0;
            $stats->summarksaverage = 0;
        }

        if ($stats->maxmark != 0) {
            $stats->facility = $stats->markaverage / $stats->maxmark;
        } else {
            $stats->facility = null;
        }

        sort($stats->markarray, SORT_NUMERIC);
        sort($stats->othermarksarray, SORT_NUMERIC);

        // Here we have collected enough data to make the decision about which questions have variants whose stats we also want to
        // calculate. We delete the initialised structures where they are not needed.
        if (!$stats->get_variants() || !$stats->break_down_by_variant()) {
            $stats->clear_variants();
        }

        foreach ($stats->get_variants() as $variant) {
            $this->initial_question_walker($stats->variantstats[$variant]);
        }
    }

    /**
     * Loop through all last step data again.
     *
     * Now we know the averages, accumulate the date needed to compute the higher
     * moments of the question scores.
     *
     * @param object $step        the state to add to the statistics.
     * @param calculated $stats       the question statistics we are accumulating.
     * @param float[]  $summarks    of the sum of marks for each question usage, indexed by question usage id
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

        if (isset($stats->variantstats[$step->variant])) {
            $this->secondary_steps_walker($step, $stats->variantstats[$step->variant], $summarks);
        }
    }

    /**
     * And finally loop through all the questions again.
     *
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

        foreach ($stats->variantstats as $variantstat) {
            $this->secondary_question_walker($variantstat);
        }
    }

    /**
     * Given the question data find the average grade that random guesses would get.
     *
     * @param object $questiondata the full question object.
     * @return float the random guess score for this question.
     */
    protected function get_random_guess_score($questiondata) {
        return \question_bank::get_qtype(
            $questiondata->qtype, false)->get_random_guess_score($questiondata);
    }

    /**
     * Find time of non-expired statistics in the database.
     *
     * @param \qubaid_condition $qubaids Which question usages to look for?
     * @return int|bool Time of cached record that matches this qubaid_condition or false is non found.
     */
    public function get_last_calculated_time($qubaids) {
        return $this->stats->get_last_calculated_time($qubaids);
    }

    /**
     * Load cached statistics from the database.
     *
     * @param \qubaid_condition $qubaids Which question usages to load the cached stats for?
     * @return all_calculated_for_qubaid_condition The cached stats.
     */
    public function get_cached($qubaids) {
        $this->stats->get_cached($qubaids);
        return $this->stats;
    }
}

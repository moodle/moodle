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
     * @var \progress_trace
     */
    protected $progress;

    /**
     * Constructor.
     *
     * @param object[] questions to analyze, keyed by slot, also analyses sub questions for random questions.
     *                              we expect some extra fields - slot, maxmark and number on the full question data objects.
     * @param \core\progress\base|null $progress the element to send progress messages to, default is {@link \core\progress\null}.
     */
    public function __construct($questions, $progress = null) {

        if ($progress === null) {
            $progress = new \core\progress\null();
        }
        $this->progress = $progress;

        foreach ($questions as $slot => $question) {
            $this->questionstats[$slot] = $this->new_slot_stats($question, $slot);
        }
    }

    /**
     * Set up a calculated instance ready to store a questions stats.
     *
     * @param $question
     * @param $slot
     * @return calculated
     */
    protected function new_slot_stats($question, $slot) {
        $toreturn = new calculated();
        $toreturn->questionid = $question->id;
        $toreturn->maxmark = $question->maxmark;
        $toreturn->question = $question;
        $toreturn->slot = $slot;
        $toreturn->positions = $question->number;
        $toreturn->randomguessscore = $this->get_random_guess_score($question);
        return $toreturn;
    }

    /**
     * Set up a calculated instance ready to store a randomly selected question's stats.
     *
     * @param $step
     * @return calculated_for_subquestion
     */
    protected function new_subq_stats($step) {
        $toreturn = new calculated_for_subquestion();
        $toreturn->questionid = $step->questionid;
        $toreturn->maxmark = $step->maxmark;
        return $toreturn;
    }

    /**
     * @param $qubaids \qubaid_condition
     * @return array containing two arrays calculated[] and calculated_for_subquestion[].
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

                $israndomquestion = ($step->questionid != $this->questionstats[$step->slot]->questionid);
                // If this is a variant we have not seen before create a place to store stats calculations for this variant.
                if (!$israndomquestion && !isset($this->questionstats[$step->slot]->variantstats[$step->variant])) {
                    $this->questionstats[$step->slot]->variantstats[$step->variant] =
                        $this->new_slot_stats($this->questionstats[$step->slot]->question, $step->slot);
                    $this->questionstats[$step->slot]->variantstats[$step->variant]->variant = $step->variant;
                }


                // Step data walker for main question.
                $this->initial_steps_walker($step, $this->questionstats[$step->slot], $summarks, true, !$israndomquestion);

                // If this is a random question do the calculations for sub question stats.
                if ($israndomquestion) {
                    if (!isset($this->subquestionstats[$step->questionid])) {
                        $this->subquestionstats[$step->questionid] = $this->new_subq_stats($step);
                    } else if ($this->subquestionstats[$step->questionid]->maxmark != $step->maxmark) {
                        $this->subquestionstats[$step->questionid]->differentweights = true;
                    }

                    // If this is a variant of this subq we have not seen before create a place to store stats calculations for it.
                    if (!isset($this->subquestionstats[$step->questionid]->variantstats[$step->variant])) {
                        $this->subquestionstats[$step->questionid]->variantstats[$step->variant] = $this->new_subq_stats($step);
                        $this->subquestionstats[$step->questionid]->variantstats[$step->variant]->variant = $step->variant;
                    }

                    $this->initial_steps_walker($step, $this->subquestionstats[$step->questionid], $summarks, false);

                    // Extra stuff we need to do in this loop for subqs to keep track of where they need to be displayed later.

                    $number = $this->questionstats[$step->slot]->question->number;
                    $this->subquestionstats[$step->questionid]->usedin[$number] = $number;

                    // Keep track of which random questions are actually selected from each pool of questions that random
                    // questions are pulled from.
                    $randomselectorstring = $this->questionstats[$step->slot]->question->category. '/'
                                                                    .$this->questionstats[$step->slot]->question->questiontext;
                    if (!isset($this->randomselectors[$randomselectorstring])) {
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$step->questionid] = $step->questionid;
                }
            }
            $this->progress->end_progress();

            foreach ($this->randomselectors as $key => $notused) {
                ksort($this->randomselectors[$key]);
            }

            $subquestions = question_load_questions(array_keys($this->subquestionstats));
            // Compute the statistics for sub questions, if there are any.
            $this->progress->start_progress('', count($subquestions), 1);
            foreach ($subquestions as $qid => $subquestion) {
                $this->progress->increment_progress();
                $subquestion->maxmark = $this->subquestionstats[$qid]->maxmark;
                $this->subquestionstats[$qid]->question = $subquestion;
                $this->subquestionstats[$qid]->randomguessscore = $this->get_random_guess_score($subquestion);

                foreach ($this->subquestionstats[$qid]->variantstats as $variantstat) {
                    $variantstat->question = $subquestion;
                    $variantstat->randomguessscore = $this->get_random_guess_score($subquestion);
                }

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
            $this->progress->end_progress();

            // Finish computing the averages, and put the subquestion data into the
            // corresponding questions.

            // This cannot be a foreach loop because we need to have both
            // $question and $nextquestion available, but apart from that it is
            // foreach ($this->questions as $qid => $question).
            reset($this->questionstats);
            $this->progress->start_progress('', count($this->questionstats), 1);
            while (list(, $questionstat) = each($this->questionstats)) {
                $this->progress->increment_progress();
                $nextquestionstats = current($this->questionstats);

                $this->initial_question_walker($questionstat);

                // The rest of this loop is again to work out where randomly selected question stats should be displayed.
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
            $this->progress->end_progress();

            // Go through the records one more time.
            $this->progress->start_progress('', count($lateststeps), 1);
            foreach ($lateststeps as $step) {
                $this->progress->increment_progress();
                $israndomquestion = ($this->questionstats[$step->slot]->question->qtype == 'random');
                $this->secondary_steps_walker($step, $this->questionstats[$step->slot], $summarks, !$israndomquestion);

                if ($this->questionstats[$step->slot]->subquestions) {
                    $this->secondary_steps_walker($step, $this->subquestionstats[$step->questionid], $summarks);
                }
            }
            $this->progress->end_progress();

            $this->progress->start_progress('', count($this->questionstats), 1);
            $sumofcovariancewithoverallmark = 0;
            foreach ($this->questionstats as $questionstat) {
                $this->progress->increment_progress();
                $this->secondary_question_walker($questionstat);

                $this->sumofmarkvariance += $questionstat->markvariance;

                if ($questionstat->covariancewithoverallmark >= 0) {
                    $sumofcovariancewithoverallmark += sqrt($questionstat->covariancewithoverallmark);
                }
            }
            $this->progress->end_progress();

            $this->progress->start_progress('', count($this->subquestionstats), 1);
            foreach ($this->subquestionstats as $subquestionstat) {
                $this->progress->increment_progress();
                $this->secondary_question_walker($subquestionstat);
            }
            $this->progress->end_progress();

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

            // All finished.
            $this->progress->end_progress();
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
            if (is_null($fromdb->variant) && !$fromdb->slot) {
                $questionids[] = $fromdb->questionid;
            }
        }
        $subquestions = question_load_questions($questionids);
        foreach ($questionstatrecs as $fromdb) {
            if (is_null($fromdb->variant)) {
                if ($fromdb->slot) {
                    $this->questionstats[$fromdb->slot]->populate_from_record($fromdb);
                    // Array created in constructor and populated from question.
                } else {
                    $this->subquestionstats[$fromdb->questionid] = new calculated_for_subquestion();
                    $this->subquestionstats[$fromdb->questionid]->populate_from_record($fromdb);
                    $this->subquestionstats[$fromdb->questionid]->question = $subquestions[$fromdb->questionid];
                }
            }
        }
        // Add cached variant stats to data structure.
        foreach ($questionstatrecs as $fromdb) {
            if (!is_null($fromdb->variant)) {
                if ($fromdb->slot) {
                    $newcalcinstance = new calculated();
                    $this->questionstats[$fromdb->slot]->variantstats[$fromdb->variant] = $newcalcinstance;
                    $newcalcinstance->question = $this->questionstats[$fromdb->slot]->question;
                } else {
                    $newcalcinstance = new calculated_for_subquestion();
                    $this->subquestionstats[$fromdb->questionid]->variantstats[$fromdb->variant] = $newcalcinstance;
                    $newcalcinstance->question = $subquestions[$fromdb->questionid];
                }
                $newcalcinstance->populate_from_record($fromdb);
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
    qa.variant,
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
     * Perform some computations on the per-question statistics calculations after
     * we have been through all the step data.
     *
     * @param calculated $stats question stats to update.
     * @param bool       $dovariantsalso do we also want to do the same calculations for the variants?
     */
    protected function initial_question_walker($stats, $dovariantsalso = true) {
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

        if ($dovariantsalso) {
            foreach ($stats->variantstats as $variantstat) {
                $this->initial_question_walker($variantstat, false);
            }
        }
    }

    /**
     * Now we know the averages, accumulate the date needed to compute the higher
     * moments of the question scores.
     *
     * @param object $step        the state to add to the statistics.
     * @param calculated $stats       the question statistics we are accumulating.
     * @param array  $summarks    of the sum of marks for each question usage, indexed by question usage id
     * @param bool   $dovariantalso do we also want to do the same calculations for the variant?
     */
    protected function secondary_steps_walker($step, $stats, $summarks, $dovariantalso = true) {
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

        if ($dovariantalso) {
            $this->secondary_steps_walker($step, $stats->variantstats[$step->variant], $summarks, false);
        }
    }

    /**
     * Perform more per-question statistics calculations.
     *
     * @param calculated $stats question stats to update.
     * @param bool       $dovariantsalso do we also want to do the same calculations for the variants?
     */
    protected function secondary_question_walker($stats, $dovariantsalso = true) {

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


        if ($dovariantsalso) {
            foreach ($stats->variantstats as $variantstat) {
                $this->secondary_question_walker($variantstat, false);
            }
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

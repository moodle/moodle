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
 * Question statistics calculations class. Used in the quiz statistics report but also available for use elsewhere.
 *
 * @package    core
 * @subpackage questionbank
 * @copyright  2013 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This class has methods to compute the question statistics from the raw data.
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_statistics {
    public $questions;
    public $subquestions = array();

    protected $summarksavg;

    protected $sumofmarkvariance = 0;
    protected $randomselectors = array();

    /**
     * Constructor.
     *
     * @param $questions array the main questions indexed by slot.
     */
    public function __construct($questions) {
        foreach ($questions as $slot => $question) {
            $question->_stats = $this->make_blank_question_stats();
            $question->_stats->questionid = $question->id;
            $question->_stats->slot = $slot;
        }

        $this->questions = $questions;
    }

    /**
     * @return object ready to hold all the question statistics.
     */
    protected function make_blank_question_stats() {
        $stats = new stdClass();
        $stats->slot = null;
        $stats->s = 0;
        $stats->totalmarks = 0;
        $stats->totalothermarks = 0;
        $stats->markvariancesum = 0;
        $stats->othermarkvariancesum = 0;
        $stats->covariancesum = 0;
        $stats->covariancemaxsum = 0;
        $stats->subquestion = false;
        $stats->subquestions = '';
        $stats->covariancewithoverallmarksum = 0;
        $stats->randomguessscore = null;
        $stats->markarray = array();
        $stats->othermarksarray = array();
        return $stats;
    }

    /**
     * @param $qubaids qubaid_condition
     * @return array with three items
     *              - $lateststeps array of latest step data for the question usages
     *              - $summarks    array of total marks for each usage, indexed by usage id
     *              - $summarksavg the average of the total marks over all the usages
     */
    protected function get_latest_steps($qubaids) {
        $dm = new question_engine_data_mapper();

        $fields = "    qas.id,
    qa.questionusageid,
    qa.questionid,
    qa.slot,
    qa.maxmark,
    qas.fraction * qa.maxmark as mark";

        $lateststeps = $dm->load_questions_usages_latest_steps($qubaids, array_keys($this->questions), $fields);
        $summarks = array();
        if ($lateststeps) {
            foreach ($lateststeps as $step) {
                if (!isset($summarks[$step->questionusageid])) {
                    $summarks[$step->questionusageid] = 0;
                }
                $summarks[$step->questionusageid] += $step->mark;
            }
            $summarksavg = array_sum($summarks) / count($summarks);
        } else {
            $summarksavg = null;
        }

        return array($lateststeps, $summarks, $summarksavg);
    }

    /**
     * @param $qubaids qubaid_condition
     */
    public function calculate($qubaids) {
        set_time_limit(0);

        list($lateststeps, $summarks, $summarksavg) = $this->get_latest_steps($qubaids);

        if ($lateststeps) {
            $subquestionstats = array();

            // Compute the statistics of position, and for random questions, work
            // out which questions appear in which positions.
            foreach ($lateststeps as $step) {
                $this->initial_steps_walker($step, $this->questions[$step->slot]->_stats, $summarks);

                // If this is a random question what is the real item being used?
                if ($step->questionid != $this->questions[$step->slot]->id) {
                    if (!isset($subquestionstats[$step->questionid])) {
                        $subquestionstats[$step->questionid] = $this->make_blank_question_stats();
                        $subquestionstats[$step->questionid]->questionid = $step->questionid;
                        $subquestionstats[$step->questionid]->usedin = array();
                        $subquestionstats[$step->questionid]->subquestion = true;
                        $subquestionstats[$step->questionid]->differentweights = false;
                        $subquestionstats[$step->questionid]->maxmark = $step->maxmark;
                    } else if ($subquestionstats[$step->questionid]->maxmark != $step->maxmark) {
                        $subquestionstats[$step->questionid]->differentweights = true;
                    }

                    $this->initial_steps_walker($step, $subquestionstats[$step->questionid], $summarks, false);

                    $number = $this->questions[$step->slot]->number;
                    $subquestionstats[$step->questionid]->usedin[$number] = $number;

                    $randomselectorstring = $this->questions[$step->slot]->category .
                        '/' . $this->questions[$step->slot]->questiontext;
                    if (!isset($this->randomselectors[$randomselectorstring])) {
                        $this->randomselectors[$randomselectorstring] = array();
                    }
                    $this->randomselectors[$randomselectorstring][$step->questionid] =
                        $step->questionid;
                }
            }

            foreach ($this->randomselectors as $key => $notused) {
                ksort($this->randomselectors[$key]);
            }

            // Compute the statistics of question id, if we need any.
            $this->subquestions = question_load_questions(array_keys($subquestionstats));
            foreach ($this->subquestions as $qid => $subquestion) {
                $subquestion->_stats = $subquestionstats[$qid];
                $subquestion->maxmark = $subquestion->_stats->maxmark;
                $subquestion->_stats->randomguessscore = $this->get_random_guess_score($subquestion);

                $this->initial_question_walker($subquestion->_stats);

                if ($subquestionstats[$qid]->differentweights) {
                    // TODO output here really sucks, but throwing is too severe.
                    global $OUTPUT;
                    echo $OUTPUT->notification(
                        get_string('erroritemappearsmorethanoncewithdifferentweight',
                                   'quiz_statistics', $this->subquestions[$qid]->name));
                }

                if ($subquestion->_stats->usedin) {
                    sort($subquestion->_stats->usedin, SORT_NUMERIC);
                    $subquestion->_stats->positions = implode(',', $subquestion->_stats->usedin);
                } else {
                    $subquestion->_stats->positions = '';
                }
            }

            // Finish computing the averages, and put the subquestion data into the
            // corresponding questions.

            // This cannot be a foreach loop because we need to have both
            // $question and $nextquestion available, but apart from that it is
            // foreach ($this->questions as $qid => $question).
            reset($this->questions);
            while (list($slot, $question) = each($this->questions)) {
                $nextquestion = current($this->questions);
                $question->_stats->positions = $question->number;
                $question->_stats->maxmark = $question->maxmark;
                $question->_stats->randomguessscore = $this->get_random_guess_score($question);

                $this->initial_question_walker($question->_stats);

                if ($question->qtype == 'random') {
                    $randomselectorstring = $question->category.'/'.$question->questiontext;
                    if ($nextquestion && $nextquestion->qtype == 'random') {
                        $nextrandomselectorstring = $nextquestion->category . '/' .
                            $nextquestion->questiontext;
                        if ($randomselectorstring == $nextrandomselectorstring) {
                            continue; // Next loop iteration.
                        }
                    }
                    if (isset($this->randomselectors[$randomselectorstring])) {
                        $question->_stats->subquestions = implode(',',
                                                                  $this->randomselectors[$randomselectorstring]);
                    }
                }
            }

            // Go through the records one more time.
            foreach ($lateststeps as $step) {
                $this->secondary_steps_walker($step, $this->questions[$step->slot]->_stats, $summarks, $summarksavg);

                if ($this->questions[$step->slot]->qtype == 'random') {
                    $this->secondary_steps_walker($step, $this->subquestions[$step->questionid]->_stats, $summarks, $summarksavg);
                }
            }

            $sumofcovariancewithoverallmark = 0;
            foreach ($this->questions as $slot => $question) {
                $this->secondary_question_walker($question->_stats);

                $this->sumofmarkvariance += $question->_stats->markvariance;

                if ($question->_stats->covariancewithoverallmark >= 0) {
                    $sumofcovariancewithoverallmark +=
                        sqrt($question->_stats->covariancewithoverallmark);
                    $question->_stats->negcovar = 0;
                } else {
                    $question->_stats->negcovar = 1;
                }
            }

            foreach ($this->subquestions as $subquestion) {
                $this->secondary_question_walker($subquestion->_stats);
            }

            foreach ($this->questions as $question) {
                if ($sumofcovariancewithoverallmark) {
                    if ($question->_stats->negcovar) {
                        $question->_stats->effectiveweight = null;
                    } else {
                        $question->_stats->effectiveweight = 100 *
                            sqrt($question->_stats->covariancewithoverallmark) /
                            $sumofcovariancewithoverallmark;
                    }
                } else {
                    $question->_stats->effectiveweight = null;
                }
            }
            $this->cache_stats($qubaids);
        }


    }

    /**
     * @param $qubaids qubaid_condition
     */
    protected function cache_stats($qubaids) {
        global $DB;
        $cachetime = time();
        foreach ($this->questions as $question) {
            $question->_stats->hashcode = $qubaids->get_hash_code();
            $question->_stats->timemodified = $cachetime;
            $DB->insert_record('question_statistics', $question->_stats, false);
        }

        foreach ($this->subquestions as $subquestion) {
            $subquestion->_stats->hashcode = $qubaids->get_hash_code();
            $subquestion->_stats->timemodified = $cachetime;
            $DB->insert_record('question_statistics', $subquestion->_stats, false);
        }

    }

    /**
     * Update $stats->totalmarks, $stats->markarray, $stats->totalothermarks
     * and $stats->othermarksarray to include another state.
     *
     * @param object $step the state to add to the statistics.
     * @param object $stats the question statistics we are accumulating.
     * @param array  $summarks of the sum of marks for each question usage, indexed by question usage id
     * @param bool $positionstat whether this is a statistic of position of question.
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
     * @param object $stats quetsion stats to update.
     */
    protected function initial_question_walker($stats) {
        $stats->markaverage = $stats->totalmarks / $stats->s;

        if ($stats->maxmark != 0) {
            $stats->facility = $stats->markaverage / $stats->maxmark;
        } else {
            $stats->facility = null;
        }

        $stats->othermarkaverage = $stats->totalothermarks / $stats->s;

        sort($stats->markarray, SORT_NUMERIC);
        sort($stats->othermarksarray, SORT_NUMERIC);
    }

    /**
     * Now we know the averages, accumulate the date needed to compute the higher
     * moments of the question scores.
     *
     * @param object $step     the state to add to the statistics.
     * @param object $stats    the question statistics we are accumulating.
     * @param array  $summarks of the sum of marks for each question usage, indexed by question usage id
     * @param float  $summarksavg the average sum of marks for all question usages
     */
    protected function secondary_steps_walker($step, $stats, $summarks, $summarksavg) {
        $markdifference = $step->mark - $stats->markaverage;
        if ($stats->subquestion) {
            $othermarkdifference = $summarks[$step->questionusageid] - $stats->othermarkaverage;
        } else {
            $othermarkdifference = $summarks[$step->questionusageid] - $step->mark -
                    $stats->othermarkaverage;
        }
        $overallmarkdifference = $summarks[$step->questionusageid] - $summarksavg;

        $sortedmarkdifference = array_shift($stats->markarray) - $stats->markaverage;
        $sortedothermarkdifference = array_shift($stats->othermarksarray) -
                $stats->othermarkaverage;

        $stats->markvariancesum += pow($markdifference, 2);
        $stats->othermarkvariancesum += pow($othermarkdifference, 2);
        $stats->covariancesum += $markdifference * $othermarkdifference;
        $stats->covariancemaxsum += $sortedmarkdifference * $sortedothermarkdifference;
        $stats->covariancewithoverallmarksum += $markdifference * $overallmarkdifference;
    }

    /**
     * Perform more per-question statistics calculations.
     *
     * @param object $stats quetsion stats to update.
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

        } else {
            $stats->markvariance = null;
            $stats->othermarkvariance = null;
            $stats->covariance = null;
            $stats->covariancemax = null;
            $stats->covariancewithoverallmark = null;
            $stats->sd = null;
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
        return question_bank::get_qtype(
                $questiondata->qtype, false)->get_random_guess_score($questiondata);
    }

    /**
     * Used when computing CIC.
     * @return number
     */
    public function get_sum_of_mark_variance() {
        return $this->sumofmarkvariance;
    }

    /**
     * @param qubaid_condition $qubaids
     */
    public function get_cached($qubaids) {
        global $DB;
        $questionstats = $DB->get_records('question_statistics',
                                          array('hashcode' => $qubaids->get_hash_code()));

        $subquestionstats = array();
        foreach ($questionstats as $stat) {
            if ($stat->slot) {
                $this->questions[$stat->slot]->_stats = $stat;
            } else {
                $subquestionstats[$stat->questionid] = $stat;
            }
        }

        if (!empty($subquestionstats)) {
            $subqstofetch = array_keys($subquestionstats);
            $this->subquestions = question_load_questions($subqstofetch);
            foreach ($this->subquestions as $subqid => $subq) {
                $this->subquestions[$subqid]->_stats = $subquestionstats[$subqid];
                $this->subquestions[$subqid]->maxmark = $subq->defaultmark;
            }
        }
    }

}

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

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * This class is used to return the stats as calculated by {@link \core_question\statistics\questions\calculator}
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated {

    public $questionid;

    // These first fields are the final fields cached in the db and shown in reports.

    // See : http://docs.moodle.org/dev/Quiz_statistics_calculations#Position_statistics .

    public $slot = null;

    /**
     * @var null|integer if this property is not null then this is the stats for a variant of a question or when inherited by
     *                   calculated_for_subquestion and not null then this is the stats for a variant of a sub question.
     */
    public $variant = null;

    /**
     * @var bool is this a sub question.
     */
    public $subquestion = false;

    /**
     * @var string if this stat has been picked as a min, median or maximum facility value then this string says which stat this
     *                  is. Prepended to question name for display.
     */
    public $minmedianmaxnotice = '';

    /**
     * @var int total attempts at this question.
     */
    public $s = 0;

    /**
     * @var float effective weight of this question.
     */
    public $effectiveweight;

    /**
     * @var bool is covariance of this questions mark with other question marks negative?
     */
    public $negcovar;

    /**
     * @var float
     */
    public $discriminationindex;

    /**
     * @var float
     */
    public $discriminativeefficiency;

    /**
     * @var float standard deviation
     */
    public $sd;

    /**
     * @var float
     */
    public $facility;

    /**
     * @var float max mark achievable for this question.
     */
    public $maxmark;

    /**
     * @var string comma separated list of the positions in which this question appears.
     */
    public $positions;

    /**
     * @var null|float The average score that students would have got by guessing randomly. Or null if not calculable.
     */
    public $randomguessscore = null;

    // End of fields in db.

    protected $fieldsindb = array('questionid', 'slot', 'subquestion', 's', 'effectiveweight', 'negcovar', 'discriminationindex',
        'discriminativeefficiency', 'sd', 'facility', 'subquestions', 'maxmark', 'positions', 'randomguessscore', 'variant');

    // Fields used for intermediate calculations.

    public $totalmarks = 0;

    public $totalothermarks = 0;

    /**
     * @var float The total of marks achieved for all positions in all attempts where this item was seen.
     */
    public $totalsummarks = 0;

    public $markvariancesum = 0;

    public $othermarkvariancesum = 0;

    public $covariancesum = 0;

    public $covariancemaxsum = 0;

    public $subquestions = '';

    public $covariancewithoverallmarksum = 0;

    public $markarray = array();

    public $othermarksarray = array();

    public $markaverage;

    public $othermarkaverage;

    /**
     * @var float The average for all attempts, of the sum of the marks for all positions in which this item appeared.
     */
    public $summarksaverage;

    public $markvariance;
    public $othermarkvariance;
    public $covariance;
    public $covariancemax;
    public $covariancewithoverallmark;

    /**
     * @var object full question data
     */
    public $question;

    /**
     * An array of calculated stats for each variant of the question. Even when there is just one variant we still calculate this
     * data as there is no way to know if there are variants before we have finished going through the attempt data one time.
     *
     * @var calculated[] $variants
     */
    public $variantstats = array();

    /**
     * Set if this record has been retrieved from cache. This is the time that the statistics were calculated.
     *
     * @var integer
     */
    public $timemodified;

    /**
     * Set up a calculated instance ready to store a question's (or a variant of a slot's question's)
     * stats for one slot in the quiz.
     *
     * @param null|object     $question
     * @param null|int     $slot
     * @param null|int $variant
     */
    public function __construct($question = null, $slot = null, $variant = null) {
        if ($question !== null) {
            $this->questionid = $question->id;
            $this->maxmark = $question->maxmark;
            $this->positions = $question->number;
            $this->question = $question;
        }
        if ($slot !== null) {
            $this->slot = $slot;
        }
        if ($variant !== null) {
            $this->variant = $variant;
        }
    }

    /**
     * Used to determine which random questions pull sub questions from the same pools. Where pool means category and possibly
     * all the sub categories of that category.
     *
     * @return null|string represents the pool of questions from which this question draws if it is random, or null if not.
     */
    public function random_selector_string() {
        if ($this->question->qtype == 'random') {
            return $this->question->category .'/'. $this->question->questiontext;
        } else {
            return null;
        }
    }

    /**
     * Cache calculated stats stored in this object in 'question_statistics' table.
     *
     * @param \qubaid_condition $qubaids
     * @param int|null $timemodified the modified time to store. Defaults to the current time.
     */
    public function cache($qubaids, $timemodified = null) {
        global $DB;
        $toinsert = new \stdClass();
        $toinsert->hashcode = $qubaids->get_hash_code();
        $toinsert->timemodified = $timemodified ?? time();
        foreach ($this->fieldsindb as $field) {
            $toinsert->{$field} = $this->{$field};
        }
        $DB->insert_record('question_statistics', $toinsert, false);

        if ($this->get_variants()) {
            foreach ($this->variantstats as $variantstat) {
                $variantstat->cache($qubaids, $timemodified);
            }
        }
    }

    /**
     * Load properties of this class from db record.
     *
     * @param object $record Given a record from 'question_statistics' copy stats from record to properties.
     */
    public function populate_from_record($record) {
        foreach ($this->fieldsindb as $field) {
            $this->$field = $record->$field;
        }
        $this->timemodified = $record->timemodified;
    }

    /**
     * Sort the variants of this question by variant number.
     */
    public function sort_variants() {
        ksort($this->variantstats);
    }

    /**
     * Get any sub question ids for this question.
     *
     * @return int[] array of sub-question ids or empty array if there are none.
     */
    public function get_sub_question_ids() {
        if ($this->subquestions !== '') {
            return explode(',', $this->subquestions);
        } else {
            return array();
        }
    }

    /**
     * Array of variants that have appeared in the attempt data for this question. Or an empty array if there is only one variant.
     *
     * @return int[] the variant nos.
     */
    public function get_variants() {
        $variants = array_keys($this->variantstats);
        if (count($variants) > 1 || reset($variants) != 1) {
            return $variants;
        } else {
            return array();
        }
    }

    /**
     * Do we break down the stats for this question by variant or not?
     *
     * @return bool Do we?
     */
    public function break_down_by_variant() {
        $qtype = \question_bank::get_qtype($this->question->qtype);
        return $qtype->break_down_stats_and_response_analysis_by_variant($this->question);
    }


    /**
     * Delete the data structure for storing variant stats.
     */
    public function clear_variants() {
        $this->variantstats = array();
    }
}

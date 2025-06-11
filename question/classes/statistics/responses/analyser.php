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
 * This file contains the code to analyse all the responses to a particular question.
 *
 * @package    core_question
 * @copyright  2014 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;
defined('MOODLE_INTERNAL') || die();

/**
 * This class can compute, store and cache the analysis of the responses to a particular question.
 *
 * @package    core_question
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analyser {
    /**
     * @var int When analysing responses and breaking down the count of responses per try, how many columns should we break down
     * tries into? This is set to 5 columns, any response in a try more than try 5 will be counted in the fifth column.
     */
    const MAX_TRY_COUNTED = 5;

    /**
     * @var int previously, the time after which statistics are automatically recomputed.
     * @deprecated since Moodle 4.3. Use of pre-computed stats is no longer time-limited.
     * @todo MDL-78090 Final deprecation in Moodle 4.7
     */
    const TIME_TO_CACHE = 900; // 15 minutes.

    /** @var object full question data from db. */
    protected $questiondata;

    /**
     * @var analysis_for_question|analysis_for_question_all_tries
     */
    public $analysis;

    /**
     * @var int used during calculations, so all results are stored with the same timestamp.
     */
    protected $calculationtime;

    /**
     * @var array Two index array first index is unique string for each sub question part, the second string index is the 'class'
     * that sub-question part can be classified into.
     *
     * This is the return value from {@link \question_type::get_possible_responses()} see that method for fuller documentation.
     */
    public $responseclasses = array();

    /**
     * @var bool whether to break down response analysis by variant. This only applies to questions that have variants and is
     *           used to suppress the break down of analysis by variant when there are going to be very many variants.
     */
    protected $breakdownbyvariant;

    /**
     * Create a new instance of this class for holding/computing the statistics
     * for a particular question.
     *
     * @param object $questiondata the full question data from the database defining this question.
     * @param string $whichtries   which tries to analyse.
     */
    public function __construct($questiondata, $whichtries = \question_attempt::LAST_TRY) {
        $this->questiondata = $questiondata;
        $qtypeobj = \question_bank::get_qtype($this->questiondata->qtype);
        if ($whichtries != \question_attempt::ALL_TRIES) {
            $this->analysis = new analysis_for_question($qtypeobj->get_possible_responses($this->questiondata));
        } else {
            $this->analysis = new analysis_for_question_all_tries($qtypeobj->get_possible_responses($this->questiondata));
        }

        $this->breakdownbyvariant = $qtypeobj->break_down_stats_and_response_analysis_by_variant($this->questiondata);
    }

    /**
     * Does the computed analysis have sub parts?
     *
     * @return bool whether this analysis has more than one subpart.
     */
    public function has_subparts() {
        return count($this->responseclasses) > 1;
    }

    /**
     * Does the computed analysis's sub parts have classes?
     *
     * @return bool whether this analysis has (a subpart with) more than one response class.
     */
    public function has_response_classes() {
        foreach ($this->responseclasses as $partclasses) {
            if (count($partclasses) > 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Analyse all the response data for all the specified attempts at this question.
     *
     * @param \qubaid_condition $qubaids which attempts to consider.
     * @param string $whichtries         which tries to analyse. Will be one of
     *                                   \question_attempt::FIRST_TRY, LAST_TRY or ALL_TRIES.
     * @return analysis_for_question
     */
    public function calculate($qubaids, $whichtries = \question_attempt::LAST_TRY) {
        $this->calculationtime = time();
        // Load data.
        $dm = new \question_engine_data_mapper();
        $questionattempts = $dm->load_attempts_at_question($this->questiondata->id, $qubaids);

        // Analyse it.
        foreach ($questionattempts as $qa) {
            $responseparts = $qa->classify_response($whichtries);
            if ($this->breakdownbyvariant) {
                $this->analysis->count_response_parts($qa->get_variant(), $responseparts);
            } else {
                $this->analysis->count_response_parts(1, $responseparts);
            }

        }
        $this->analysis->cache($qubaids, $whichtries, $this->questiondata->id, $this->calculationtime);
        return $this->analysis;
    }

    /**
     * Retrieve the computed response analysis from the question_response_analysis table.
     *
     * @param \qubaid_condition $qubaids    load the analysis of which question usages?
     * @param string            $whichtries load the analysis of which tries?
     * @return analysis_for_question|boolean analysis or false if no cached analysis found.
     */
    public function load_cached($qubaids, $whichtries) {
        global $DB;

        $timemodified = self::get_last_analysed_time($qubaids, $whichtries);
        // Variable name 'analyses' is the plural of 'analysis'.
        $responseanalyses = $DB->get_records('question_response_analysis',
                ['hashcode' => $qubaids->get_hash_code(), 'whichtries' => $whichtries,
                        'questionid' => $this->questiondata->id, 'timemodified' => $timemodified]);
        if (!$responseanalyses) {
            return false;
        }

        $analysisids = [];
        foreach ($responseanalyses as $responseanalysis) {
            $analysisforsubpart = $this->analysis->get_analysis_for_subpart($responseanalysis->variant, $responseanalysis->subqid);
            $class = $analysisforsubpart->get_response_class($responseanalysis->aid);
            $class->add_response($responseanalysis->response, $responseanalysis->credit);
            $analysisids[] = $responseanalysis->id;
        }
        [$sql, $params] = $DB->get_in_or_equal($analysisids);
        $counts = $DB->get_records_select('question_response_count', "analysisid {$sql}", $params);
        foreach ($counts as $count) {
            $responseanalysis = $responseanalyses[$count->analysisid];
            $analysisforsubpart = $this->analysis->get_analysis_for_subpart($responseanalysis->variant, $responseanalysis->subqid);
            $class = $analysisforsubpart->get_response_class($responseanalysis->aid);
            $class->set_response_count($responseanalysis->response, $count->try, $count->rcount);

        }
        return $this->analysis;
    }


    /**
     * Find time of non-expired analysis in the database.
     *
     * @param \qubaid_condition $qubaids    check for the analysis of which question usages?
     * @param string            $whichtries check for the analysis of which tries?
     * @return integer|boolean Time of cached record that matches this qubaid_condition or false if none found.
     */
    public function get_last_analysed_time($qubaids, $whichtries) {
        global $DB;
        return $DB->get_field('question_response_analysis', 'MAX(timemodified)',
                ['hashcode' => $qubaids->get_hash_code(), 'whichtries' => $whichtries,
                        'questionid' => $this->questiondata->id]);
    }
}

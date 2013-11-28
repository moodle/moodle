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
 * This file contains the code to analyse all the responses to a particular
 * question.
 *
 * @package    core_question
 * @copyright  2013 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;
defined('MOODLE_INTERNAL') || die();

/**
 * This class can store and compute the analysis of the responses to a particular
 * question.
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analyser {
    /** @var object full question data from db. */
    protected $questiondata;

    /**
     * @var analysis_for_question
     */
    public $analysis;

    /**
     * @var array Two index array first index is unique for each sub question part, the second index is the 'class' that this sub
     *          question part can be classified into. This is the return value from {@link \question_type::get_possible_responses()}
     */
    public $responseclasses = array();

    /**
     * Create a new instance of this class for holding/computing the statistics
     * for a particular question.
     *
     * @param object $questiondata the full question data from the database defining this question.
     */
    public function __construct($questiondata) {
        $this->questiondata = $questiondata;
        $qtypeobj = \question_bank::get_qtype($this->questiondata->qtype);
        $this->analysis = new analysis_for_question($qtypeobj->get_possible_responses($this->questiondata));

    }

    /**
     * @return bool whether this analysis has more than one subpart.
     */
    public function has_subparts() {
        return count($this->responseclasses) > 1;
    }

    /**
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
     * @return bool whether this analysis has a response class more than one
     *      different acutal response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        foreach ($this->responseclasses as $subpartid => $partclasses) {
            foreach ($partclasses as $responseclassid => $modelresponse) {
                $numresponses = count($this->responses[$subpartid][$responseclassid]);
                if ($numresponses > 1) {
                    return true;
                }
                $actualresponse = key($this->responses[$subpartid][$responseclassid]);
                if ($numresponses == 1 && $actualresponse != $modelresponse->responseclass) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Analyse all the response data for for all the specified attempts at
     * this question.
     * @param \qubaid_condition $qubaids which attempts to consider.
     * @return analysis_for_question
     */
    public function calculate($qubaids) {
        // Load data.
        $dm = new \question_engine_data_mapper();
        $questionattempts = $dm->load_attempts_at_question($this->questiondata->id, $qubaids);

        // Analyse it.
        foreach ($questionattempts as $qa) {
            $responseparts = $qa->classify_response();
            $this->analysis->count_response_parts($responseparts);
        }
        $this->analysis->cache($qubaids, $this->questiondata->id);
        return $this->analysis;
    }

    /** @var integer Time after which responses are automatically reanalysed. */
    const TIME_TO_CACHE = 900; // 15 minutes.


    /**
     * Retrieve the computed response analysis from the question_response_analysis table.
     *
     * @param \qubaid_condition $qubaids which attempts to get cached response analysis for.
     * @return analysis_for_question|boolean analysis or false if no cached analysis found.
     */
    public function load_cached($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        $rows = $DB->get_records_select('question_response_analysis', 'hashcode = ? AND questionid = ? AND timemodified > ?',
                                        array($qubaids->get_hash_code(), $this->questiondata->id, $timemodified));
        if (!$rows) {
            return false;
        }

        foreach ($rows as $row) {
            $class = $this->analysis->get_subpart($row->subqid)->get_response_class($row->aid);
            $class->add_response_and_count($row->response, $row->credit, $row->rcount);
        }
        return $this->analysis;
    }


    /**
     * Find time of non-expired analysis in the database.
     *
     * @param $qubaids \qubaid_condition
     * @return integer|boolean Time of cached record that matches this qubaid_condition or false if none found.
     */
    public function get_last_analysed_time($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        return $DB->get_field_select('question_response_analysis', 'timemodified',
                                     'hashcode = ? AND questionid = ? AND timemodified > ?',
                                     array($qubaids->get_hash_code(), $this->questiondata->id, $timemodified), IGNORE_MULTIPLE);
    }
}

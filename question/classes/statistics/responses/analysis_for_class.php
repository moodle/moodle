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
 * @package    core_question
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;



/**
 * Counts a class of responses for this sub part of the question.
 *
 * No response is one possible class of response to a question.
 *
 * - There is a separate data structure for each question or sub question's analysis
 * {@link \core_question\statistics\responses\analysis_for_question}
 * or {@link \core_question\statistics\responses\analysis_for_question_all_tries}.
 * - There are separate analysis for each variant in this top level instance.
 * - Then there are class instances representing the analysis of each of the sub parts of each variant of the question.
 * {@link \core_question\statistics\responses\analysis_for_subpart}.
 * - Then within the sub part analysis there are response class analysis
 * {@link \core_question\statistics\responses\analysis_for_class}.
 * - Then within each class analysis there are analysis for each actual response
 * {@link \core_question\statistics\responses\analysis_for_actual_response}.
 *
 * @package    core_question
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analysis_for_class {

    /**
     * @var string must be unique for each response class within this sub part.
     */
    protected $responseclassid;

    /**
     * @var string represent this class in the response analysis table.
     */
    protected $modelresponse;

    /** @var string the (partial) credit awarded for this responses. */
    protected $fraction;

    /** @var analysis_for_actual_response[] key is the actual response represented as a string as it will be displayed in report.
     */
    protected $actualresponses = array();

    /**
     * Constructor, just an easy way to set the fields.
     *
     * @param \question_possible_response $possibleresponse
     * @param string                      $responseclassid
     */
    public function __construct($possibleresponse, $responseclassid) {
        $this->modelresponse = $possibleresponse->responseclass;
        $this->fraction = $possibleresponse->fraction;
        $this->responseclassid = $responseclassid;
    }

    /**
     * Keep a count of a response to this question sub part that falls within this class.
     *
     * @param string     $actualresponse
     * @param float|null $fraction
     * @param int        $try
     * @return \core_question\statistics\responses\analysis_for_actual_response
     */
    public function count_response($actualresponse, $fraction, $try) {
        if (!isset($this->actualresponses[$actualresponse])) {
            if ($fraction === null) {
                $fraction = $this->fraction;
            }
            $this->add_response($actualresponse, $fraction);
        }
        $this->get_response($actualresponse)->increment_count($try);
    }

    /**
     * Cache analysis for class.
     *
     * @param \qubaid_condition $qubaids    which question usages have been analysed.
     * @param string            $whichtries which tries have been analysed?
     * @param int               $questionid which question.
     * @param int               $variantno  which variant.
     * @param string            $subpartid  which sub part.
     */
    public function cache($qubaids, $whichtries, $questionid, $variantno, $subpartid) {
        foreach ($this->get_responses() as $response) {
            $analysisforactualresponse = $this->get_response($response);
            $analysisforactualresponse->cache($qubaids, $whichtries, $questionid, $variantno, $subpartid, $this->responseclassid);
        }
    }

    /**
     * Add an actual response to the data structure.
     *
     * @param string $response A string representing the actual response.
     * @param float  $fraction The fraction of grade awarded for this response.
     */
    public function add_response($response, $fraction) {
        $this->actualresponses[$response] = new analysis_for_actual_response($response, $fraction);
    }

    /**
     * Used when loading cached counts.
     *
     * @param string $response
     * @param int $try the try number, will be zero if not keeping track of try.
     * @param int $count the count
     */
    public function set_response_count($response, $try, $count) {
        $this->actualresponses[$response]->set_count($try, $count);
    }

    /**
     * Are there actual responses to sub parts that where classified into this class?
     *
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        $actualresponses = $this->get_responses();
        if (count($actualresponses) > 1) {
            return true;
        } else if (count($actualresponses) === 1) {
            $singleactualresponse = reset($actualresponses);
            return (string)$singleactualresponse !== (string)$this->modelresponse;
        }
        return false;
    }

    /**
     * Return the data to display in the response analysis table.
     *
     * @param bool $responseclasscolumn
     * @param string $partid
     * @return object[]
     */
    public function data_for_question_response_table($responseclasscolumn, $partid) {
        $return = array();
        if (count($this->get_responses()) == 0) {
            $rowdata = new \stdClass();
            $rowdata->part = $partid;
            $rowdata->responseclass = $this->modelresponse;
            if (!$responseclasscolumn) {
                $rowdata->response = $this->modelresponse;
            } else {
                $rowdata->response = '';
            }
            $rowdata->fraction = $this->fraction;
            $rowdata->totalcount = 0;
            $rowdata->trycount = array();
            $return[] = $rowdata;
        } else {
            foreach ($this->get_responses() as $actualresponse) {
                $response = $this->get_response($actualresponse);
                $return[] = $response->data_for_question_response_table($partid, $this->modelresponse);
            }
        }
        return $return;
    }

    /**
     * What is the highest try number that an actual response of this response class has been seen?
     *
     * @return int try number
     */
    public function get_maximum_tries() {
        $max = 1;
        foreach ($this->get_responses() as $actualresponse) {
            $max = max($max, $this->get_response($actualresponse)->get_maximum_tries());
        }
        return $max;
    }

    /**
     * Return array of the actual responses to this sub part that were classified into this class.
     *
     * @return string[] the actual responses we are counting tries at.
     */
    protected function get_responses() {
        return array_keys($this->actualresponses);
    }

    /**
     * Get the data structure used to count the responses that match an actual response within this class of responses.
     *
     * @param string $response
     * @return analysis_for_actual_response the instance for keeping count of tries for $response.
     */
    protected function get_response($response) {
        return $this->actualresponses[$response];
    }
}

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
 * Represents an actual part of the response that has been classified in a class of responses for this sub part of the question.
 *
 * A question and it's response is represented as having one or more sub parts where the response to each sub-part might fall
 * into one of one or more classes.
 *
 * No response is one possible class of response to a question.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analysis_for_class {

    /**
     * @var string
     */
    protected $responseclassid;

    /**
     * @var string
     */
    protected $modelresponse;

    /** @var string the (partial) credit awarded for this responses. */
    protected $fraction;

    /**
     *
     * @var analysis_for_actual_response[] key is the actual response represented as a string as it will be displayed in report.
     */
    protected $actualresponses = array();

    /**
     * Constructor, just an easy way to set the fields.
     * @param \question_possible_response $possibleresponse
     * @param string                      $responseclassid
     */
    public function __construct($possibleresponse, $responseclassid) {
        $this->modelresponse = $possibleresponse->responseclass;
        $this->fraction = $possibleresponse->fraction;
        $this->responseclassid = $responseclassid;
    }

    /**
     * @param string $actualresponse
     * @param float|null $fraction
     */
    public function count_response($actualresponse, $fraction) {
        if (!isset($this->actualresponses[$actualresponse])) {
            if ($fraction === null) {
                $fraction = $this->fraction;
            }
            $this->actualresponses[$actualresponse] = new analysis_for_actual_response($actualresponse, $fraction);
        }
        $this->actualresponses[$actualresponse]->increment_count();
    }

    /**
     * @param \qubaid_condition $qubaids
     * @param int               $questionid the question id
     * @param string            $subpartid
     */
    public function cache($qubaids, $questionid, $subpartid) {
        foreach ($this->actualresponses as $response => $actualresponse) {
            $actualresponse->cache($qubaids, $questionid, $subpartid, $this->responseclassid, $response);
        }
    }

    public function add_response_and_count($response, $fraction, $count) {
        $this->actualresponses[$response] = new analysis_for_actual_response($response, $fraction, $count);
    }

    /**
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        if (count($this->actualresponses) > 1) {
            return true;
        } else if (count($this->actualresponses) == 1) {
            $onlyactualresponse = reset($this->actualresponses);
            return (string)$onlyactualresponse != $this->modelresponse;
        }
        return false;
    }

    /**
     * @return object[]
     */
    public function data_for_question_response_table($responseclasscolumn, $partid) {
        $return = array();
        if (empty($this->actualresponses)) {
            $rowdata = new \stdClass();
            $rowdata->part = $partid;
            $rowdata->responseclass = $this->modelresponse;
            if (!$responseclasscolumn) {
                $rowdata->response = $this->modelresponse;
            } else {
                $rowdata->response = '';
            }
            $rowdata->fraction = $this->fraction;
            $rowdata->count = 0;
            $return[] = $rowdata;
        } else {
            foreach ($this->actualresponses as $actualresponse) {
                $return[] = $actualresponse->data_for_question_response_table($partid, $this->modelresponse);
            }
        }
        return $return;
    }
}

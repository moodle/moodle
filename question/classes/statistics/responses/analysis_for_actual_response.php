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


class analysis_for_actual_response {
    /**
     * @var int count of this response
     */
    protected $count;

    /**
     * @var float grade for this response, normally between 0 and 1.
     */
    protected $fraction;

    /**
     * @var string the response as it will be displayed in report.
     */
    protected $response;

    /**
     * @param string $response
     * @param float  $fraction
     * @param int    $count     defaults to zero, this param used when loading from db.
     */
    public function __construct($response, $fraction, $count = 0) {
        $this->response = $response;
        $this->fraction = $fraction;
        $this->count = $count;
    }

    /**
     * Used to count the occurrences of response sub parts.
     */
    public function increment_count() {
        $this->count++;
    }


    /**
     * @param \qubaid_condition $qubaids
     * @param int               $questionid the question id
     * @param string            $subpartid
     * @param string            $responseclassid
     */
    public function cache($qubaids, $questionid, $subpartid, $responseclassid) {
        global $DB;
        $row = new \stdClass();
        $row->hashcode = $qubaids->get_hash_code();
        $row->questionid = $questionid;
        $row->subqid = $subpartid;
        if ($responseclassid === '') {
            $row->aid = null;
        } else {
            $row->aid = $responseclassid;
        }
        $row->response = $this->response;
        $row->rcount = $this->count;
        $row->credit = $this->fraction;
        $row->timemodified = time();
        $DB->insert_record('question_response_analysis', $row, false);
    }

    public function response_matches($response) {
        return $response == $this->response;
    }

    /**
     * @param string $partid
     * @param string $modelresponse
     * @return object
     */
    public function data_for_question_response_table($partid, $modelresponse) {
        $rowdata = new \stdClass();
        $rowdata->part = $partid;
        $rowdata->responseclass = $modelresponse;
        $rowdata->response = $this->response;
        $rowdata->fraction = $this->fraction;
        $rowdata->count = $this->count;
        return $rowdata;
    }
}

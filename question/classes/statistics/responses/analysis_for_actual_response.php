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
 * The leafs of the analysis data structure.
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
class analysis_for_actual_response {
    /**
     * @var int[] count per try for this response.
     */
    protected $trycount = array();

    /**
     * @var int total count of tries with this response.
     */
    protected $totalcount = 0;

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
     */
    public function __construct($response, $fraction) {
        $this->response = $response;
        $this->fraction = $fraction;
    }

    /**
     * Used to count the occurrences of response sub parts.
     *
     * @param int $try the try number, or 0 if only keeping one count, not a count for each try.
     */
    public function increment_count($try = 0) {
        $this->totalcount++;
        if ($try != 0) {
            if ($try > analyser::MAX_TRY_COUNTED) {
                $try = analyser::MAX_TRY_COUNTED;
            }
            if (!isset($this->trycount[$try])) {
                $this->trycount[$try] = 0;
            }
            $this->trycount[$try]++;
        }

    }

    /**
     * Used to set the count of occurrences of response sub parts, when loading count from cache.
     *
     * @param int $try the try number, or 0 if only keeping one count, not a count for each try.
     * @param int $count
     */
    public function set_count($try, $count) {
        $this->totalcount = $this->totalcount + $count;
        $this->trycount[$try] = $count;
    }

    /**
     * Cache analysis for class.
     *
     * @param \qubaid_condition $qubaids    which question usages have been analysed.
     * @param string            $whichtries which tries have been analysed?
     * @param int               $questionid which question.
     * @param int               $variantno  which variant.
     * @param string            $subpartid which sub part is this actual response in?
     * @param string            $responseclassid which response class is this actual response in?
     * @param int|null          $calculationtime time when the analysis was done. (Defaults to time()).
     */
    public function cache($qubaids, $whichtries, $questionid, $variantno, $subpartid, $responseclassid, $calculationtime = null) {
        global $DB;
        $row = new \stdClass();
        $row->hashcode = $qubaids->get_hash_code();
        $row->whichtries = $whichtries;
        $row->questionid = $questionid;
        $row->variant = $variantno;
        $row->subqid = $subpartid;
        if ($responseclassid === '') {
            $row->aid = null;
        } else {
            $row->aid = $responseclassid;
        }
        $row->response = $this->response;
        $row->credit = $this->fraction;
        $row->timemodified = $calculationtime ? $calculationtime : time();
        $analysisid = $DB->insert_record('question_response_analysis', $row);
        if ($whichtries === \question_attempt::ALL_TRIES) {
            foreach ($this->trycount as $try => $count) {
                $countrow = new \stdClass();
                $countrow->try = $try;
                $countrow->rcount = $count;
                $countrow->analysisid = $analysisid;
                $DB->insert_record('question_response_count', $countrow, false);
            }
        } else {
            $countrow = new \stdClass();
            $countrow->try = 0;
            $countrow->rcount = $this->totalcount;
            $countrow->analysisid = $analysisid;
            $DB->insert_record('question_response_count', $countrow, false);
        }
    }

    /**
     * Returns an object with a property for each column of the question response analysis table.
     *
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
        $rowdata->totalcount = $this->totalcount;
        $rowdata->trycount = $this->trycount;
        return $rowdata;
    }

    /**
     * What is the highest try number that this response has been seen?
     *
     * @return int try number
     */
    public function get_maximum_tries() {
        return max(array_keys($this->trycount));
    }
}

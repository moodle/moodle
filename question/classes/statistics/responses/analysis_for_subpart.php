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
 *
 * Data structure to count responses for each of the sub parts of a question.
 *
 * @package    core_question
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;

/**
 * Representing the analysis of each of the sub parts of each variant of the question.
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
class analysis_for_subpart {

    /**
     * @var analysis_for_class[]
     */
    protected $responseclasses;

    /**
     * Takes an array of possible_responses as returned from {@link \question_type::get_possible_responses()}.
     *
     * @param \question_possible_response[] $responseclasses as returned from {@link \question_type::get_possible_responses()}.
     */
    public function __construct(?array $responseclasses = null) {
        if (is_array($responseclasses)) {
            foreach ($responseclasses as $responseclassid => $responseclass) {
                $this->responseclasses[$responseclassid] = new analysis_for_class($responseclass, $responseclassid);
            }
        } else {
            $this->responseclasses = [];
        }
    }

    /**
     * Unique ids for response classes.
     *
     * @return string[]
     */
    public function get_response_class_ids() {
        return array_keys($this->responseclasses);
    }

    /**
     * Get the instance of the class handling the analysis of $classid for this sub part.
     *
     * @param string $classid id for response class.
     * @return analysis_for_class
     */
    public function get_response_class($classid) {
        if (!isset($this->responseclasses[$classid])) {
            debugging('Unexpected class id ' . $classid . ' encountered.');
            $this->responseclasses[$classid] = new analysis_for_class('[Unknown]', $classid);
        }
        return $this->responseclasses[$classid];

    }

    /**
     * Whether there is more than one response class for responses in this question sub part?
     *
     * @return bool Are there?
     */
    public function has_multiple_response_classes() {
        return count($this->get_response_class_ids()) > 1;
    }

    /**
     * Count a part of a response.
     *
     * @param \question_classified_response $subpart
     * @param int $try the try number or zero if not keeping track of try number
     */
    public function count_response($subpart, $try = 0) {
        $responseanalysisforclass = $this->get_response_class($subpart->responseclassid);
        $responseanalysisforclass->count_response($subpart->response, $subpart->fraction, $try);
    }

    /**
     * Cache analysis for sub part.
     *
     * @param \qubaid_condition $qubaids    which question usages have been analysed.
     * @param string            $whichtries which tries have been analysed?
     * @param int               $questionid which question.
     * @param int               $variantno  which variant.
     * @param string            $subpartid  which sub part.
     * @param int|null          $calculationtime time when the analysis was done. (Defaults to time()).
     */
    public function cache($qubaids, $whichtries, $questionid, $variantno, $subpartid, $calculationtime = null) {
        foreach ($this->get_response_class_ids() as $responseclassid) {
            $analysisforclass = $this->get_response_class($responseclassid);
            $analysisforclass->cache($qubaids, $whichtries, $questionid, $variantno, $subpartid, $calculationtime);
        }
    }

    /**
     * Has actual responses different to the model response for this class?
     *
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        foreach ($this->get_response_class_ids() as $responseclassid) {
            if ($this->get_response_class($responseclassid)->has_actual_responses()) {
                return true;
            }
        }
        return false;
    }

    /**
     * What is the highest try number for this sub part?
     *
     * @return int max tries
     */
    public function get_maximum_tries() {
        $max = 1;
        foreach ($this->get_response_class_ids() as $responseclassid) {
            $max = max($max, $this->get_response_class($responseclassid)->get_maximum_tries());
        }
        return $max;
    }
}

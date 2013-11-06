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
 * 'Classes' to classify the sub parts of a question response into.
 *
 * @package    core_question
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;


class analysis_for_subpart {

    /**
     * Takes an array of possible_responses - ({@link \question_possible_response} objects).
     * Or takes an array of {@link \question_possible_response} objects.
     *
     * @param \question_possible_response[] $responseclasses
     */
    public function __construct(array $responseclasses = null) {
        if (is_array($responseclasses)) {
            foreach ($responseclasses as $responseclassid => $responseclass) {
                $this->responseclasses[$responseclassid] = new analysis_for_class($responseclass, $responseclassid);
            }
        }
    }

    /**
     *
     * @var analysis_for_class[]
     */
    protected $responseclasses;

    /**
     * Unique ids for response classes.
     *
     * @return string[]
     */
    public function get_response_class_ids() {
        return array_keys($this->responseclasses);
    }

    /**
     * @param string $classid id for response class.
     * @return analysis_for_class
     */
    public function get_response_class($classid) {
        return $this->responseclasses[$classid];
    }

    public function has_multiple_response_classes() {
        return count($this->responseclasses) > 1;
    }

    /**
     * @param \question_classified_response $subpart
     */
    public function count_response($subpart) {
        $this->responseclasses[$subpart->responseclassid]->count_response($subpart->response, $subpart->fraction);
    }

    /**
     * @param \qubaid_condition $qubaids
     * @param int               $questionid the question id
     * @param string            $subpartid
     */
    public function cache($qubaids, $questionid, $subpartid) {
        foreach ($this->responseclasses as $responseclassid => $responseclass) {
            $responseclass->cache($qubaids, $questionid, $subpartid, $responseclassid);
        }
    }

    /**
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        foreach ($this->responseclasses as $responseclass) {
            if ($responseclass->has_actual_responses()) {
                return true;
            }
        }
        return false;
    }
}

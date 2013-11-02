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
 * Analysis for possible responses for parts of a question. It is up to a question type designer to decide on how many parts their
 * question has. A sub part might represent a sub question embedded in the question for example in a matching question there are
 * several sub parts. A numeric question with a unit might be divided into two sub parts for the purposes of response analysis
 * or the question type designer might decide to treat the answer, both the numeric and unit part,
 * as a whole for the purposes of response analysis.
 *
 * Responses can be further divided into 'classes' in which they are classified. One or more of these 'classes' are contained in
 * the responses
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analysis_for_question {

    /**
     * Takes either a two index array as a parameter with keys subpartid and classid and values possible_response.
     * Or takes an array of {@link responses_for_classes} objects.
     *
     * @param $subparts[string]array[]\question_possible_response $array
     */
    public function __construct(array $subparts = null) {
        if (!is_null($subparts)) {
            foreach ($subparts as $subpartid => $classes) {
                $this->subparts[$subpartid] = new analysis_for_subpart($classes);
            }
        }
    }

    /**
     * @var analysis_for_subpart[]
     */
    protected $subparts;

    /**
     * Unique ids for sub parts.
     *
     * @return string[]
     */
    public function get_subpart_ids() {
        return array_keys($this->subparts);
    }

    /**
     * @param string $subpartid id for sub part.
     * @return analysis_for_subpart
     */
    public function get_subpart($subpartid) {
        return $this->subparts[$subpartid];
    }

    /**
     * Used to work out what kind of table is needed to display stats.
     *
     * @return bool whether this question has (a subpart with) more than one response class.
     */
    public function has_multiple_response_classes() {
        foreach ($this->subparts as $subpart) {
            if ($subpart->has_multiple_response_classes()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Used to work out what kind of table is needed to display stats.
     *
     * @return bool whether this analysis has more than one subpart.
     */
    public function has_subparts() {
        return count($this->subparts) > 1;
    }

    /**
     * Takes an array of {@link \question_classified_response} and adds counts of the responses to the sub parts and classes.
     *
     * @var \question_classified_response[] $responseparts keys are sub-part id.
     */
    public function count_response_parts($responseparts) {
        foreach ($responseparts as $subpartid => $responsepart) {
            $this->get_subpart($subpartid)->count_response($responsepart);
        }
    }

    /**
     * @param \qubaid_condition $qubaids
     * @param int               $questionid the question id
     */
    public function cache($qubaids, $questionid) {
        foreach ($this->subparts as $subpartid => $subpart) {
            $subpart->cache($qubaids, $questionid, $subpartid);
        }
    }

    /**
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        foreach ($this->subparts as $subpartid => $subpart) {
            if ($subpart->has_actual_responses()) {
                return true;
            }
        }
        return false;
    }

}

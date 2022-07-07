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
 * This file contains a class to analyse all the responses for multiple tries at a particular question.
 *
 * @package    core_question
 * @copyright  2014 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\responses;

/**
 * Analysis for possible responses for parts of a question with multiple submitted responses.
 *
 * If the analysis was for a single try it would be handled by {@link \core_question\statistics\responses\analysis_for_question}.
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
class analysis_for_question_all_tries extends analysis_for_question{
    /**
     * Constructor.
     *
     * @param int      $variantno               variant number
     * @param \array[] $responsepartsforeachtry for question with multiple tries we expect an array with first index being try no
     *                                          then second index is subpartid and values are \question_classified_response
     */
    public function count_response_parts($variantno, $responsepartsforeachtry) {
        foreach ($responsepartsforeachtry as $try => $responseparts) {
            foreach ($responseparts as $subpartid => $responsepart) {
                $this->get_analysis_for_subpart($variantno, $subpartid)->count_response($responsepart, $try);
            }
        }
    }

    public function has_multiple_tries_data() {
        return true;
    }

    /**
     * What is the highest number of tries at this question?
     *
     * @return int try number
     */
    public function get_maximum_tries() {
        $max = 1;
        foreach ($this->get_variant_nos() as $variantno) {
            foreach ($this->get_subpart_ids($variantno) as $subpartid) {
                $max = max($max, $this->get_analysis_for_subpart($variantno, $subpartid)->get_maximum_tries());
            }
        }
        return $max;
    }

}

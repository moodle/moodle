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
 * question has. See {@link \question_type::get_possible_responses()} and sub classes where the sub parts and response classes are
 * defined.
 *
 * A sub part might represent a sub question embedded in the question for example in a matching question there are
 * several sub parts. A numeric question with a unit might be divided into two sub parts for the purposes of response analysis
 * or the question type designer might decide to treat the answer, both the numeric and unit part,
 * as a whole for the purposes of response analysis.
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
class analysis_for_question {

    /**
     * Constructor method.
     *
     * @param array[] Two index array, first index is unique string for each sub question part,
     *                    the second string index is the 'class' that sub-question part can be classified into.
     *                    Value in array is instance of {@link \question_possible_response}
     *                    This is the return value from {@link \question_type::get_possible_responses()}
     *                    see that method for fuller documentation.
     */
    public function __construct(?array $possiblereponses = null) {
        if ($possiblereponses !== null) {
            $this->possibleresponses = $possiblereponses;
        }
    }

    /**
     * @var array[] See description above in constructor method.
     */
    protected $possibleresponses = array();

    /**
     * A multidimensional array whose first index is variant no and second index is subpart id, array contents are of type
     * {@link analysis_for_subpart}.
     *
     * @var array[]
     */
    protected $subparts = array();

    /**
     * Initialise data structure for response analysis of one variant.
     *
     * @param int $variantno
     */
    protected function initialise_stats_for_variant($variantno) {
        $this->subparts[$variantno] = array();
        foreach ($this->possibleresponses as $subpartid => $classes) {
            $this->subparts[$variantno][$subpartid] = new analysis_for_subpart($classes);
        }
    }

    /**
     * Variant nos found in this question's attempt data.
     *
     * @return int[]
     */
    public function get_variant_nos() {
        return array_keys($this->subparts);
    }

    /**
     * Unique ids for sub parts.
     *
     * @param int $variantno
     * @return string[]
     */
    public function get_subpart_ids($variantno) {
        return array_keys($this->subparts[$variantno]);
    }

    /**
     * Get the response counts etc. for variant $variantno, question sub part $subpartid.
     *
     * Or if there is no recorded analysis yet then initialise the data structure for that part of the analysis and return the
     * initialised analysis objects.
     *
     * @param int    $variantno
     * @param string $subpartid id for sub part.
     * @return analysis_for_subpart
     */
    public function get_analysis_for_subpart($variantno, $subpartid) {
        if (!isset($this->subparts[$variantno])) {
            $this->initialise_stats_for_variant($variantno);
        }
        if (!isset($this->subparts[$variantno][$subpartid])) {
            debugging('Unexpected sub-part id ' . $subpartid .
                    ' encountered.');
            $this->subparts[$variantno][$subpartid] = new analysis_for_subpart();
        }
        return $this->subparts[$variantno][$subpartid];
    }

    /**
     * Used to work out what kind of table is needed to display stats.
     *
     * @return bool whether this question has (a subpart with) more than one response class.
     */
    public function has_multiple_response_classes() {
        foreach ($this->get_variant_nos() as $variantno) {
            foreach ($this->get_subpart_ids($variantno) as $subpartid) {
                if ($this->get_analysis_for_subpart($variantno, $subpartid)->has_multiple_response_classes()) {
                    return true;
                }
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
        foreach ($this->get_variant_nos() as $variantno) {
            if (count($this->get_subpart_ids($variantno)) > 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool Does this response analysis include counts for responses for multiple tries of the question?
     */
    public function has_multiple_tries_data() {
        return false;
    }

    /**
     * What is the highest number of tries at this question?
     *
     * @return int always 1 as this class is for analysing only one try.
     */
    public function get_maximum_tries() {
        return 1;
    }


    /**
     * Takes an array of {@link \question_classified_response} and adds counts of the responses to the sub parts and classes.
     *
     * @param int                             $variantno
     * @param \question_classified_response[] $responseparts keys are sub-part id.
     */
    public function count_response_parts($variantno, $responseparts) {
        foreach ($responseparts as $subpartid => $responsepart) {
            $this->get_analysis_for_subpart($variantno, $subpartid)->count_response($responsepart);
        }
    }

    /**
     * Save the analysis to the DB, first cleaning up any old ones.
     *
     * @param \qubaid_condition $qubaids    which question usages have been analysed.
     * @param string            $whichtries which tries have been analysed?
     * @param int               $questionid which question.
     * @param int|null          $calculationtime time when the analysis was done. (Defaults to time()).
     */
    public function cache($qubaids, $whichtries, $questionid, $calculationtime = null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $analysisids = $DB->get_fieldset(
            'question_response_analysis',
            'id',
            [
                'hashcode' => $qubaids->get_hash_code(),
                'whichtries' => $whichtries,
                'questionid' => $questionid,
            ]
        );
        if (!empty($analysisids)) {
            [$insql, $params] = $DB->get_in_or_equal($analysisids);
            $DB->delete_records_select('question_response_count', 'analysisid ' . $insql, $params);
            $DB->delete_records_select('question_response_analysis', 'id ' . $insql, $params);
        }

        foreach ($this->get_variant_nos() as $variantno) {
            foreach ($this->get_subpart_ids($variantno) as $subpartid) {
                $analysisforsubpart = $this->get_analysis_for_subpart($variantno, $subpartid);
                $analysisforsubpart->cache($qubaids, $whichtries, $questionid, $variantno, $subpartid, $calculationtime);
            }
        }

        $transaction->allow_commit();
    }

    /**
     * @return bool whether this analysis has a response class with more than one
     *      different actual response, or if the actual response is different from
     *      the model response.
     */
    public function has_actual_responses() {
        foreach ($this->get_variant_nos() as $variantno) {
            foreach ($this->get_subpart_ids($variantno) as $subpartid) {
                if ($this->get_analysis_for_subpart($variantno, $subpartid)->has_actual_responses()) {
                    return true;
                }
            }
        }
        return false;
    }

}

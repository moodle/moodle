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
 * @package    qtype
 * @subpackage calculated
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Calculated question type conversion handler
 */
class moodle1_qtype_calculated_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'CALCULATED',
            'CALCULATED/NUMERICAL_UNITS/NUMERICAL_UNIT',
            'CALCULATED/DATASET_DEFINITIONS/DATASET_DEFINITION',
            'CALCULATED/DATASET_DEFINITIONS/DATASET_DEFINITION/DATASET_ITEMS/DATASET_ITEM'
        );
    }

    /**
     * Appends the calculated specific information to the question
     */
    public function process_question(array $data, array $raw) {

        // convert and write the answers first
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // convert and write the numerical units and numerical options
        if (isset($data['calculated'][0]['numerical_units'])) {
            $numericalunits   = $data['calculated'][0]['numerical_units'];
            $numericaloptions = $this->get_default_numerical_options($data['oldquestiontextformat']);
        } else {
            $numericalunits   = array();
            $numericaloptions = array();
        }
        $this->write_numerical_units($numericalunits);
        $this->write_numerical_options($numericaloptions);

        // write dataset_definitions
        if (isset($data['calculated'][0]['dataset_definitions']['dataset_definition'])) {
            $datasetdefinitions = $data['calculated'][0]['dataset_definitions']['dataset_definition'];
        } else {
            $datasetdefinitions = array();
        }
        $this->write_dataset_definitions($datasetdefinitions);

        // write calculated_records
        $this->xmlwriter->begin_tag('calculated_records');
        foreach ($data['calculated'] as $calculatedrecord) {
            $record = array(
                'id'                  => $this->converter->get_nextid(),
                'answer'              => $calculatedrecord['answer'],
                'tolerance'           => $calculatedrecord['tolerance'],
                'tolerancetype'       => $calculatedrecord['tolerancetype'],
                'correctanswerlength' => $calculatedrecord['correctanswerlength'],
                'correctanswerformat' => $calculatedrecord['correctanswerformat']
            );
            $this->write_xml('calculated_record', $record, array('/calculated_record/id'));
        }
        $this->xmlwriter->end_tag('calculated_records');

        // write calculated_options
        $options = array(
            'calculate_option' => array(
                'id'                             => $this->converter->get_nextid(),
                'synchronize'                    => 0,
                'single'                         => 0,
                'shuffleanswers'                 => 0,
                'correctfeedback'                => null,
                'correctfeedbackformat'          => FORMAT_HTML,
                'partiallycorrectfeedback'       => null,
                'partiallycorrectfeedbackformat' => FORMAT_HTML,
                'incorrectfeedback'              => null,
                'incorrectfeedbackformat'        => FORMAT_HTML,
                'answernumbering'                => 'abc'
            )
        );
        $this->write_xml('calculated_options', $options, array('/calculated_options/calculate_option/id'));
    }
}

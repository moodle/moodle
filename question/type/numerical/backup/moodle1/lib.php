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
 * @subpackage numerical
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Multichoice question type conversion handler
 */
class moodle1_qtype_numerical_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'NUMERICAL',
            'NUMERICAL/NUMERICAL_UNITS/NUMERICAL_UNIT',
        );
    }

    /**
     * Appends the numerical specific information to the question
     */
    public function process_question(array $data, array $raw) {

        // convert and write the answers first
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // convert and write the numerical units and numerical options
        if (isset($data['numerical'][0]['numerical_units'])) {
            $numericalunits = $data['numerical'][0]['numerical_units'];
        } else {
            $numericalunits = array();
        }
        $numericaloptions = $this->get_default_numerical_options(
                $data['oldquestiontextformat'], $numericalunits);

        $this->write_numerical_units($numericalunits);
        $this->write_numerical_options($numericaloptions);

        // and finally numerical_records
        $this->xmlwriter->begin_tag('numerical_records');
        foreach ($data['numerical'] as $numericalrecord) {
            // we do not use write_xml() here because $numericalrecords contains more than we want
            $this->xmlwriter->begin_tag('numerical_record', array('id' => $this->converter->get_nextid()));
            $this->xmlwriter->full_tag('answer', $numericalrecord['answer']);
            $this->xmlwriter->full_tag('tolerance', $numericalrecord['tolerance']);
            $this->xmlwriter->end_tag('numerical_record');
        }
        $this->xmlwriter->end_tag('numerical_records');
    }
}

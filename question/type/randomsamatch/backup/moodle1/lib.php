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
 * Serve question type files
 *
 * @package    qtype_randomsamatch
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Random shortanswer matching question type conversion handler.
 *
 * @copyright  2013 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class moodle1_qtype_randomsamatch_handler extends moodle1_qtype_handler {

    /**
     * Returns the list of paths within one <QUESTION> that this qtype needs to have included
     * in the grouped question structure
     *
     * @return array of strings
     */
    public function get_question_subpaths() {
        return array(
            'RANDOMSAMATCH',
        );
    }

    /**
     * Appends the randomsamatch specific information to the question.
     *
     * @param array $data grouped question data
     * @param array $raw grouped raw QUESTION data
     */
    public function process_question(array $data, array $raw) {

        // Convert match options.
        if (isset($data['randomsamatch'])) {
            $randomsamatch = $data['randomsamatch'][0];
        } else {
            $randomsamatch = array('choose' => 4);
        }
        $randomsamatch['id'] = $this->converter->get_nextid();
        $randomsamatch['subcats'] = 1;
        $randomsamatch['correctfeedback'] = '';
        $randomsamatch['correctfeedbackformat'] = FORMAT_HTML;
        $randomsamatch['partiallycorrectfeedback'] = '';
        $randomsamatch['partiallycorrectfeedbackformat'] = FORMAT_HTML;
        $randomsamatch['incorrectfeedback'] = '';
        $randomsamatch['incorrectfeedbackformat'] = FORMAT_HTML;
        $this->write_xml('randomsamatch', $randomsamatch, array('/randomsamatch/id'));
    }
}

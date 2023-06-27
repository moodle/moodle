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
 * @subpackage multichoice
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Multichoice question type conversion handler
 */
class moodle1_qtype_multichoice_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'MULTICHOICE',
        );
    }

    /**
     * Appends the multichoice specific information to the question
     */
    public function process_question(array $data, array $raw) {

        // Convert and write the answers first.
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // Convert and write the multichoice.
        if (!isset($data['multichoice'])) {
            // This should never happen, but it can do if the 1.9 site contained
            // corrupt data.
            $data['multichoice'] = array(array(
                'single'                         => 1,
                'shuffleanswers'                 => 1,
                'correctfeedback'                => '',
                'correctfeedbackformat'          => FORMAT_HTML,
                'partiallycorrectfeedback'       => '',
                'partiallycorrectfeedbackformat' => FORMAT_HTML,
                'incorrectfeedback'              => '',
                'incorrectfeedbackformat'        => FORMAT_HTML,
                'answernumbering'                => 'abc',
                'showstandardinstruction'        => 0
            ));
        }
        $this->write_multichoice($data['multichoice'], $data['oldquestiontextformat'], $data['id']);
    }

    /**
     * Converts the multichoice info and writes it into the question.xml
     *
     * @param array $multichoices the grouped structure
     * @param int $oldquestiontextformat - {@see moodle1_question_bank_handler::process_question()}
     * @param int $questionid question id
     */
    protected function write_multichoice(array $multichoices, $oldquestiontextformat, $questionid) {
        global $CFG;

        // The grouped array is supposed to have just one element - let us use foreach anyway
        // just to be sure we do not loose anything.
        foreach ($multichoices as $multichoice) {
            // Append an artificial 'id' attribute (is not included in moodle.xml).
            $multichoice['id'] = $this->converter->get_nextid();

            // Replay the upgrade step 2009021801.
            $multichoice['correctfeedbackformat']               = 0;
            $multichoice['partiallycorrectfeedbackformat']      = 0;
            $multichoice['incorrectfeedbackformat']             = 0;

            if ($CFG->texteditors !== 'textarea' and $oldquestiontextformat == FORMAT_MOODLE) {
                $multichoice['correctfeedback']                 = text_to_html($multichoice['correctfeedback'], false, false, true);
                $multichoice['correctfeedbackformat']           = FORMAT_HTML;
                $multichoice['partiallycorrectfeedback']        = text_to_html($multichoice['partiallycorrectfeedback'], false, false, true);
                $multichoice['partiallycorrectfeedbackformat']  = FORMAT_HTML;
                $multichoice['incorrectfeedback']               = text_to_html($multichoice['incorrectfeedback'], false, false, true);
                $multichoice['incorrectfeedbackformat']         = FORMAT_HTML;
            } else {
                $multichoice['correctfeedbackformat']           = $oldquestiontextformat;
                $multichoice['partiallycorrectfeedbackformat']  = $oldquestiontextformat;
                $multichoice['incorrectfeedbackformat']         = $oldquestiontextformat;
            }

            $multichoice['correctfeedback'] = $this->migrate_files(
                    $multichoice['correctfeedback'], 'question', 'correctfeedback', $questionid);
            $multichoice['partiallycorrectfeedback'] = $this->migrate_files(
                    $multichoice['partiallycorrectfeedback'], 'question', 'partiallycorrectfeedback', $questionid);
            $multichoice['incorrectfeedback'] = $this->migrate_files(
                    $multichoice['incorrectfeedback'], 'question', 'incorrectfeedback', $questionid);

            $this->write_xml('multichoice', $multichoice, array('/multichoice/id'));
        }
    }
}

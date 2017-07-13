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
 * Defines the editing form for the 'missingtype' question type.
 *
 * @package    qtype
 * @subpackage missingtype
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This question renderer class is used when the actual question type of this
 * question cannot be found.
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_missingtype_edit_form extends question_edit_form {
    public function __construct($submiturl, $question, $category, $contexts, $formeditable = true) {
        parent::__construct($submiturl, $question, $category, $contexts, false);
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) {
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_missingtype', '{no}'),
                question_bank::fraction_options_full());
    }

    public function set_data($question) {
        if (isset($question->options) && is_array($question->options->answers)) {
            $answers = $question->options->answers;
            $default_values = array();
            $key = 0;
            foreach ($answers as $answer) {
                $default_values['answer['.$key.']'] = $answer->answer;
                $default_values['fraction['.$key.']'] = $answer->fraction;
                $default_values['feedback['.$key.']'] = $answer->feedback;
                $key++;
            }
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    public function qtype() {
        return 'missingtype';
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $errors['name'] = get_string('cannotchangeamissingqtype', 'qtype_missingtype');
        return $errors;
    }
}

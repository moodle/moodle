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
 * Defines the editing form for the numerical question type.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * numerical editing form definition.
 *
 * @copyright  2007 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_edit_numerical_form extends question_edit_form {

    protected function get_per_answer_fields($mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);

        $tolerance =& $mform->createElement('text', 'tolerance', get_string('acceptederror', 'qtype_numerical'));
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        array_splice($repeated, 3, 0, array($tolerance));
        $repeated[1]->setSize(10);

        return $repeated;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {

//------------------------------------------------------------------------------------------
        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_numerical', '{no}'),
                $creategrades->gradeoptions);
//------------------------------------------------------------------------------------------
        question_bank::get_qtype('numerical')->add_units_options($mform, $this);
        question_bank::get_qtype('numerical')->add_units_elements($mform, $this);
    }

    protected function data_preprocessing($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $draftid = file_get_submitted_draft_itemid('feedback['.$key.']');
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['tolerance['.$key.']'] = $answer->tolerance;
                    $default_values['feedback['.$key.']'] = array();
                    $default_values['feedback['.$key.']']['format'] = $answer->feedbackformat;
                    $default_values['feedback['.$key.']']['text'] = file_prepare_draft_area(
                        $draftid,       // draftid
                        $this->context->id,    // context
                        'question',   // component
                        'answerfeedback',             // filarea
                        !empty($answer->id)?(int)$answer->id:null, // itemid
                        $this->fileoptions,    // options
                        $answer->feedback      // text
                    );
                    $default_values['feedback['.$key.']']['itemid'] = $draftid;
                    $key++;
                }
            }
            question_bank::get_qtype('numerical')->set_numerical_unit_data($this, $question, $default_values);

            $question = (object)((array)$question + $default_values);
        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check the answers.
        $answercount = 0;
        $maxgrade = false;
        $answers = $data['answer'];
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer != '') {
                $answercount++;
                if (!(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                    $errors["answer[$key]"] = get_string('answermustbenumberorstar', 'qtype_numerical');
                }
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 || !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbenumberorstar', 'qtype_numerical');
                $answercount++;
            }
        }
        if ($answercount == 0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_numerical');
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        question_bank::get_qtype('numerical')->validate_numerical_options($data, $errors);

        return $errors;
    }

    public function qtype() {
        return 'numerical';
    }
}

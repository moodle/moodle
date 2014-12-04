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
 * Defines the editing form for the multiple choice question type.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Ordering editing form definition
 * (originally based on mutiple choice form)
 *
 * @copyright  2007 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_edit_form extends question_edit_form {

    const NUM_ANS_START = 10;
    const NUM_ANS_ADD   = 5;

    public function qtype() {
        return 'ordering';
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    public function definition_inner($mform) {
        $options = array(
            0 => get_string('ordering_exactorder',    'qtype_ordering'), // = all ?
            1 => get_string('ordering_relativeorder', 'qtype_ordering'), // = random subset
            2 => get_string('ordering_contiguous',    'qtype_ordering')  // = contiguous subset
        );
        $mform->addElement('select', 'logical', get_string('ordering_logicalpossibilities', 'qtype_ordering'), $options);
        $mform->setDefault('logical', 0);

        $options = array(0 => 'All');
        for ($i=3; $i <= 20; $i++) {
            $options[] = $i;
        }
        $mform->addElement('select', 'studentsee', get_string('ordering_itemsforstudent', 'qtype_ordering'), $options);
        $mform->setDefault('studentsee', 0);

        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'choicehdr', get_string('ordering_choiceno', 'qtype_ordering', '{no}'));
        $repeated[] =& $mform->createElement('textarea', 'answer', get_string('ordering_answer', 'qtype_ordering'), 'rows="3" cols="50"');

        if (empty($this->question->options)){
            $countanswers = 0;
        } else {
            $countanswers = count($this->question->options->answers);
        }
        if (self::NUM_ANS_START > ($countanswers + self::NUM_ANS_ADD)) {
            $repeatsatstart = self::NUM_ANS_START;
        } else {
            $repeatsatstart = ($countanswers + self::NUM_ANS_ADD);
        }
        $repeatedoptions = array();
        $repeatedoptions['fraction']['default'] = 0;
        $mform->setType('answer', PARAM_NOTAGS);

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', self::NUM_ANS_ADD, get_string('ordering_addmoreanswers', 'qtype_ordering'));

        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'qtype_ordering'));

        $mform->addElement('htmleditor', 'correctfeedback', get_string('correctfeedback', 'qtype_ordering'));
        $mform->setType('correctfeedback', PARAM_RAW);

        $mform->addElement('htmleditor', 'partiallycorrectfeedback', get_string('partiallycorrectfeedback', 'qtype_ordering'));
        $mform->setType('partiallycorrectfeedback', PARAM_RAW);

        $mform->addElement('htmleditor', 'incorrectfeedback', get_string('incorrectfeedback', 'qtype_ordering'));
        $mform->setType('incorrectfeedback', PARAM_RAW);

    }

    public function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);
        //$question = $this->data_preprocessing_answers($question, true);
        //$question = $this->data_preprocessing_combined_feedback($question, true);
        //$question = $this->data_preprocessing_hints($question, true, true);


        if (!empty($question->options)){
	        $answers = $question->options->answers;
	        if (count($answers)) {
	            $key = 0;
	            foreach ($answers as $answerkey => $answer){
	                $default_values['answer['.$key.']'] = $answer->answer;
	                $default_values['fraction['.$key.']'] = $answerkey + 1;
	                //$default_values['feedback['.$key.']'] = $answer->feedback;
	                $key++;
	            }
	        }
	        $default_values['studentsee'] =  $question->options->studentsee;
	        $default_values['logical'] =  $question->options->logical;
	        $question = (object)((array)$question + $default_values);

        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = array();

        $answercount = 0;
        foreach ($data['answer'] as $answer){
            $answer = trim($answer);
            if ($answer || $answer==='0'){
                $answercount++;
            }
        }

        if ($answercount==0){
            $errors['answer[0]'] = get_string('ordering_notenoughanswers', 'qtype_ordering', 2);
            $errors['answer[1]'] = get_string('ordering_notenoughanswers', 'qtype_ordering', 2);
        } elseif ($answercount==1){
            $errors['answer[1]'] = get_string('ordering_notenoughanswers', 'qtype_ordering', 2);

        }

        return $errors;
    }
}

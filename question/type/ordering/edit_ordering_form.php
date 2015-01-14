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
            0 => get_string('exactorder',    'qtype_ordering'), // = all ?
            1 => get_string('relativeorder', 'qtype_ordering'), // = random subset
            2 => get_string('contiguous',    'qtype_ordering')  // = contiguous subset
        );
        $mform->addElement('select', 'logical', get_string('logicalpossibilities', 'qtype_ordering'), $options);
        $mform->setDefault('logical', 0);

        $options = array(0 => 'All');
        for ($i=3; $i <= 20; $i++) {
            $options[] = $i;
        }
        $mform->addElement('select', 'studentsee', get_string('itemsforstudent', 'qtype_ordering'), $options);
        $mform->setDefault('studentsee', 0);

        $elements = array();
        $elements[] =& $mform->createElement('header', 'choicehdr', get_string('choiceno', 'qtype_ordering', '{no}'));
        $elements[] =& $mform->createElement('textarea', 'answer', get_string('answer', 'qtype_ordering'), 'rows="3" cols="50"');

        if (empty($this->question->options)){
            $count = 0;
        } else {
            $count = count($this->question->options->answers);
        }
        $start = max(self::NUM_ANS_START, $count + self::NUM_ANS_ADD);

        $options = array('fraction' => array('default' => 0));
        $mform->setType('answer', PARAM_NOTAGS);

        $label = get_string('addmoreanswers', 'qtype_ordering');
        $this->repeat_elements($elements, $start, $options, 'noanswers', 'addanswers', self::NUM_ANS_ADD, $label);

        $this->add_combined_feedback_fields();
    }

    public function data_preprocessing($question) {

        $question = parent::data_preprocessing($question);
        //$question = $this->data_preprocessing_answers($question, true);
        $question = $this->data_preprocessing_combined_feedback($question);

        if (isset($question->options->answers)) {
            $i = 0;
            foreach ($question->options->answers as $answer) {
                if (trim($answer->answer)=='') {
                    continue; // skip empty answers
                }
                if ($i==0) {
                    $question->answer     = array();
                    $question->fraction   = array();
                    $question->logical    = $question->options->logical;
                    $question->studentsee = $question->options->studentsee;
                }
                $question->answer[$i]   = $answer->answer;
                $question->fraction[$i] = ($i + 1);
                $i++;
            }
        }

        return $question;
    }

    public function validation($data, $files) {
        $errors = array();

        $answercount = 0;
        foreach ($data['answer'] as $answer){
            if (trim($answer)=='') {
                continue; // skip empty answer
            }
            $answercount++;
        }

        switch ($answercount) {
            case 0: $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_ordering', 2);
            case 1: $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_ordering', 2);
        }

        return $errors;
    }
}

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
 * Editing form for the OU multiple response question type class.
 *
 * @package qtype_oumultiresponse
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Editing form for the oumultiresponse question type.
 *
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_edit_oumultiresponse_form extends question_edit_form {

    public function definition_inner($mform) {
        global $QTYPES;

        $mform->addElement('advcheckbox', 'shuffleanswers', get_string('shuffleanswers', 'qtype_multichoice'), null, null, array(0,1));
        $mform->addHelpButton('shuffleanswers', 'shuffleanswers', 'qtype_multichoice');
        $mform->setDefault('shuffleanswers', 1);

        $mform->addElement('select', 'answernumbering', get_string('answernumbering', 'qtype_multichoice'),
                qtype_multichoice::get_numbering_styles());
        $mform->setDefault('answernumbering', 'abc');

        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'),
                null, max(5, QUESTION_NUMANS_START));

        $this->add_combined_feedback_fields(true);

        $this->add_interactive_settings(true, true);
    }

    protected function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] = $mform->createElement('header', 'choicehdr', get_string('choiceno', 'qtype_multichoice', '{no}'));
        $repeated[] = $mform->createElement('text', 'answer', get_string('answer', 'question'), array('size' => 50));
        $repeated[] = $mform->createElement('checkbox', 'correctanswer', get_string('correctanswer', 'qtype_oumultiresponse'));
        $repeated[] = $mform->createElement('editor', 'feedback', get_string('feedback', 'quiz'),
                                array('rows' => 5), $this->editoroptions);

        // These are returned by arguments passed by reference.
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $answersoption = 'answers';

        return $repeated;
    }

    protected function get_hint_fields($withclearwrong = false, $withshownumpartscorrect = false) {
        list($repeated, $repeatedoptions) = parent::get_hint_fields($withclearwrong, $withshownumpartscorrect);
        $repeated[] = $this->_form->createElement('checkbox', 'hintshowchoicefeedback', '', get_string('showeachanswerfeedback', 'qtype_oumultiresponse'));
        return array($repeated, $repeatedoptions);
    }

    function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_answers($question, true);
        $question = $this->data_preprocessing_combined_feedback($question, true);
        $question = $this->data_preprocessing_hints($question, true, true);

        if (!empty($question->options->answers)) {
            $key = 0;
            foreach ($question->options->answers as $answer){
                $question->correctanswer[$key] = $answer->fraction > 0;
                $key++;
            }
        }

        if (!empty($question->hints)) {
            $key = 0;
            foreach ($question->hints as $hint) {
                $question->hintshowchoicefeedback[$key] = !empty($hint->options);
                $key += 1;
            }
        }

        if (!empty($question->options)) {
            $question->shuffleanswers =  $question->options->shuffleanswers;
            $question->answernumbering =  $question->options->answernumbering;
        }

        return $question;
    }

    public function validation($data, $files){
        $errors = parent::validation($data, $files);

        $answers = $data['answer'];
        $answercount = 0;
        $numberofcorrectanswers = 0;
        foreach ($answers as $key => $answer){
            $trimmedanswer = trim($answer);
            if (empty($trimmedanswer)){
                continue;
            }

            $answercount++;
            if (!empty($data['correctanswer'][$key])) {
                $numberofcorrectanswers++;
            }
        }

        // Perform sanity checks on number of correct answers
        if ($numberofcorrectanswers == 0) {
            $errors['answer[0]'] = get_string('notenoughcorrectanswers', 'qtype_oumultiresponse');
        }

        // Perform sanity checks on number of answers
        if ($answercount==0){
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
        } elseif ($answercount==1){
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
        }

        return $errors;
    }

    public function qtype() {
        return 'oumultiresponse';
    }
}

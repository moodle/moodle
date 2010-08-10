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

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the editing form for the shortanswer question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * shortanswer editing form definition.
 */
class question_edit_shortanswer_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        $menu = array(get_string('caseno', 'quiz'), get_string('caseyes', 'quiz'));
        $mform->addElement('select', 'usecase', get_string('casesensitive', 'quiz'), $menu);

        $mform->addElement('static', 'answersinstruct', get_string('correctanswers', 'quiz'), get_string('filloutoneanswer', 'quiz'));
        $mform->closeHeaderBefore('answersinstruct');

        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_shortanswer', '{no}'),
                $creategrades->gradeoptions);
    }

    function data_preprocessing($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            $answers_ids = array();
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $answers_ids[] = $answer->id;
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['feedback['.$key.']'] = array();

                    // prepare feedback editor to display files in draft area
                    $draftid_editor = file_get_submitted_draft_itemid('feedback['.$key.']');
                    $default_values['feedback['.$key.']']['text'] = file_prepare_draft_area(
                        $draftid_editor,       // draftid
                        $this->context->id,    // context
                        'question',   // component
                        'answerfeedback',             // filarea
                        !empty($answer->id)?(int)$answer->id:null, // itemid
                        $this->fileoptions,    // options
                        $answer->feedback      // text
                    );
                    $default_values['feedback['.$key.']']['itemid'] = $draftid_editor;
                    // prepare files code block ends

                    $default_values['feedback['.$key.']']['format'] = $answer->feedbackformat;
                    $key++;
                }
            }
            $default_values['usecase'] = $question->options->usecase;
            $question = (object)((array)$question + $default_values);
        }
        return $question;
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer !== ''){
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 || !html_is_blank($data['feedback'][$key]['text'])) {
                $errors["answer[$key]"] = get_string('answermustbegiven', 'qtype_shortanswer');
                $answercount++;
            }
        }
        if ($answercount==0){
            $errors['answer[0]'] = get_string('notenoughanswers', 'quiz', 1);
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }
        return $errors;
    }
    function qtype() {
        return 'shortanswer';
    }
}

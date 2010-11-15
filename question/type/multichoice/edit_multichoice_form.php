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
 * Defines the editing form for the multichoice question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * multiple choice editing form definition.
 */
class question_edit_multichoice_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        global $QTYPES;

        $menu = array(get_string('answersingleno', 'qtype_multichoice'), get_string('answersingleyes', 'qtype_multichoice'));
        $mform->addElement('select', 'single', get_string('answerhowmany', 'qtype_multichoice'), $menu);
        $mform->setDefault('single', 1);

        $mform->addElement('advcheckbox', 'shuffleanswers', get_string('shuffleanswers', 'qtype_multichoice'), null, null, array(0,1));
        $mform->addHelpButton('shuffleanswers', 'shuffleanswers', 'qtype_multichoice');
        $mform->setDefault('shuffleanswers', 1);

        $numberingoptions = $QTYPES[$this->qtype()]->get_numbering_styles();
        $menu = array();
        foreach ($numberingoptions as $numberingoption) {
            $menu[$numberingoption] = get_string('answernumbering' . $numberingoption, 'qtype_multichoice');
        }
        $mform->addElement('select', 'answernumbering', get_string('answernumbering', 'qtype_multichoice'), $menu);
        $mform->setDefault('answernumbering', 'abc');

/*        $mform->addElement('static', 'answersinstruct', get_string('choices', 'qtype_multichoice'), get_string('fillouttwochoices', 'qtype_multichoice'));
        $mform->closeHeaderBefore('answersinstruct');
*/
        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'),
                $creategrades->gradeoptionsfull, max(5, QUESTION_NUMANS_START));

        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'qtype_multichoice'));

        foreach (array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback') as $feedbackname) {
            $mform->addElement('editor', $feedbackname, get_string($feedbackname, 'qtype_multichoice'),
                                array('rows' => 10), $this->editoroptions);
            $mform->setType($feedbackname, PARAM_RAW);
        }

    }

    function data_preprocessing($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;

                    // prepare question text
                    $draftid = file_get_submitted_draft_itemid('feedback['.$key.']');
                    $default_values['feedback['.$key.']'] = array();
                    $default_values['feedback['.$key.']']['text'] = file_prepare_draft_area($draftid, $this->context->id, 'question', 'answerfeedback', empty($answer->id)?null:(int)$answer->id, $this->fileoptions, $answer->feedback);
                    $default_values['feedback['.$key.']']['format'] = $answer->feedbackformat;
                    $default_values['feedback['.$key.']']['itemid'] = $draftid;
                    $key++;
                }
            }
            $default_values['single'] =  $question->options->single;
            $default_values['answernumbering'] =  $question->options->answernumbering;
            $default_values['shuffleanswers'] =  $question->options->shuffleanswers;

            // prepare feedback editor to display files in draft area
            foreach (array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback') as $feedbackname) {
                $draftid = file_get_submitted_draft_itemid($feedbackname);
                $text = $question->options->$feedbackname;
                $feedbackformat = $feedbackname . 'format';
                $format = $question->options->$feedbackformat;
                $default_values[$feedbackname] = array();
                $default_values[$feedbackname]['text'] = file_prepare_draft_area(
                    $draftid,       // draftid
                    $this->context->id,    // context
                    'qtype_multichoice',   // component
                    $feedbackname,         // filarea
                    !empty($question->id)?(int)$question->id:null, // itemid
                    $this->fileoptions,    // options
                    $text      // text
                );
                $default_values[$feedbackname]['format'] = $format;
                $default_values[$feedbackname]['itemid'] = $draftid;
            }
            // prepare files code block ends

            $question = (object)((array)$question + $default_values);
        }
        return $question;
    }

    function qtype() {
        return 'multichoice';
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['answer'];
        $answercount = 0;

        $totalfraction = 0;
        $maxfraction = -1;

        foreach ($answers as $key => $answer){
            //check no of choices
            $trimmedanswer = trim($answer);
            if (!empty($trimmedanswer)){
                $answercount++;
            }
            //check grades
            if ($answer != '') {
                if ($data['fraction'][$key] > 0) {
                    $totalfraction += $data['fraction'][$key];
                }
                if ($data['fraction'][$key] > $maxfraction) {
                    $maxfraction = $data['fraction'][$key];
                }
            }
        }

        if ($answercount==0){
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);
        } elseif ($answercount==1){
            $errors['answer[1]'] = get_string('notenoughanswers', 'qtype_multichoice', 2);

        }

        /// Perform sanity checks on fractional grades
        if ($data['single']) {
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $errors['fraction[0]'] = get_string('errfractionsnomax', 'qtype_multichoice', $maxfraction);
            }
        } else {
            $totalfraction = round($totalfraction,2);
            if ($totalfraction != 1) {
                $totalfraction = $totalfraction * 100;
                $errors['fraction[0]'] = get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
            }
        }
        return $errors;
    }
}

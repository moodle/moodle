<?php
/**
 * Defines the editing form for the missingtype question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * missingtype editing form definition.
 */
class question_edit_missingtype_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        $creategrades = get_grade_options();
        $gradeoptions = $creategrades->gradeoptionsfull;
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'choicehdr', get_string('choiceno', 'qtype_multichoice', '{no}'));
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'),
                                array('course' => $this->coursefilesid));

        if (isset($this->question->options)){
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }
        if ($this->question->formoptions->repeatelements){
            $repeatsatstart = (QUESTION_NUMANS_START > ($countanswers + QUESTION_NUMANS_ADD))?
                                QUESTION_NUMANS_START : ($countanswers + QUESTION_NUMANS_ADD);
        } else {
            $repeatsatstart = $countanswers;
        }
        $repeatedoptions = array();
        $repeatedoptions['fraction']['default'] = 0;
        $mform->setType('answer', PARAM_NOTAGS);
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', QUESTION_NUMANS_ADD, get_string('addmorechoiceblanks', 'qtype_multichoice'));
    }

    function set_data($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['feedback['.$key.']'] = $answer->feedback;
                    $key++;
                }
            }
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'missingtype';
    }

    function validation($data){
        $errors = parent::validation($data);
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
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_multichoice', 1);
        }


        $totalfraction = round($totalfraction, 2);
        if ($totalfraction != 1) {
            $totalfraction = $totalfraction * 100;
            $errors['fraction[0]'] = get_string('errfractionsaddwrong', 'qtype_multichoice', $totalfraction);
        }

        return $errors;
    }
}
?>
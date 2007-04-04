<?php
/**
 * Defines the editing form for the shortanswer question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
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
        $gradeoptions = $creategrades->gradeoptions;
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', get_string('answerno', 'qtype_shortanswer', '{no}'));
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'));
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'));

        if (isset($this->question->options)){
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }
        $repeatsatstart = (QUESTION_NUMANS_START > ($countanswers + QUESTION_NUMANS_ADD))?
                            QUESTION_NUMANS_START : ($countanswers + QUESTION_NUMANS_ADD);
        $repeatedoptions = array();
        $mform->setType('answer', PARAM_RAW);
        $repeatedoptions['fraction']['default'] = 0;
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', QUESTION_NUMANS_ADD, get_string('addmoreanswerblanks', 'qtype_shortanswer'));

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
            $default_values['usecase'] =  $question->options->usecase;
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }
    function validation($data){
        $errors = array();
        $answers = $data['answer'];
        $answercount = 0;
        $maxgrade = false;
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if (!empty($trimmedanswer)){
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
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
?>
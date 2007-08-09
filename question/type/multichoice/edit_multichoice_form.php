<?php
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
        $mform->setHelpButton('shuffleanswers', array('multichoiceshuffle', get_string('shuffleanswers','qtype_multichoice'), 'quiz'));
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
        $gradeoptions = $creategrades->gradeoptionsfull;
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'choicehdr', get_string('choiceno', 'qtype_multichoice', '{no}'));
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'), array('size' => 50));
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
        $mform->setType('answer', PARAM_RAW);
        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', QUESTION_NUMANS_ADD, get_string('addmorechoiceblanks', 'qtype_multichoice'));

        $mform->addElement('header', 'overallfeedbackhdr', get_string('overallfeedback', 'qtype_multichoice'));

        $mform->addElement('htmleditor', 'correctfeedback', get_string('correctfeedback', 'qtype_multichoice'),
                                array('course' => $this->coursefilesid));
        $mform->setType('correctfeedback', PARAM_RAW);

        $mform->addElement('htmleditor', 'partiallycorrectfeedback', get_string('partiallycorrectfeedback', 'qtype_multichoice'),
                                array('course' => $this->coursefilesid));
        $mform->setType('partiallycorrectfeedback', PARAM_RAW);

        $mform->addElement('htmleditor', 'incorrectfeedback', get_string('incorrectfeedback', 'qtype_multichoice'),
                                array('course' => $this->coursefilesid));
        $mform->setType('incorrectfeedback', PARAM_RAW);

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
            $default_values['single'] =  $question->options->single;
            $default_values['answernumbering'] =  $question->options->answernumbering;
            $default_values['shuffleanswers'] =  $question->options->shuffleanswers;
            $default_values['correctfeedback'] =  $question->options->correctfeedback;
            $default_values['partiallycorrectfeedback'] =  $question->options->partiallycorrectfeedback;
            $default_values['incorrectfeedback'] =  $question->options->incorrectfeedback;
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'multichoice';
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
?>
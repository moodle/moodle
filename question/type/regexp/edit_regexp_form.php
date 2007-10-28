<?php
/** by Joseph Rézeau 23:43 24/02/2007
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

class question_edit_regexp_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */

    function definition_inner(&$mform) {
        $langfile = 'qtype_'.$this->qtype();

        $mform->removeElement('generalfeedback'); //JR
        $menu = array(get_string('no', 'moodle'), get_string('yes', 'moodle'));
        $mform->addElement('select', 'usehint', get_string('usehint', 'qtype_regexp'), $menu);
        $mform->setHelpButton('usehint', array('regexphint', get_string('usehint', 'qtype_regexp'), 'qtype_regexp'));
        $mform->addElement('static', 'answersinstruct', get_string('correctanswers', 'quiz'), get_string('filloutoneanswer', $langfile));
        $mform->closeHeaderBefore('answersinstruct');
        $creategrades = get_grade_options();
        $gradeoptions = $creategrades->gradeoptions;
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', get_string('answerno', $langfile, '{no}'));
        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'),array('size'=>100)); //JR
        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeated[] =& $mform->createElement('textarea', 'feedback', get_string('feedback', 'quiz'),array('cols'=>60, 'rows'=>1));

        if (isset($this->question->options)){
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }
        $repeatsatstart = (QUESTION_NUMANS_START > ($countanswers + QUESTION_NUMANS_ADD))?
                            QUESTION_NUMANS_START : ($countanswers + QUESTION_NUMANS_ADD);
        $repeatedoptions = array();
        $mform->setType('answer', PARAM_NOTAGS);
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
            $default_values['image'] =  "None";
            $default_values['usehint'] =  $question->options->usehint;
            $question = (object)((array)$question + $default_values);
        } else {
			$key = 0;
            $default_values['fraction['.$key.']'] = 1;
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }
    function validation($data){
        $langfile = 'qtype_'.$this->qtype();
        $errors = array();
        $answers = $data['answer'];
        $answercount = 0;
        foreach ($answers as $answer){
            $trimmedanswer = trim($answer);
            if (!empty($trimmedanswer)){
					$parenserror = check_my_parens($trimmedanswer);
					if ($parenserror) {
						$errors['answer['.$answercount.']'] = $parenserror;
					}
				$answercount++;
            }
        }
        if ($answercount==0){
            $errors['answer[0]'] = get_string('notenoughanswers', $langfile);
        }
		if ($data['fraction'][0] != 1) {
			$errors['fraction[0]'] = get_string('fractionsnomax', $langfile, 1);
		}
        return $errors;
    }
    function qtype() {
        return 'regexp';
    }
}
?>
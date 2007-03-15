<?php
/**
 * Defines the editing form for the numerical question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * numerical editing form definition.
 */
class question_edit_numerical_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {

//------------------------------------------------------------------------------------------
        $creategrades = get_grade_options();
        $gradeoptions = $creategrades->gradeoptions;
        $repeated = array();
        $repeatedoptions = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', get_string('answerno', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'answer', get_string('answer', 'quiz'));
        $mform->setType('answer', PARAM_NUMBER);

        $repeated[] =& $mform->createElement('text', 'tolerance', get_string('acceptederror', 'quiz'));
        $mform->setType('tolerance', PARAM_NUMBER);

        $repeated[] =& $mform->createElement('select', 'fraction', get_string('grade'), $gradeoptions);
        $repeatedoptions['fraction']['default'] = 0;

        $repeated[] =& $mform->createElement('htmleditor', 'feedback', get_string('feedback', 'quiz'));
        $mform->setType('feedback', PARAM_RAW);


        if (isset($this->question->options)){
            $countanswers = count($this->question->options->answers);
        } else {
            $countanswers = 0;
        }
        $repeatsatstart = (QUESTION_NUMANS_START > ($countanswers + 1))?
                            QUESTION_NUMANS_START : ($countanswers + 1);

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions, 'noanswers', 'addanswers', 2, get_string('addmoreanswerblanks', 'qtype_numerical'));

//------------------------------------------------------------------------------------------
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
        $mform->setType('multiplier', PARAM_NOTAGS);

        if (isset($this->question->options)){
            $countunits = count($this->question->options->units);
        } else {
            $countunits = 0;
        }
        $repeatsatstart = $countunits + 2;
        $this->repeat_elements($repeated, $repeatsatstart, array(), 'nounits', 'addunits', 2, get_string('addmoreunitblanks', 'qtype_numerical'));

        $firstunit =& $mform->getElement('multiplier[0]');
        $firstunit->freeze();
        $firstunit->setValue('1.0');
        $firstunit->setPersistantFreeze(true);
    }

    function set_data($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            if (count($answers)) {
                $key = 0;
                foreach ($answers as $answer){
                    $default_values['answer['.$key.']'] = $answer->answer;
                    $default_values['fraction['.$key.']'] = $answer->fraction;
                    $default_values['tolerance['.$key.']'] = $answer->tolerance;
                    $default_values['feedback['.$key.']'] = $answer->feedback;
                    $key++;
                }
            }
            $units  = array_values($question->options->units);
            // make sure the default unit is at index 0
            usort($units, create_function('$a, $b', // make sure the default unit is at index 0
            'if (1.0 === (float)$a->multiplier) { return -1; } else '.
            'if (1.0 === (float)$b->multiplier) { return 1; } else { return 0; }'));
            if (count($units)) {
                $key = 0;
                foreach ($units as $unit){
                    $default_values['unit['.$key.']'] = $unit->unit;
                    $default_values['multiplier['.$key.']'] = $unit->multiplier;
                    $key++;
                }
            }
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }
    function validation($data){
        $errors = array();

        // Check the answers.
        $answercount = 0;
        $maxgrade = false;
        $answers = $data['answer'];
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer!=''){
                $answercount++;
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            }
        }
        if ($answercount==0){
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_numerical');
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }

        // Check units.
        $alreadyseenunits = array();
        foreach ($data['unit'] as $key => $unit) {
            $trimmedunit = trim($unit);
            if ($trimmedunit!='' && in_array($trimmedunit, $alreadyseenunits)) {
                $errors["unit[$key]"] = get_string('errorrepeatedunit', 'qtype_numerical');
                if (trim($data['multiplier'][$key]) == '') {
                    $errors["multiplier[$key]"] = get_string('errornomultiplier', 'qtype_numerical');
                }
            } else {
                $alreadyseenunits[] = $trimmedunit;
            }
        }

        return $errors;
    }
    function qtype() {
        return 'numerical';
    }
}
?>
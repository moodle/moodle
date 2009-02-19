<?php  // $Id$
/**
 * Defines the editing form for the numerical question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * numerical editing form definition.
 */
class question_edit_numerical_form extends question_edit_form {

    function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);

        $tolerance =& $mform->createElement('text', 'tolerance', get_string('acceptederror', 'quiz'));
        $repeatedoptions['tolerance']['type'] = PARAM_NUMBER;
        array_splice($repeated, 3, 0, array($tolerance));
        $repeated[1]->setSize(10);

        return $repeated;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {

//------------------------------------------------------------------------------------------
        $creategrades = get_grade_options();
        $this->add_per_answer_fields($mform, get_string('answerno', 'qtype_numerical', '{no}'),
                $creategrades->gradeoptions);
//------------------------------------------------------------------------------------------
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'unithdr', get_string('unithdr', 'qtype_numerical', '{no}'));

        $repeated[] =& $mform->createElement('text', 'unit', get_string('unit', 'quiz'));
        $mform->setType('unit', PARAM_NOTAGS);

        $repeated[] =& $mform->createElement('text', 'multiplier', get_string('multiplier', 'quiz'));
        $mform->setType('multiplier', PARAM_NUMBER);

        if (isset($this->question->options)){
            $countunits = count($this->question->options->units);
        } else {
            $countunits = 0;
        }

        if ($this->question->formoptions->repeatelements){
            $repeatsatstart = $countunits + 2;
        } else {
            $repeatsatstart = $countunits;
        }
        $this->repeat_elements($repeated, $repeatsatstart, array(), 'nounits', 'addunits', 2, get_string('addmoreunitblanks', 'qtype_numerical'));

        if ($mform->elementExists('multiplier[0]')) {
        /// Does not exist when this form is used in 'move to another category'
        /// mode with a qusetion that has no units. This was leading to errors.
            $firstunit =& $mform->getElement('multiplier[0]');
            $firstunit->freeze();
            $firstunit->setValue('1.0');
            $firstunit->setPersistantFreeze(true);
        }
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
            if (!empty($units)) {
                foreach ($units as $key => $unit){
                    $default_values['unit['.$key.']'] = $unit->unit;
                    $default_values['multiplier['.$key.']'] = $unit->multiplier;
                }
            }
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check the answers.
        $answercount = 0;
        $maxgrade = false;
        $answers = $data['answer'];
        foreach ($answers as $key => $answer) {
            $trimmedanswer = trim($answer);
            if ($trimmedanswer != '') {
                $answercount++;
                if (!(is_numeric($trimmedanswer) || $trimmedanswer == '*')) {
                    $errors["answer[$key]"] = get_string('answermustbenumberorstar', 'qtype_numerical');
                }
                if ($data['fraction'][$key] == 1) {
                    $maxgrade = true;
                }
            } else if ($data['fraction'][$key] != 0 || !html_is_blank($data['feedback'][$key])) {
                $errors["answer[$key]"] = get_string('answermustbenumberorstar', 'qtype_numerical');
                $answercount++;
            }
        }
        if ($answercount == 0) {
            $errors['answer[0]'] = get_string('notenoughanswers', 'qtype_numerical');
        }
        if ($maxgrade == false) {
            $errors['fraction[0]'] = get_string('fractionsnomax', 'question');
        }

        // Check units.
        $alreadyseenunits = array();
        if (isset($data['unit'])) {
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
        }

        return $errors;
    }
    function qtype() {
        return 'numerical';
    }
}
?>
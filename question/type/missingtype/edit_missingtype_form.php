<?php  // $Id$
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
        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'),
                $creategrades->gradeoptionsfull);
    }

    function set_data($question) {
        if (isset($question->options)){
            $answers = $question->options->answers;
            $default_values = array();
            if (is_array($answers) && count($answers)) {
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

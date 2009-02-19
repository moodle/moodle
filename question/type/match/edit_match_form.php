<?php  // $Id$
/**
 * Defines the editing form for the match question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * match editing form definition.
 */
class question_edit_match_form extends question_edit_form {

    function get_per_answer_fields(&$mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $repeated[] =& $mform->createElement('header', 'answerhdr', $label);
        $repeated[] =& $mform->createElement('textarea', 'subquestions', get_string('question', 'quiz'), array('cols'=>40, 'rows'=>3));
        $repeated[] =& $mform->createElement('text', 'subanswers', get_string('answer', 'quiz'), array('size'=>50));
        $repeatedoptions['subquestions']['type'] = PARAM_RAW;
        $repeatedoptions['subanswers']['type'] = PARAM_TEXT;
        $answersoption = 'subquestions';
        return $repeated;
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        $mform->addElement('advcheckbox', 'shuffleanswers', get_string('shuffle', 'quiz'), null, null, array(0,1));
        $mform->setHelpButton('shuffleanswers', array('matchshuffle', get_string('shuffle','quiz'), 'quiz'));
        $mform->setDefault('shuffleanswers', 1);

        $mform->addElement('static', 'answersinstruct', get_string('choices', 'quiz'), get_string('filloutthreeqsandtwoas', 'qtype_match'));
        $mform->closeHeaderBefore('answersinstruct');

        $this->add_per_answer_fields($mform, get_string('questionno', 'quiz', '{no}'), 0);
    }

    function set_data($question) {
        if (isset($question->options)){
            $subquestions = $question->options->subquestions;
            if (count($subquestions)) {
                $key = 0;
                foreach ($subquestions as $subquestion){
                    $default_values['subanswers['.$key.']'] = $subquestion->answertext;
                    $default_values['subquestions['.$key.']'] = $subquestion->questiontext;
                    $key++;
                }
            }
            $default_values['shuffleanswers'] =  $question->options->shuffleanswers;
            $question = (object)((array)$question + $default_values);
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'match';
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $answers = $data['subanswers'];
        $questions = $data['subquestions'];
        $questioncount = 0;
        $answercount = 0;
        foreach ($questions as $key => $question){
            $trimmedquestion = trim($question);
            $trimmedanswer = trim($answers[$key]);
            if ($trimmedquestion != ''){
                $questioncount++;
            }
            if ($trimmedanswer != '' || $trimmedquestion != ''){
                $answercount++;
            }
            if ($trimmedquestion != '' && $trimmedanswer == ''){
                $errors['subanswers['.$key.']'] = get_string('nomatchinganswerforq', 'qtype_match', $trimmedquestion);
            }
        }
        $numberqanda = new stdClass;
        $numberqanda->q = 2;
        $numberqanda->a = 3;
        if ($questioncount < 1){
            $errors['subquestions[0]'] = get_string('notenoughqsandas', 'qtype_match', $numberqanda);
        }
        if ($questioncount < 2){
            $errors['subquestions[1]'] = get_string('notenoughqsandas', 'qtype_match', $numberqanda);
        }
        if ($answercount < 3){
            $errors['subanswers[2]'] = get_string('notenoughqsandas', 'qtype_match', $numberqanda);
        }
        return $errors;
    }
}
?>
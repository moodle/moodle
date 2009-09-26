<?php  // $Id$
/**
 * Defines the editing form for the randomsamatch question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * randomsamatch editing form definition.
 */
class question_edit_randomsamatch_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        $mform->removeElement('image');

        $questionstoselect = array();
        for ($i=2; $i<=QUESTION_NUMANS; $i++){
            $questionstoselect[$i] = $i;
        }

        $mform->addElement('select', 'choose', get_string("randomsamatchnumber", "quiz"), $questionstoselect);
        $mform->setType('feedback', PARAM_RAW);

        $mform->addElement('hidden', 'fraction', 0);
        $mform->setType('fraction', PARAM_RAW);
    }

    function set_data($question) {
        if (empty($question->name)) {
            $question->name =  get_string("randomsamatch", "quiz");
        }

        if (empty($question->questiontext)) {
            $question->questiontext =  get_string("randomsamatchintro", "quiz");
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'randomsamatch';
    }

    function validation($data, $files) {
        global $QTYPES;
        $errors = parent::validation($data, $files);
        if (isset($data->categorymoveto)) {
            list($category) = explode(',', $data['categorymoveto']);
        } else {
            list($category) = explode(',', $data['category']);
        }
        $saquestions = $QTYPES['randomsamatch']->get_sa_candidates($category);
        $numberavailable = count($saquestions);
        if ($saquestions === false){
            $a = new object();
            $a->catname = get_field('question_categories', 'name', 'id', $category);
            $errors['choose'] = get_string('nosaincategory', 'qtype_randomsamatch', $a);

        } elseif ($numberavailable < $data['choose']){
            $a = new object();
            $a->catname = get_field('question_categories', 'name', 'id', $category);
            $a->nosaquestions = $numberavailable;
            $errors['choose'] = get_string('notenoughsaincategory', 'qtype_randomsamatch', $a);
        }
        return $errors;

    }
}
?>
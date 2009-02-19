<?php  // $Id$
require_once($CFG->dirroot.'/question/type/edit_question_form.php');
/**
 * Defines the editing form for the thruefalse question type.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 *//** */

/**
 * truefalse editing form definition.
 */
class question_edit_truefalse_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        $mform->addElement('select', 'correctanswer', get_string('correctanswer', 'qtype_truefalse'),
                array(0 => get_string('false', 'qtype_truefalse'), 1 => get_string('true', 'qtype_truefalse')));

        $mform->addElement('htmleditor', 'feedbacktrue', get_string('feedbacktrue', 'qtype_truefalse'),
                                array('course' => $this->coursefilesid));;
        $mform->setType('feedbacktrue', PARAM_RAW);

        $mform->addElement('htmleditor', 'feedbackfalse', get_string('feedbackfalse', 'qtype_truefalse'),
                                array('course' => $this->coursefilesid));
        $mform->setType('feedbackfalse', PARAM_RAW);

        // Fix penalty factor at 1.
        $mform->setDefault('penalty', 1);
        $mform->freeze('penalty');
    }

    function set_data($question) {
        if (!empty($question->options->trueanswer)) {
            $trueanswer = $question->options->answers[$question->options->trueanswer];
            $question->correctanswer = ($trueanswer->fraction != 0);
            $question->feedbacktrue = $trueanswer->feedback;
            $question->feedbackfalse = $question->options->answers[$question->options->falseanswer]->feedback;
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'truefalse';
    }
}
?>
<?php  // $Id$
/**
 * Defines the editing form for the description question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * description editing form definition.
 */
class question_edit_description_form extends question_edit_form {
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    function definition_inner(&$mform) {
        //don't need these default elements :
        $mform->removeElement('defaultgrade');
        $mform->removeElement('penalty');

        $mform->addElement('hidden', 'defaultgrade', 0);
        $mform->setType('defaultgrade', PARAM_RAW);
    }

    function qtype() {
        return 'description';
    }
}
?>
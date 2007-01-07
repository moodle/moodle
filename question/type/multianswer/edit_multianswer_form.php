<?php
/**
 * Defines the editing form for the multianswer question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

/**
 * multianswer editing form definition.
 */
class question_edit_multianswer_form extends question_edit_form {

    // No question-type specific form fields.


    function qtype() {
        return 'multianswer';
    }
}
?>
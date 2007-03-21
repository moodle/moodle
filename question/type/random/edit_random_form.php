<?php
/**
 * Defines the editing form for the random question type.
 *
 * @copyright &copy; 2007 Jamie Pratt
 * @author Jamie Pratt me@jamiep.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 */

/**
 * random editing form definition.
 */
class question_edit_random_form extends question_edit_form {
    /**
     * Build the form definition.
     *
     * This adds all the form files that the default question type supports.
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    function definition() {
        global $COURSE, $CFG;

        $qtype = $this->qtype();
        $langfile = "qtype_$qtype";

        $mform =& $this->_form;

        // Standard fields at the start of the form.
        $mform->addElement('header', 'generalheader', get_string("general", 'form'));

        $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                array('courseid' => $COURSE->id, 'published' => true, 'only_editable' => true));

        $mform->addElement('text', 'name', get_string('questionname', 'quiz'),
                array('size' => 50));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'questiontext', get_string("recurse", "quiz"), null, null, array(0, 1));

        // Standard fields at the end of the form.
        $mform->addElement('hidden', 'questiontextformat', 0);
        $mform->setType('questiontextformat', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_ALPHA);

        $mform->addElement('hidden', 'inpopup');
        $mform->setType('inpopup', PARAM_INT);

        $mform->addElement('hidden', 'versioning');
        $mform->setType('versioning', PARAM_BOOL);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));

        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    function set_data($question) {
        if (empty($question->name)) {
            $question->name = get_string("random", "quiz");
        }
        parent::set_data($question);
    }

    function qtype() {
        return 'random';
    }
}
?>
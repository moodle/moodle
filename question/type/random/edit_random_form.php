<?php  // $Id$
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
                array('contexts' => $this->contexts->having_cap('moodle/question:useall')));

        $mform->addElement('advcheckbox', 'questiontext', get_string("recurse", "quiz"), null, null, array(0, 1));

        $mform->addElement('hidden', 'name');
        $mform->setType('name', PARAM_ALPHA);
        $mform->setDefault('name', '');

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

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', 0);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', 0);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->setDefault('returnurl', 0);

        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('savechanges'));

        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
    function validation($fromform, $files) {
        //validation of category
        //is not relevant for this question type
        return array();
    }
    function qtype() {
        return 'random';
    }
}
?>
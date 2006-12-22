<?php
/**
 * A base class for question editing forms.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 *//** */

/**
 * Form definition base class. This defines the common fields that
 * all question types need. Question types should define their own
 * class that inherits from this one, and implements the definition_inner()
 * method.
 */
class edit_question_form extends moodleform {
    /**
     * Build the form definition.
     * 
     * This adds all the form files that the default question type supports. 
     * If your question type does not support all these fields, then you can
     * override this method and remove the ones you don't want with $mform->removeElement().
     */
    function definition() {
        global $COURSE;
        
        $qtype = $this->qtype();
        $langfile = "qtype_$qtype";
        
        $mform =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        // Standard fields at the start of the form.
        $mform->addElement('header', 'formheader', get_string("editing$qtype", $langfile));
        $mform->setHelpButton('formheader', array($qtype, get_string($qtype, $qtype), $qtype));
        
        $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'),
                array('courseid' => $COURSE->id, 'published' => true, 'only_editable' => true));
        
        $mform->addElement('text', 'name', get_string('questionname', 'quiz'),
                array('size' => 50));
        $mform->setType('name', PARAM_MULTILANG);
        $mform->addRule('name', null, 'required', null, 'client');
        
        $mform->addElement('htmleditor', 'questiontext', get_string('questiontext', 'quiz'),
                array('rows' => 15, 'course' => $COURSE->id));
        $mform->setType('questiontext', PARAM_RAW);
        $mform->setHelpButton('questiontext', array('questiontext', get_string('questiontext', 'quiz'), 'quiz'));
        $mform->addElement('format', 'questiontextformat', get_string('format'));

        if (empty($images)) {
            $mform->addElement('static', 'image', get_string('imagedisplay', 'quiz'), get_string('noimagesyet'));
        } else {
            $images[''] = get_string('none');
            $mform->addElement('select', 'image', get_string('imagedisplay', 'quiz'), $images);
            $mform->setType('image', PARAM_FILE);
        }
  
        $mform->addElement('text', 'defaultgrade', get_string('defaultgrade', 'quiz'),
                array('size' => 3));
        $mform->setType('defaultgrade', PARAM_INT);
        $mform->addRule('defaultgrade', null, 'required', null, 'client');

        $mform->addElement('text', 'penalty', get_string('penaltyfactor', 'quiz'),
                array('size' => 3));
        $mform->setType('penalty', PARAM_NUMBER);
        $mform->addRule('penalty', null, 'required', null, 'client');
        $mform->setHelpButton('penalty', array('penalty', get_string('penalty', 'quiz'), 'quiz'));

        $mform->addElement('htmleditor', 'generalfeedback', get_string('generalfeedback', 'quiz'),
                array('rows' => 10, 'course' => $COURSE->id));
        $mform->setType('generalfeedback', PARAM_RAW);
        $mform->setHelpButton('generalfeedback', array('generalfeedback', get_string('generalfeedback', 'quiz'), 'quiz'));
  
        // Any questiontype specific fields.
        $this->definition_inner($mform);

        // Standard fields at the end of the form.
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
        if (!empty($id)) {
            $buttonarray[] = &$mform->createElement('submit', 'makecopy', get_string('makecopy', 'quiz'));
        }
        $buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('revert'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $renderer->addStopFieldsetElements('buttonar');
    }
    
    /**
     * Add any question-type specific form fields.
     * 
     * @param object $mform the form being built. 
     */
    function definition_inner(&$mform) {
        // By default, do nothing.
    }
    
    function set_defaults($question) {
        global $QTYPES;
        $QTYPES[$question->qtype]->set_default_options($question);
        parent::set_defaults($question);
    }
        
    /**
     * Override this in the subclass to question type name.
     * @return the question type name, should be the same as the name() method in the question type class.
     */
    function qtype() {
        return '';
    }
}

?>
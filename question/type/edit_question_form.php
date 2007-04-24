<?php
/**
 * A base class for question editing forms.
 *
 * @copyright &copy; 2006 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
 *//** */

/**
 * Form definition base class. This defines the common fields that
 * all question types need. Question types should define their own
 * class that inherits from this one, and implements the definition_inner()
 * method.
 * 
 * @package questionbank
 * @subpackage questiontypes
 */
class question_edit_form extends moodleform {
    /**
     * Question object with options and answers already loaded by get_question_options
     * Be careful how you use this it is needed sometimes to set up the structure of the
     * form in definition_inner but data is always loaded into the form with set_data.
     *
     * @var object
     */
    var $question;

    function question_edit_form($submiturl, $question){
        $this->question = $question;
        parent::moodleform($submiturl);
    }
    /**
     * Build the form definition.
     *
     * This adds all the form fields that the default question type supports.
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

        $mform->addElement('questioncategory', 'category', get_string('category', 'quiz'), null,
                array('courseid' => $COURSE->id, 'published' => true, 'only_editable' => true));

        $mform->addElement('text', 'name', get_string('questionname', 'quiz'),
                array('size' => 50));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'questiontext', get_string('questiontext', 'quiz'),
                array('rows' => 15, 'course' => $COURSE->id));
        $mform->setType('questiontext', PARAM_RAW);
        $mform->setHelpButton('questiontext', array(array('questiontext', get_string('questiontext', 'quiz'), 'quiz'), 'richtext'), false, 'editorhelpbutton');
        $mform->addElement('format', 'questiontextformat', get_string('format'));

        make_upload_directory("$COURSE->id");    // Just in case
        $coursefiles = get_directory_list("$CFG->dataroot/$COURSE->id", $CFG->moddata);
        foreach ($coursefiles as $filename) {
            if (mimeinfo("icon", $filename) == "image.gif") {
                $images["$filename"] = $filename;
            }
        }
        if (empty($images)) {
            $mform->addElement('static', 'image', get_string('imagedisplay', 'quiz'), get_string('noimagesyet'));
        } else {
            $mform->addElement('select', 'image', get_string('imagedisplay', 'quiz'), array_merge(array(''=>get_string('none')), $images));
        }

        $mform->addElement('text', 'defaultgrade', get_string('defaultgrade', 'quiz'),
                array('size' => 3));
        $mform->setType('defaultgrade', PARAM_INT);
        $mform->setDefault('defaultgrade', 1);
        $mform->addRule('defaultgrade', null, 'required', null, 'client');

        $mform->addElement('text', 'penalty', get_string('penaltyfactor', 'quiz'),
                array('size' => 3));
        $mform->setType('penalty', PARAM_NUMBER);
        $mform->addRule('penalty', null, 'required', null, 'client');
        $mform->setHelpButton('penalty', array('penalty', get_string('penalty', 'quiz'), 'quiz'));
        $mform->setDefault('penalty', 0.1);

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
        if (!empty($this->question->id)) {
            $buttonarray[] = &$mform->createElement('submit', 'makecopy', get_string('makecopy', 'quiz'));
        }
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Add any question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    function definition_inner(&$mform) {
        // By default, do nothing.
    }

    function set_data($question) {
        global $QTYPES;
        $QTYPES[$question->qtype]->set_default_options($question);
        if (empty($question->image)){
            unset($question->image);
        }

        // Set any options.
        $extra_question_fields = $QTYPES[$question->qtype]->extra_question_fields();
        if (is_array($extra_question_fields)) {
            array_shift($extra_question_fields);
            foreach ($extra_question_fields as $field) {
                $question->$field = $question->options->$field;
            }
        }
        
        parent::set_data($question);
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
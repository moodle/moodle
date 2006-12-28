<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class mod_data_comment_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('htmleditor', 'content', get_string('comment', 'data'), array('cols'=>85, 'rows'=>18));
        $mform->addRule('content', get_string('required'), 'required', null, 'client');
        $mform->setType('content', PARAM_RAW); // cleaned before the display

        $mform->addElement('format', 'format', get_string('format'));
        $mform->setHelpButton('format', array('textformat', get_string('helpformatting')));

        // hidden optional params
        $mform->addElement('hidden', 'mode', 'add');
        $mform->setType('mode', PARAM_ALPHA);

        $mform->addElement('hidden', 'page', 0);
        $mform->setType('page', PARAM_INT);

        $mform->addElement('hidden', 'rid', 0);
        $mform->setType('rid', PARAM_INT);

        $mform->addElement('hidden', 'commentid', 0);
        $mform->setType('commentid', PARAM_INT);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }
}
?>
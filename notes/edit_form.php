<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class note_edit_form extends moodleform {

    function definition() {
        $mform    =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('select', 'user', get_string('user'), $this->_customdata['userlist']);
        $mform->addRule('user', get_string('nouser', 'notes'), 'required', null, 'client');

        $mform->addElement('textarea', 'content', get_string('content', 'notes'), array('rows'=>15, 'cols'=>40));
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('nocontent', 'notes'), 'required', null, 'client');
        $mform->setHelpButton('content', array('writing', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('select', 'rating', get_string('rating', 'notes'), note_get_rating_names());
        $mform->setDefault('rating', 3);

        $mform->addElement('select', 'publishstate', get_string('publishstate', 'notes'), note_get_state_names());
        $mform->setDefault('publishstate', NOTES_STATE_PUBLIC);
        $mform->setType('publishstate', PARAM_ALPHA);

        $this->add_action_buttons();

        $mform->addElement('hidden', 'course');
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'note');
        $mform->setType('note', PARAM_INT);
    }
}

<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class forgot_password_form extends moodleform {

    function definition() {
        $mform    =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        $mform->addElement('header', '', get_string('passwordforgotten'), '');

        $mform->addElement('text', 'username', get_string('username'));
        $mform->setType('username', PARAM_RAW);

        $mform->addElement('text', 'email', get_string('email'));
        $mform->setType('email', PARAM_RAW);

         // hidden params
        $mform->addElement('hidden', 'action', 'find');
        $mform->setType('action', PARAM_ALPHA);

        // buttons
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('ok'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);

        $renderer->addStopFieldsetElements('buttonar');
    }
}

?>
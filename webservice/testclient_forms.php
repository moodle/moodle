<?php

require_once($CFG->dirroot.'/lib/formslib.php');


class webservice_test_client_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $functions = $this->_customdata;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        $mform->addElement('select', 'function', get_string('function', 'webservice'), $functions);

        $this->add_action_buttons(false, get_string('select'));
    }
}

class moodle_group_get_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $mform->addElement('text', 'wsusername', 'wsusername');
        $mform->addElement('text', 'wspassword', 'wspassword');
        $mform->addElement('text', 'groupids[0]', 'groupids[0]');
        $mform->addElement('text', 'groupids[1]', 'groupids[1]');
        $mform->addElement('text', 'groupids[2]', 'groupids[2]');
        $mform->addElement('text', 'groupids[3]', 'groupids[3]');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);
        $mform->setDefault('function', 'moodle_group_get_groups');

        $this->add_action_buttons(true, get_string('test', 'webservice'));
    }
}
<?php

require_once($CFG->dirroot.'/lib/formslib.php');


class webservice_test_client_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        list($functions, $protocols) = $this->_customdata;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        $mform->addElement('select', 'protocol', get_string('protocol', 'webservice'), $protocols);

        $mform->addElement('select', 'function', get_string('function', 'webservice'), $functions);

        $this->add_action_buttons(false, get_string('select'));
    }
}

// === Test client forms ===

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

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }
}

class moodle_group_create_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $mform->addElement('text', 'wsusername', 'wsusername');
        $mform->addElement('text', 'wspassword', 'wspassword');
        $mform->addElement('text', 'courseid', 'courseid');
        $mform->addElement('text', 'name', 'name');
        $mform->addElement('text', 'description', 'description');
        $mform->addElement('text', 'enrolmentkey', 'enrolmentkey');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }
}

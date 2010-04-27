<?php

require_once($CFG->dirroot.'/lib/formslib.php');

class plagiarism_setup_form extends moodleform {

/// Define the form
    function definition () {
        global $CFG;

        $mform =& $this->_form;
        $choices = array('No','Yes');
        $mform->addElement('html', get_string('tiiexplain', 'plagiarism'));
        $mform->addElement('checkbox', 'turnitin_use', get_string('usetii', 'plagiarism'));

        $mform->addElement('text', 'turnitin_api', get_string('tiiapi', 'plagiarism'));
        $mform->addElement('static','turnitin_api_description', '', get_string('configtiiapi', 'plagiarism'));
        $mform->addRule('turnitin_api', null, 'required', null, 'client');
        $mform->setDefault('turnitin_api', 'https://api.turnitin.com/api.asp');

        $mform->addElement('text', 'turnitin_accountid', get_string('tiiaccountid', 'plagiarism'));
        $mform->addElement('static','turnitin_accountid_description', '', get_string('configtiiaccountid', 'plagiarism'));
        $mform->addRule('turnitin_accountid', null, 'numeric', null, 'client');

        $mform->addElement('passwordunmask', 'turnitin_secretkey', get_string('tiisecretkey', 'plagiarism'));
        $mform->addElement('static','turnitin_secretkey_description', '', get_string('configtiisecretkey', 'plagiarism'));
        $mform->addRule('turnitin_secretkey', null, 'required', null, 'client');

        $mform->addElement('checkbox', 'turnitin_senduseremail', get_string('tiisenduseremail', 'plagiarism'));
        $mform->addElement('static','turnitin_senduseremail_description', '', get_string('config_tiisenduseremail', 'plagiarism'));

        $mform->addElement('checkbox', 'turnitin_enablegrademark', get_string('tiienablegrademark', 'plagiarism'));
        $mform->addElement('static','turnitin_enablegrademark_description', '', get_string('config_tiienablegrademark', 'plagiarism'));

        $mform->addElement('text', 'turnitin_emailprefix', get_string('tiiemailprefix', 'plagiarism'));
        $mform->addElement('static','turnitin_emailprefix_description', '', get_string('configtiiemailprefix', 'plagiarism'));
        $mform->disabledIf('turnitin_emailprefix', 'turnitin_senduseremail', 'checked');

        $mform->addElement('text', 'turnitin_courseprefix', get_string('tiicourseprefix', 'plagiarism'));
        $mform->addElement('static','turnitin_courseprefix_description', '', get_string('configtiicourseprefix', 'plagiarism'));
        $mform->addRule('turnitin_courseprefix', null, 'required', null, 'client');

        $mform->addElement('text', 'turnitin_userid', get_string('username'));
        $mform->addElement('static','turnitin_userid_description', '', get_string('configtiiuserid', 'plagiarism'));
        $mform->addRule('turnitin_userid', null, 'required', null, 'client');

        $mform->addElement('text', 'turnitin_email', get_string('email'));
        $mform->addElement('static','turnitin_email_description', '', get_string('configtiiemail', 'plagiarism'));
        $mform->addRule('turnitin_email', null, 'email', null, 'client');
        $mform->addRule('turnitin_email', null, 'required', null, 'client');

        $mform->addElement('text', 'turnitin_firstname', get_string('firstname'));
        $mform->addElement('static','turnitin_firstname_description', '', get_string('configtiifirstname', 'plagiarism'));
        $mform->addRule('turnitin_firstname', null, 'required', null, 'client');

        $mform->addElement('text', 'turnitin_lastname', get_string('lastname'));
        $mform->addElement('static','turnitin_lastname_description', '', get_string('configtiilastname', 'plagiarism'));
        $mform->addRule('turnitin_lastname', null, 'required', null, 'client');

        $mform->addElement('textarea', 'turnitin_student_disclosure', get_string('studentdisclosure','plagiarism'),'wrap="virtual" rows="6" cols="50"');
        $mform->addElement('static','turnitin_student_disclosure_description', '', get_string('configstudentdisclosure','plagiarism'));
        $mform->setDefault('turnitin_student_disclosure', get_string('studentdisclosuredefault','plagiarism'));

        $this->add_action_buttons(true);
    }
}

class plagiarism_defaults_form extends moodleform {

/// Define the form
    function definition () {
        $mform =& $this->_form;
        plagiarism_get_form_elements($mform);
        $this->add_action_buttons(true);
    }
}


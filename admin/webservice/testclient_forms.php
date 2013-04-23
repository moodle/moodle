<?php

require_once($CFG->libdir.'/formslib.php');


class webservice_test_client_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        list($functions, $protocols) = $this->_customdata;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        $authmethod = array('simple' => 'simple', 'token' => 'token');
        $mform->addElement('select', 'authmethod', get_string('authmethod', 'webservice'), $authmethod);
        $mform->setType('simple', PARAM_ALPHA);

        $mform->addElement('select', 'protocol', get_string('protocol', 'webservice'), $protocols);
        $mform->setType('protocol', PARAM_ALPHA);

        $mform->addElement('select', 'function', get_string('function', 'webservice'), $functions);
        $mform->setType('function', PARAM_PLUGIN);

        $this->add_action_buttons(false, get_string('select'));
    }
}

// === Test client forms ===

class moodle_user_create_users_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        /// specific to the create users function
        $mform->addElement('text', 'username', 'username');
        $mform->setType('username', PARAM_USERNAME);
        $mform->addElement('text', 'password', 'password');
        $mform->setType('password', PARAM_RAW);
        $mform->addElement('text', 'firstname', 'firstname');
        $mform->setType('firstname', PARAM_RAW);
        $mform->addElement('text', 'lastname', 'lastname');
        $mform->setType('lastname', PARAM_RAW);
        $mform->addElement('text', 'email', 'email');
        $mform->setType('email', PARAM_EMAIL);

        $mform->addElement('text', 'customfieldtype', 'customfieldtype');
        $mform->setType('customfieldtype', PARAM_RAW);
        $mform->addElement('text', 'customfieldvalue', 'customfieldvalue');
        $mform->setType('customfieldvalue', PARAM_RAW);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);



        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        //set customfields
        if (!empty($data->customfieldtype)) {
            $data->customfields = array(array('type' => $data->customfieldtype, 'value' => $data->customfieldvalue));
        }

        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);
        unset($data->customfieldtype);
        unset($data->customfieldvalue);

        $params = array();
        $params['users'] = array();
        $params['users'][] = (array)$data;

        return $params;
    }
}


class moodle_user_update_users_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);

        /// specific to the create users function
        $mform->addElement('text', 'id', 'id');
        $mform->addRule('id', get_string('required'), 'required', null, 'client');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('text', 'username', 'username');
        $mform->setType('username', PARAM_USERNAME);
        $mform->addElement('text', 'password', 'password');
        $mform->setType('password', PARAM_RAW);
        $mform->addElement('text', 'firstname', 'firstname');
        $mform->setType('firstname', PARAM_RAW);
        $mform->addElement('text', 'lastname', 'lastname');
        $mform->setType('lastname', PARAM_RAW);
        $mform->addElement('text', 'email', 'email');
        $mform->setType('email', PARAM_EMAIL);


        $mform->addElement('text', 'customfieldtype', 'customfieldtype');
        $mform->setType('customfieldtype', PARAM_RAW);
        $mform->addElement('text', 'customfieldvalue', 'customfieldvalue');
        $mform->setType('customfieldvalue', PARAM_RAW);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);



        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }

        //set customfields
        if (!empty($data->customfieldtype)) {
            $data->customfields = array(array('type' => $data->customfieldtype, 'value' => $data->customfieldvalue));
        }

        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);
        unset($data->customfieldtype);
        unset($data->customfieldvalue);

        foreach($data as $key => $value) {
            if (empty($value)) {
                 unset($data->{$key});
            }
        }

        $params = array();
        $params['users'] = array();
        $params['users'][] = (array)$data;

        return $params;
    }
}


class moodle_user_delete_users_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);

        /// beginning of specific code to the create users function
        $mform->addElement('text', 'userids[0]', 'userids[0]');
        $mform->addElement('text', 'userids[1]', 'userids[1]');
        $mform->addElement('text', 'userids[2]', 'userids[2]');
        $mform->addElement('text', 'userids[3]', 'userids[3]');
        $mform->setType('userids', PARAM_INT);
        /// end of specific code to the create users function

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        ///  beginning of specific code to the create users form
        $params = array();
        $params['userids'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->userids[$i])) {
                continue;
            }
            $params['userids'][] = $data->userids[$i];
        }
        /// end of specific code to the create users function

        return $params;
    }
}


class moodle_user_get_users_by_id_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);

        /// beginning of specific code to the create users function
        $mform->addElement('text', 'userids[0]', 'userids[0]');
        $mform->addElement('text', 'userids[1]', 'userids[1]');
        $mform->addElement('text', 'userids[2]', 'userids[2]');
        $mform->addElement('text', 'userids[3]', 'userids[3]');
        $mform->setType('userids', PARAM_INT);
        /// end of specific code to the create users function

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);



        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        ///  beginning of specific code to the create users form
        $params = array();
        $params['userids'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->userids[$i])) {
                continue;
            }
            $params['userids'][] = $data->userids[$i];
        }
        /// end of specific code to the create users function

        return $params;
    }
}

class moodle_group_create_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;


        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);

        $mform->addElement('text', 'courseid', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('text', 'name', 'name');
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('text', 'description', 'description');
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('text', 'enrolmentkey', 'enrolmentkey');
        $mform->setType('enrolmentkey', PARAM_RAW);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);



        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['groups'] = array();
        $params['groups'][] = (array)$data;

        return $params;
    }
}

class moodle_group_get_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'groupids[0]', 'groupids[0]');
        $mform->addElement('text', 'groupids[1]', 'groupids[1]');
        $mform->addElement('text', 'groupids[2]', 'groupids[2]');
        $mform->addElement('text', 'groupids[3]', 'groupids[3]');
        $mform->setType('groupids', PARAM_INT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['groupids'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->groupids[$i])) {
                continue;
            }
            $params['groupids'][] = $data->groupids[$i];
        }

        return $params;
    }
}

class moodle_group_get_course_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'courseid', 'courseid');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['courseid'] = $data->courseid;

        return $params;
    }
}

class moodle_group_delete_groups_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'groupids[0]', 'groupids[0]');
        $mform->addElement('text', 'groupids[1]', 'groupids[1]');
        $mform->addElement('text', 'groupids[2]', 'groupids[2]');
        $mform->addElement('text', 'groupids[3]', 'groupids[3]');
        $mform->setType('groupids', PARAM_INT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $mform->addElement('static', 'warning', '', get_string('executewarnign', 'webservice'));

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['groupids'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->groupids[$i])) {
                continue;
            }
            $params['groupids'][] = $data->groupids[$i];
        }

        return $params;
    }
}

class moodle_group_get_groupmembers_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'groupids[0]', 'groupids[0]');
        $mform->addElement('text', 'groupids[1]', 'groupids[1]');
        $mform->addElement('text', 'groupids[2]', 'groupids[2]');
        $mform->addElement('text', 'groupids[3]', 'groupids[3]');
        $mform->setType('groupids', PARAM_INT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['groupids'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->groupids[$i])) {
                continue;
            }
            $params['groupids'][] = $data->groupids[$i];
        }

        return $params;
    }
}

class moodle_group_add_groupmembers_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
        $mform->addElement('text', 'userid[0]', 'userid[0]');
        $mform->addElement('text', 'groupid[0]', 'groupid[0]');
        $mform->addElement('text', 'userid[1]', 'userid[1]');
        $mform->addElement('text', 'groupid[1]', 'groupid[1]');
        $mform->setType('userid', PARAM_INT);
        $mform->setType('groupids', PARAM_INT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['members'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->groupid[$i]) or empty($data->userid[$i])) {
                continue;
            }
            $params['members'][] = array('userid'=>$data->userid[$i], 'groupid'=>$data->groupid[$i]);
        }

        return $params;
    }
}

class moodle_group_delete_groupmembers_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        //note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'userid[0]', 'userid[0]');
        $mform->addElement('text', 'groupid[0]', 'groupid[0]');
        $mform->addElement('text', 'userid[1]', 'userid[1]');
        $mform->addElement('text', 'groupid[1]', 'groupid[1]');
        $mform->setType('userid', PARAM_INT);
        $mform->setType('groupids', PARAM_INT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // remove unused from form data
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['members'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->groupid[$i]) or empty($data->userid[$i])) {
                continue;
            }
            $params['members'][] = array('userid'=>$data->userid[$i], 'groupid'=>$data->groupid[$i]);
        }

        return $params;
    }
}

/**
 * Form class for create_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_create_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        // Note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters.
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'name[0]', 'name[0]');
        $mform->addElement('text', 'parent[0]', 'parent[0]');
        $mform->addElement('text', 'idnumber[0]', 'idnumber[0]');
        $mform->addElement('text', 'description[0]', 'description[0]');
        $mform->addElement('text', 'name[1]', 'name[1]');
        $mform->addElement('text', 'parent[1]', 'parent[1]');
        $mform->addElement('text', 'idnumber[1]', 'idnumber[1]');
        $mform->addElement('text', 'description[1]', 'description[1]');
        $mform->setType('name', PARAM_TEXT);
        $mform->setType('parent', PARAM_INT);
        $mform->setType('idnumber', PARAM_RAW);
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->name[$i]) or empty($data->parent[$i])) {
                continue;
            }
            $params['categories'][] = array('name'=>$data->name[$i], 'parent'=>$data->parent[$i],
                                            'idnumber'=>$data->idnumber[$i], 'description'=>$data->description[$i]);
        }
        return $params;
    }
}

/**
 * Form class for delete_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_delete_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        // Note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters.
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'id[0]', 'id[0]');
        $mform->addElement('text', 'newparent[0]', 'newparent[0]');
        $mform->addElement('text', 'recursive[0]', 'recursive[0]');
        $mform->addElement('text', 'id[1]', 'id[1]');
        $mform->addElement('text', 'newparent[1]', 'newparent[1]');
        $mform->addElement('text', 'recursive[1]', 'recursive[1]');
        $mform->setType('id', PARAM_INT);
        $mform->setType('newparent', PARAM_INT);
        $mform->setType('recursive', PARAM_BOOL);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {
            if (empty($data->id[$i])) {
                continue;
            }
            $attrs = array();
            $attrs['id'] = $data->id[$i];
            if (!empty($data->newparent[$i])) {
                $attrs['newparent'] = $data->newparent[$i];
            }
            if (!empty($data->recursive[$i])) {
                $attrs['recursive'] = $data->recursive[$i];
            }
            $params['categories'][] = $attrs;
        }
        return $params;
    }
}

/**
 * Form class for create_categories() web service function test.
 *
 * @package   core_webservice
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2012 Fabio Souto
 */
class core_course_update_categories_form extends moodleform {
    /**
     * The form definition.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'wstestclienthdr', get_string('testclient', 'webservice'));

        // Note: these values are intentionally PARAM_RAW - we want users to test any rubbish as parameters.
        $data = $this->_customdata;
        if ($data['authmethod'] == 'simple') {
            $mform->addElement('text', 'wsusername', 'wsusername');
            $mform->setType('wsusername', PARAM_USERNAME);
            $mform->addElement('text', 'wspassword', 'wspassword');
            $mform->setType('wspassword', PARAM_RAW);
        } else if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
            $mform->setType('token', PARAM_RAW_TRIMMED);
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_ALPHA);
        $mform->addElement('text', 'id[0]', 'id[0]');
        $mform->addElement('text', 'name[0]', 'name[0]');
        $mform->addElement('text', 'parent[0]', 'parent[0]');
        $mform->addElement('text', 'idnumber[0]', 'idnumber[0]');
        $mform->addElement('text', 'description[0]', 'description[0]');
        $mform->addElement('text', 'id[1]', 'id[1]');
        $mform->addElement('text', 'name[1]', 'name[1]');
        $mform->addElement('text', 'parent[1]', 'parent[1]');
        $mform->addElement('text', 'idnumber[1]', 'idnumber[1]');
        $mform->addElement('text', 'description[1]', 'description[1]');
        $mform->setType('id', PARAM_INT);
        $mform->setType('name', PARAM_TEXT);
        $mform->setType('parent', PARAM_INT);
        $mform->setType('idnumber', PARAM_RAW);
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_PLUGIN);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('execute', 'webservice'));
    }

    /**
     * Get the parameters that the user submitted using the form.
     * @return array|null
     */
    public function get_params() {
        if (!$data = $this->get_data()) {
            return null;
        }
        // Remove unused from form data.
        unset($data->submitbutton);
        unset($data->protocol);
        unset($data->function);
        unset($data->wsusername);
        unset($data->wspassword);
        unset($data->token);
        unset($data->authmethod);

        $params = array();
        $params['categories'] = array();
        for ($i=0; $i<10; $i++) {

            if (empty($data->id[$i])) {
                continue;
            }
            $attrs = array();
            $attrs['id'] = $data->id[$i];
            if (!empty($data->name[$i])) {
                $attrs['name'] = $data->name[$i];
            }
            if (!empty($data->parent[$i])) {
                $attrs['parent'] = $data->parent[$i];
            }
            if (!empty($data->idnumber[$i])) {
                $attrs['idnumber'] = $data->idnumber[$i];
            }
            if (!empty($data->description[$i])) {
                $attrs['description'] = $data->description[$i];
            }
            $params['categories'][] = $attrs;
        }
        return $params;
    }
}
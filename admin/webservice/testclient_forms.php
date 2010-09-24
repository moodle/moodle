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

        $mform->addElement('select', 'protocol', get_string('protocol', 'webservice'), $protocols);

        $mform->addElement('select', 'function', get_string('function', 'webservice'), $functions);

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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        /// specific to the create users function
        $mform->addElement('text', 'username', 'username');
        $mform->addElement('text', 'password', 'password');
        $mform->addElement('text', 'firstname', 'firstname');
        $mform->addElement('text', 'lastname', 'lastname');
        $mform->addElement('text', 'email', 'email');

        $mform->addElement('text', 'customfieldtype', 'customfieldtype');
        $mform->addElement('text', 'customfieldvalue', 'customfieldvalue');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);



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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        /// specific to the create users function
        $mform->addElement('text', 'id', 'id');
        $mform->addRule('id', get_string('required'), 'required', null, 'client');
        $mform->addElement('text', 'username', 'username');
        $mform->addElement('text', 'password', 'password');
        $mform->addElement('text', 'firstname', 'firstname');
        $mform->addElement('text', 'lastname', 'lastname');
        $mform->addElement('text', 'email', 'email');


        $mform->addElement('text', 'customfieldtype', 'customfieldtype');
        $mform->addElement('text', 'customfieldvalue', 'customfieldvalue');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);



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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        /// beginning of specific code to the create users function
        $mform->addElement('text', 'userids[0]', 'userids[0]');
        $mform->addElement('text', 'userids[1]', 'userids[1]');
        $mform->addElement('text', 'userids[2]', 'userids[2]');
        $mform->addElement('text', 'userids[3]', 'userids[3]');
        /// end of specific code to the create users function

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);



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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

        /// beginning of specific code to the create users function
        $mform->addElement('text', 'userids[0]', 'userids[0]');
        $mform->addElement('text', 'userids[1]', 'userids[1]');
        $mform->addElement('text', 'userids[2]', 'userids[2]');
        $mform->addElement('text', 'userids[3]', 'userids[3]');
        /// end of specific code to the create users function

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);



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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);

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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
        $mform->addElement('text', 'courseid', 'courseid');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
        $mform->addElement('text', 'groupids[0]', 'groupids[0]');
        $mform->addElement('text', 'groupids[1]', 'groupids[1]');
        $mform->addElement('text', 'groupids[2]', 'groupids[2]');
        $mform->addElement('text', 'groupids[3]', 'groupids[3]');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
        $mform->addElement('text', 'userid[0]', 'userid[0]');
        $mform->addElement('text', 'groupid[0]', 'groupid[0]');
        $mform->addElement('text', 'userid[1]', 'userid[1]');
        $mform->addElement('text', 'groupid[1]', 'groupid[1]');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

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
            $mform->addElement('text', 'wspassword', 'wspassword');
        } else  if ($data['authmethod'] == 'token') {
            $mform->addElement('text', 'token', 'token');
        }

        $mform->addElement('hidden', 'authmethod', $data['authmethod']);
        $mform->setType('authmethod', PARAM_SAFEDIR);
        $mform->addElement('text', 'userid[0]', 'userid[0]');
        $mform->addElement('text', 'groupid[0]', 'groupid[0]');
        $mform->addElement('text', 'userid[1]', 'userid[1]');
        $mform->addElement('text', 'groupid[1]', 'groupid[1]');

        $mform->addElement('hidden', 'function');
        $mform->setType('function', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'protocol');
        $mform->setType('protocol', PARAM_SAFEDIR);

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

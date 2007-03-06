<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class login_change_password_form extends moodleform {

    function definition() {
        global $USER;

        $mform    =& $this->_form;

        $mform->addElement('header', '', get_string('changepassword'), '');
        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

        // visible elements
        if (has_capability('moodle/user:update', $sitecontext)) {
            $mform->addElement('text', 'username', get_string('username'));
            $mform->addRule('username', get_string('required'), 'required', null, 'client');
            $mform->setType('username', PARAM_RAW);
        } else {
            $mform->addElement('hidden', 'username');
            $mform->setType('username', PARAM_RAW);
        }

        if (has_capability('moodle/user:update', $sitecontext)) {
            $mform->addElement('hidden', 'password');
            $mform->setType('username', PARAM_RAW);
        } else {
            $mform->addElement('password', 'password', get_string('oldpassword'));
            $mform->addRule('password', get_string('required'), 'required', null, 'client');
            $mform->setType('password', PARAM_RAW);
        }

        $mform->addElement('password', 'newpassword1', get_string('newpassword'));
        $mform->addRule('newpassword1', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword1', PARAM_RAW);

        $mform->addElement('password', 'newpassword2', get_string('newpassword').' ('.get_String('again').')');
        $mform->addRule('newpassword2', get_string('required'), 'required', null, 'client');
        $mform->setType('newpassword2', PARAM_RAW);


        // hidden optional params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // buttons
        if (get_user_preferences('auth_forcepasswordchange')) {
            $this->add_action_buttons(false);
        } else {
            $this->add_action_buttons(true);
        }
    }

/// perform extra password change validation
    function validation($data){
        global $USER;
        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        $errors = array();

        if (has_capability('moodle/user:update', $sitecontext)) {
            if (!$user = get_record('user', 'username', $data['username'])) {
                $errors['username'] = get_string('invalidlogin');
                return $errors;
            }
        } else {
            update_login_count();

            // ignore submitted username
            if (!$user = authenticate_user_login($USER->username, $data['password'])) {
                $errors['password'] = get_string('invalidlogin');
                return $errors;
            }

            reset_login_count();
        }

        // can not change guest user password
        if ($user->username == 'guest') {
            $errors['username'] = get_string('invalidlogin');
            return $errors;
        }

        // can not change password of primary admin
        $mainadmin = get_admin();
        if ($user->id == $mainadmin->id and $USER->id != $mainadmin->id) {
            $errors['username'] = get_string('adminprimarynoedit');
            return $errors;
        }

        if ($data['newpassword1'] <> $data['newpassword2']) {
            $errors['newpassword1'] = get_string('passwordsdiffer');
            $errors['newpassword2'] = get_string('passwordsdiffer');
            return $errors;
        } else if (!has_capability('moodle/user:update', $sitecontext) and ($data['password'] == $data['newpassword1'])){
            $errors['newpassword1'] = get_string('mustchangepassword');
            $errors['newpassword2'] = get_string('mustchangepassword');
            return $errors;
        }

        return true;
    }
}
?>

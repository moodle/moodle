<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class login_forgot_password_form extends moodleform {

    function definition() {
        $mform    =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        $mform->addElement('header', '', get_string('passwordforgotten'), '');

        $mform->addElement('text', 'username', get_string('username'));
        $mform->setType('username', PARAM_RAW);

        $mform->addElement('text', 'email', get_string('email'));
        $mform->setType('email', PARAM_RAW);

        $this->add_action_buttons(true, get_string('ok'));
    }

    function validation($data, $files) {
        global $CFG;

        $errors = parent::validation($data, $files);

        if ((!empty($data['username']) and !empty($data['email'])) or (empty($data['username']) and empty($data['email']))) {
            $errors['username'] = get_string('usernameoremail');
            $errors['email']    = get_string('usernameoremail');

        } else if (!empty($data['email'])) {
            if (!validate_email($data['email'])) {
                $errors['email'] = get_string('invalidemail');

            } else if (count_records('user', 'email', $data['email']) > 1) {
                $errors['email'] = get_string('forgottenduplicate');

            } else {
                if ($user = get_complete_user_data('email', $data['email'])) {
                    if (empty($user->confirmed)) {
                        $errors['email'] = get_string('confirmednot');
                    }
                }
                if (!$user and empty($CFG->protectusernames)) {
                    $errors['email'] = get_string('emailnotfound');
                }
            }

        } else {
            if ($user = get_complete_user_data('username', $data['username'])) {
                if (empty($user->confirmed)) {
                    $errors['email'] = get_string('confirmednot');
                }
            }
            if (!$user and empty($CFG->protectusernames)) {
                $errors['username'] = get_string('usernamenotfound');
            }
        }

        return $errors;
    }

}

?>
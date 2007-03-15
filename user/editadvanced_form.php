<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class user_editadvanced_form extends moodleform {

    // Define the form
    function definition() {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;
        $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'course', $COURSE->id);

        /// Print the required moodle fields first
        $mform->addElement('header', 'moodle', $strrequired);

        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
        $mform->addRule('username', $strrequired, 'required', null, 'client');
        $mform->setType('username', PARAM_RAW);

        $modules = get_list_of_plugins('auth');
        $auth_options = array();
        foreach ($modules as $module) {
            $auth_options[$module] = get_string("auth_$module"."title", "auth");
        }
        $mform->addElement('select', 'auth', get_string('chooseauthmethod','auth'), $auth_options);
        $mform->setHelpButton('auth', array('authchange', get_string('chooseauthmethod','auth')));
        $mform->setAdvanced('auth');

        $mform->addElement('text', 'newpassword', get_string('newpassword'), 'size="20"');
        $mform->setType('newpassword', PARAM_RAW);
        //TODO: add missing help - empty means no change

        $mform->addElement('checkbox', 'preference_auth_forcepasswordchange', get_string('forcepasswordchange'));
        //TODO: add missing help - user will be forced to change password

        /// shared fields
        useredit_shared_definition($mform);

        /// Next the customisable profile fields
        profile_definition($mform);

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $USER, $CFG;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');
        $user = get_record('user', 'id', $userid);

        // user can not change own auth method
        if ($userid == $USER->id) {
            $mform->hardFreeze('auth');
            $mform->hardFreeze('preference_auth_forcepasswordchange');
        }

        // admin must choose some password and supply correct email
        if (!empty($USER->newadminuser)) {
            $mform->addRule('newpassword', get_string('required'), 'required', null, 'client');

            $email_el =& $mform->getElement('email');
            if ($email_el->getValue() == 'root@localhost') {
                $email_el->setValue('');
            }
        }

        // require password for new users
        if ($userid == -1) {
            $mform->addRule('newpassword', get_string('required'), 'required', null, 'client');
        }

        // print picture
        if (!empty($CFG->gdversion)) {
            $image_el =& $mform->getElement('currentpicture');
            if ($user and $user->picture) {
                $image_el->setValue(print_user_picture($user->id, SITEID, $user->picture, 64, true, false, '', true));
            } else {
                $image_el->setValue(get_string('none'));
            }
        }

        /// Next the customisable profile fields
        profile_definition_after_data($mform);
    }

    function validation($usernew) {
        global $CFG;

        $usernew = (object)$usernew;
        $usernew->username = trim($usernew->username);

        $user = get_record('user', 'id', $usernew->id);
        $err = array();

        if (empty($usernew->username)) {
            //might be only whitespace
            $err['username'] = get_string('required');
        } else if (!$user or $user->username !== $usernew->username) {
            //check new username does not exist
            if (record_exists('user', 'username', $usernew->username, 'mnethostid', $CFG->mnet_localhost_id)) {
                $err['username'] = get_string('usernameexists');
            }
            //check allowed characters
            if ($usernew->username !== moodle_strtolower($usernew->username)) {
                $err['username'] = get_string('usernamelowercase');
            } else {
                if (empty($CFG->extendedusernamechars)) {
                    $string = eregi_replace("[^(-\.[:alnum:])]", '', $usernew->username);
                    if ($usernew->username !== $string) {
                        $err['username'] = get_string('alphanumerical');
                    }
                }
            }
        }

        if (!$user or $user->email !== $usernew->email) {
            if (!validate_email($usernew->email)) {
                $err['email'] = get_string('invalidemail');
            } else if (record_exists('user', 'email', $usernew->email, 'mnethostid', $CFG->mnet_localhost_id)) {
                $err['email'] = get_string('emailexists');
            }
        }

        /// Next the customisable profile fields
        $err += profile_validation($usernew);

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>

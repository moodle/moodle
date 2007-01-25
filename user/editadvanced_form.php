<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class user_editadvanced_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform =& $this->_form;
        $course = $this->_customdata;
        $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'course', $course->id);

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

        require('edit_form_common.php');

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $USER, $CFG;

        $mform =& $this->_form;
        $user = get_record('user', 'id', $mform->getElementValue('id'));

        if ($user) {

            // user can not change own auth method
            if ($user->id == $USER->id) {
                $mform->hardFreeze('auth');
                $mform->hardFreeze('preference_auth_forcepasswordchange');
            }
        }

        // admin must choose some password and supply correct email
        if (!empty($USER->newadminuser)) {
            $mform->addRule('newpassword', get_string('required'), 'required', null, 'client');

            $email = $mform->getElement('email');
            if ($email->getValue() == 'root@localhost') {
                $email->setValue('');
            }
        }

        if (!empty($CFG->gdversion)) {
            $image = $mform->getElement('currentpicture');
            if ($user) {
                $image->setValue(print_user_picture($user->id, SITEID, $user->picture, 64, true, false, '', true));
            } else {
                $image->setValue(print_user_picture(0, SITEID, 0, 64, true, false, '', true));
            }
        }
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>

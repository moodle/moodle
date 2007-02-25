<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class user_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;
        $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'course', $COURSE->id);

        /// Print the required moodle fields first
        $mform->addElement('header', 'moodle', $strrequired);

        /// shared fields
        useredit_shared_definition($mform);

        /// extra settigs
        $mform->addRule('description', $strrequired, 'required', null, 'client');
        if (!empty($CFG->gdversion) and !empty($CFG->disableuserimages)) {
            $mform->removeElement('deletepicture');
            $mform->removeElement('imagefile');
            $mform->removeElement('imagealt');
        }

        /// Next the customisable profile fields
        profile_definition($mform);

        $this->add_action_buttons(false, get_string('updatemyprofile'));
    }

    function definition_after_data() {
        global $USER, $CFG;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        if ($user = get_record('user', 'id', $userid)) {

            // print picture
            if (!empty($CFG->gdversion)) {
                $image_el = $mform->getElement('currentpicture');
                if ($user and $user->picture) {
                    $image_el->setValue(print_user_picture($user->id, SITEID, $user->picture, 64,true,false,'',true));
                } else {
                    $image_el->setValue(get_string('none'));
                }
            }

            /// disable fields that are locked by auth plugins
            $fields = get_user_fieldnames();
            $freezefields = array();
            $authplugin = get_auth_plugin($user->auth);
            foreach ($fields as $field) {
                if (!$mform->elementExists($field)) {
                    continue;
                }
                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $freezefields[] = $field;
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $user->$field != '') {
                        $freezefields[] = $field;
                    }
                }
            }
            $mform->hardFreeze($freezefields);
        }

        /// Next the customisable profile fields
        profile_definition_after_data($mform);

    }

    function validation ($usernew) {
        global $CFG;

        $usernew = (object)$usernew;
        $user    = get_record('user', 'id', $usernew->id);
        $err     = array();

        // validate email
        if (!validate_email($usernew->email)) {
            $err['email'] = get_string('invalidemail');
        } else if (($usernew->email !== $user->email) and record_exists('user', 'email', $usernew->email, 'mnethostid', $CFG->mnet_localhost_id)) {
            $err['email'] = get_string('emailexists');
        }

        if ($usernew->email === $user->email and over_bounce_threshold($user)) {
            $err['email'] = get_string('toomanybounces');
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

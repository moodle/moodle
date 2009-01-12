<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class user_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE;

        $mform =& $this->_form;
        $this->set_upload_manager(new upload_manager('imagefile', false, false, null, false, 0, true, true, false));
        //Accessibility: "Required" is bad legend text.
        $strgeneral  = get_string('general');
        $strrequired = get_string('required');

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'course', $COURSE->id);

        /// Print the required moodle fields first
        $mform->addElement('header', 'moodle', $strgeneral);

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
        global $CFG;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        // if language does not exist, use site default lang
        if ($langsel = $mform->getElementValue('lang')) {
            $lang = reset($langsel);
            if (!file_exists($CFG->dataroot.'/lang/'.$lang) and 
              !file_exists($CFG->dirroot .'/lang/'.$lang)) {
                $lang_el =& $mform->getElement('lang');
                $lang_el->setValue($CFG->lang);
            }
        }

        if ($user = get_record('user', 'id', $userid)) {

            // remove description - this must be here because
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !record_exists('role_assignments', 'userid', $user->id)) {
                if ($mform->elementExists('description')) {
                    $mform->removeElement('description');
                }
            }

            // print picture
            if (!empty($CFG->gdversion)) {
                $image_el =& $mform->getElement('currentpicture');
                if ($user and $user->picture) {
                    $image_el->setValue(print_user_picture($user->id, SITEID, $user->picture, 64,true,false,'',true));
                } else {
                    $image_el->setValue(get_string('none'));
                }
            }

            /// disable fields that are locked by auth plugins
            $fields = get_user_fieldnames();
            $authplugin = get_auth_plugin($user->auth);
            foreach ($fields as $field) {
                if (!$mform->elementExists($field)) {
                    continue;
                }
                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $mform->hardFreeze($field);
                        $mform->setConstant($field, $user->$field);
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $user->$field != '') {
                        $mform->hardFreeze($field);
                        $mform->setConstant($field, $user->$field);
                    }
                }
            }
            
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
        } else if ((stripslashes($usernew->email) !== $user->email) and record_exists('user', 'email', $usernew->email, 'mnethostid', $CFG->mnet_localhost_id)) {
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

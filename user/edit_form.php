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
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

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
            // missing _utf8 in language, add it before further processing. MDL-11829 MDL-16845
            if (strpos($lang, '_utf8') === false) {
                $lang = $lang . '_utf8';
                $lang_el =& $mform->getElement('lang');
                $lang_el->setValue($lang);
            }
            // check lang exists
            if (!file_exists($CFG->dataroot.'/lang/'.$lang) and
              !file_exists($CFG->dirroot .'/lang/'.$lang)) {
                $lang_el =& $mform->getElement('lang');
                $lang_el->setValue($CFG->lang);
            }
        }


        if ($user = get_record('user', 'id', $userid)) {
            // remove description
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !record_exists('role_assignments', 'userid', $userid)) {
                $mform->removeElement('description');
            }

            // print picture
            if (!empty($CFG->gdversion)) {
                $image_el =& $mform->getElement('currentpicture');
                if ($user and $user->picture) {
                    $image_el->setValue(print_user_picture($user, SITEID, $user->picture, 64,true,false,'',true));
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

            /// Next the customisable profile fields
            profile_definition_after_data($mform, $user->id);

        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    function validation($usernew, $files) {
        global $CFG;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;
        $user    = get_record('user', 'id', $usernew->id);

        // validate email
        if (!isset($usernew->email)) {
            // mail not confirmed yet
        } else if (!validate_email(stripslashes($usernew->email))) {
            $errors['email'] = get_string('invalidemail');
        } else if ((stripslashes($usernew->email) !== $user->email) and record_exists('user', 'email', $usernew->email, 'mnethostid', $CFG->mnet_localhost_id)) {
            $errors['email'] = get_string('emailexists');
        }

        if (isset($usernew->email) and stripslashes($usernew->email) === $user->email and over_bounce_threshold($user)) {
            $errors['email'] = get_string('toomanybounces');
        }

        if (isset($usernew->email) and !empty($CFG->verifychangedemail) and !isset($errors['email']) and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $errorstr = email_is_not_allowed(stripslashes($usernew->email));
            if ($errorstr !== false) {
                $errors['email'] = $errorstr;
            }
        }

        /// Next the customisable profile fields
        $errors += profile_validation($usernew, $files);

        return $errors;
    }

    function get_um() {
        return $this->_upload_manager;
    }
}

?>

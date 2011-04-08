<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class user_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $CFG, $COURSE;

        $mform =& $this->_form;
        if (is_array($this->_customdata) && array_key_exists('editoroptions', $this->_customdata)) {
            $editoroptions = $this->_customdata['editoroptions'];
        } else {
            $editoroptions = null;
        }
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
        useredit_shared_definition($mform, $editoroptions);

        /// extra settigs
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
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;
        $userid = $mform->getElementValue('id');

        // if language does not exist, use site default lang
        if ($langsel = $mform->getElementValue('lang')) {
            $lang = reset($langsel);
            // check lang exists
            if (!get_string_manager()->translation_exists($lang, false)) {
                $lang_el =& $mform->getElement('lang');
                $lang_el->setValue($CFG->lang);
            }
        }


        if ($user = $DB->get_record('user', array('id'=>$userid))) {

            // remove description
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid'=>$userid))) {
                $mform->removeElement('description_editor');
            }

            // print picture
            if (!empty($CFG->gdversion)) {
                $image_el =& $mform->getElement('currentpicture');
                if ($user and $user->picture) {
                    $image_el->setValue($OUTPUT->user_picture($user, array('courseid'=>SITEID, 'size'=>64)));
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
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object)$usernew;
        $user    = $DB->get_record('user', array('id'=>$usernew->id));

        // validate email
        if (!isset($usernew->email)) {
            // mail not confirmed yet
        } else if (!validate_email($usernew->email)) {
            $errors['email'] = get_string('invalidemail');
        } else if (($usernew->email !== $user->email) and $DB->record_exists('user', array('email'=>$usernew->email, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            $errors['email'] = get_string('emailexists');
        }

        if (isset($usernew->email) and $usernew->email === $user->email and over_bounce_threshold($user)) {
            $errors['email'] = get_string('toomanybounces');
        }

        if (isset($usernew->email) and !empty($CFG->verifychangedemail) and !isset($errors['email']) and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM))) {
            $errorstr = email_is_not_allowed($usernew->email);
            if ($errorstr !== false) {
                $errors['email'] = $errorstr;
            }
        }

        /// Next the customisable profile fields
        $errors += profile_validation($usernew, $files);

        return $errors;
    }
}



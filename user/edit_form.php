<?php //$Id$

include_once("$CFG->dirroot/lib/formslib.php");
require_once("$CFG->dirroot/user/profile/lib.php");

class user_edit_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG;

        $mform      =& $this->_form;
        $renderer   =& $mform->defaultRenderer();
        $user       = $this->_customdata['user'];
        $course     = $this->_customdata['course'];
        $authplugin = $this->_customdata['authplugin'];

        $systemcontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        $userupdate    = has_capability('moodle/user:update', $systemcontext);
        $strrequired = get_string('required');

        $this->set_upload_manager(new upload_manager('imagefile',false,false,null,false,0,true,true));

        /// Add some extra hidden fields
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'course', $course->id);


        /// Print the required moodle fields first
        $mform->addElement('header', 'moodle', $strrequired);

        if ($userupdate) {
            $theadmin = get_admin(); // returns false during install
            $adminself = (!empty($theadmin) and ($theadmin->id == $USER->id) and ($USER->id == $user->id));
            $userauth = get_auth_plugin($user->auth);

            if ($adminself or $userauth->is_internal()) {
                $mform->addElement('text', 'username', get_string('username'), 'size="20"');
                $mform->setType('username', PARAM_MULTILANG);
                $mform->setDefault('username', '');
                $mform->addRule('username', $strrequired, 'required', null, 'client');
            } else {
                $mform->addElement('hidden', 'username');
            }

            $modules = get_list_of_plugins("auth");
            $auth_options = array();
            foreach ($modules as $module) {
                $auth_options[$module] = get_string("auth_$module"."title", "auth");
            }
            if (!$adminself) {    /// Main admin is ALWAYS default manual
                $mform->addElement('select', 'auth', get_string('chooseauthmethod','auth'),$auth_options);
                $mform->setType('auth', PARAM_ALPHANUM);
                $mform->addRule('auth', $strrequired, 'required', null, 'client');
                $mform->setHelpButton('auth', array('authchange', get_string('chooseauthmethod','auth')));
            }

            if ($adminself or $userauth->can_change_password()) {
                $newpasswordgrp = array();
                $newpasswordgrp[] = &MoodleQuickForm::createElement('text', 'newpassword', '', 'size="20"');
                $newpasswordgrp[] = &MoodleQuickForm::createElement('static', 'newpasswordtext', '', '('.get_string('leavetokeep').')');
                $mform->addGroup($newpasswordgrp, 'newpasswordgrp', get_string('newpassword'),' ',false);
                $mform->setType('newpassword', PARAM_RAW);

                if (!$adminself and $userauth->can_change_password()) {
                    if (get_user_preferences('auth_forcepasswordchange', NULL, $user->id)) {
                        $checked = ' checked="checked" ';
                    } else {
                        $checked = '';
                    }
                    $forcepasswordgrp = array();
                    $forcepasswordgrp[] = &MoodleQuickForm::createElement('checkbox', 'forcepasswordchange', '');
                    $forcepasswordgrp[] = &MoodleQuickForm::createElement('static', 'forcepasswordtext', '', '('.get_string('forcepasswordchangehelp').')');
                    $mform->addGroup($forcepasswordgrp, 'forcepasswordgrp', get_string('forcepasswordchange'));
                    $mform->setDefault('forcepasswordchange',$checked);
                }
            }
            $mform->addElement('static','username_break', '','<hr />');

        }


        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->setType('firstname', PARAM_NOTAGS);
        $mform->addRule('firstname', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="30"');
        $mform->setType('lastname', PARAM_NOTAGS);
        $mform->addRule('lastname', $strrequired, 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
        $mform->setType('email', PARAM_MULTILANG);
        $mform->addRule('email', $strrequired, 'required', null, 'client');

        $choices = array();
        $choices["0"] = get_string("emaildisplayno");
        $choices["1"] = get_string("emaildisplayyes");
        $choices["2"] = get_string("emaildisplaycourse");
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setType('maildisplay', PARAM_INT);

        $choices = array();
        $choices["0"] = get_string("emailenable");
        $choices["1"] = get_string("emaildisable");
        $mform->addElement('select', 'emailstop', get_string('emailactive'), $choices);
        $mform->setType('emailstop', PARAM_INT);

        $choices = array();
        $choices["0"] = get_string("textformat");
        $choices["1"] = get_string("htmlformat");
        $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
        $mform->setType('mailformat', PARAM_INT);
        $mform->setAdvanced('mailformat');

        if (!empty($CFG->unicodedb) && !empty($CFG->allowusermailcharset)) {
            $mailcharset = get_user_preferences('mailcharset', '0', $user->id);
            $choices = array();
            $charsets = get_list_of_charsets();
            if (!empty($CFG->sitemailcharset)) {
                $choices["0"] = get_string('site').' ('.$CFG->sitemailcharset.')';
            } else {
                $choices["0"] = get_string('site').' ('.current_charset().')';
            }
            $choices = array_merge($choices, $charsets);
            $mform->addElement('select', 'mailcharset', get_string('emailcharset'), $choices);
            $mform->setType('mailcharset', PARAM_CLEAN);
            $mform->setDefault('mailcharset', $mailcharset);
            $mform->setAdvanced('mailcharset');
        }


        $choices = array();
        $choices['0'] = get_string('emaildigestoff');
        $choices['1'] = get_string('emaildigestcomplete');
        $choices['2'] = get_string('emaildigestsubjects');
        $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
        $mform->setType('maildigest', PARAM_INT);
        $mform->setDefault('maildigest', 0);
        $mform->setAdvanced('maildigest');

        $choices = array();
        $choices["1"] = get_string("autosubscribeyes");
        $choices["0"] = get_string("autosubscribeno");
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setType('autosubscribe', PARAM_INT);
        $mform->setDefault('autosubscribe', 0);
        $mform->setAdvanced('autosubscribe');

        if (!empty($CFG->forum_trackreadposts)) {
            $choices = array();
            $choices["0"] = get_string("trackforumsno");
            $choices["1"] = get_string("trackforumsyes");
            $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
            $mform->setType('trackforums', PARAM_INT);
            $mform->setDefault('trackforums', 0);
        }
        $mform->setAdvanced('trackforums');

        if ($CFG->htmleditor) {
            $choices = array();
            $choices["0"] = get_string("texteditor");
            $choices["1"] = get_string("htmleditor");
            $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
            $mform->setType('htmleditor', PARAM_INT);
            $mform->setDefault('htmleditor', 1);
        }
        $mform->setAdvanced('htmleditor');

        $choices = array();
        $choices["0"] = get_string("ajaxno");
        $choices["1"] = get_string("ajaxyes");
        $mform->addElement('select', 'ajax', get_string('ajaxuse'), $choices);
        $mform->setType('ajax', PARAM_INT);
        $mform->setDefault('ajax', 1);
        if (empty($CFG->enableajax)) {
            $mform->hardFreeze('ajax');
        }
        $mform->setAdvanced('ajax');

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="100" size="25"');
        $mform->setType('city', PARAM_MULTILANG);
        $mform->addRule('city', $strrequired, 'required', null, 'client');


        $choices = get_list_of_countries();
        $choices= array(''=>get_string('selectacountry').'...') + $choices;
        $mform->addElement('select', 'country', get_string('selectacountry'), $choices);
        $mform->setType('country', PARAM_ALPHA);
        $mform->addRule('country', $strrequired, 'required', null, 'client');
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        }

        $choices = get_list_of_timezones();
        $choices['99'] = get_string('serverlocaltime');
        if ($CFG->forcetimezone != 99) {
            $mform->addElement('static', 'forcedtimezone', get_string('timezone'), $choices[$CFG->forcetimezone]);
        } else {
            $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
            $mform->setType('timezone', PARAM_PATH);
            $mform->setDefault('timezone', '99');
        }

        $choices = array();
        if ($choices = get_list_of_languages()) {
            $mform->addElement('select', 'lang', get_string('preferredlanguage'), $choices);
            $mform->setType('lang', PARAM_FILE);
            $mform->setDefault('lang', $CFG->lang);
        }

        if (!empty($CFG->allowuserthemes)) {
            $choices = array();
            $choices[''] = get_string('default');
            $choices += get_list_of_themes();
            $mform->addElement('select', 'theme', get_string('preferredtheme'), $choices);
            $mform->setType('theme', PARAM_ALPHANUM);
            $mform->setAdvanced('theme');
        }

        $mform->addElement('htmleditor', 'description', get_string('userdescription'));
        $mform->setType('description', PARAM_MULTILANG);
        $mform->setHelpButton('description', array('text', get_string('helptext')));
        if (!$userupdate) {
            $mform->addRule('description', $strrequired, 'required', null, 'client');
        }

        $choices = array();
        $choices["0"] = get_string("screenreaderno");
        $choices["1"] = get_string("screenreaderyes");
        $mform->addElement('select', 'screenreader', get_string('screenreaderuse'), $choices);
        $mform->setType('screenreader', PARAM_INT);
        $mform->setDefault('screenreader', 0);
        $mform->setAdvanced('screenreader');

        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
        if (!empty($CFG->gdversion) and $maxbytes and (empty($CFG->disableuserimages) or $userupdate)) {

            $mform->addElement('header', 'moodle_picture', get_string('pictureof', '', fullname($user)));

            $mform->addElement('static', 'currentpicture', get_string('currentpicture'),print_user_picture($user->id, $course->id, $user->picture, false, true, false));
            if ($user->picture) {
                $mform->addElement('checkbox', 'deletepicture', get_string('delete'));
                $mform->setDefault('deletepicture',false);
            }
            $mform->addElement('file', 'imagefile', get_string('newpicture'));
            $mform->setHelpButton('imagefile', array('picture', get_string('helppicture')));

            $mform->addElement('text', 'imagealt', get_string('imagealt'), 'maxlength="100" size="30"');
            $mform->setType('imagealt', PARAM_MULTILANG);

        }


        /// Moodle optional fields
        $mform->addElement('header', 'moodle_optional', get_string('optional', 'form'));
        $mform->setAdvanced('moodle_optional');

        $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');
        $mform->setType('url', PARAM_URL);

        $mform->addElement('text', 'icq', get_string('icqnumber'), 'maxlength="15" size="25"');
        $mform->setType('icq', PARAM_CLEAN);

        $mform->addElement('text', 'skype', get_string('skypeid'), 'maxlength="50" size="25"');
        $mform->setType('skype', PARAM_CLEAN);

        $mform->addElement('text', 'aim', get_string('aimid'), 'maxlength="50" size="25"');
        $mform->setType('aim', PARAM_CLEAN);

        $mform->addElement('text', 'yahoo', get_string('yahooid'), 'maxlength="50" size="25"');
        $mform->setType('yahoo', PARAM_CLEAN);

        $mform->addElement('text', 'msn', get_string('msnid'), 'maxlength="50" size="25"');
        $mform->setType('msn', PARAM_CLEAN);

        $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="64" size="25"');
        $mform->setType('idnumber', PARAM_MULTILANG);
        if (!$userupdate) {
            $mform->hardFreeze('idnumber');
        }

        if ($userupdate) {
            $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
            $mform->setType('institution', PARAM_MULTILANG);

            $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
            $mform->setType('department', PARAM_MULTILANG);
        }

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone1', PARAM_CLEAN);
        if (!$userupdate) {
            $mform->hardFreeze('phone1');
        }

        $mform->addElement('text', 'phone2', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone2', PARAM_CLEAN);
        if (!$userupdate) {
            $mform->hardFreeze('phone2');
        }

        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
        $mform->setType('address', PARAM_MULTILANG);
        if (!$userupdate) {
            $mform->hardFreeze('address');
        }

        /// disable fields that are locked by auth plugins
        $fields = get_user_fieldnames();
        $freezefields = array();
        foreach ($fields as $field) {
            $configvariable = 'field_lock_' . $field;
            if (isset($authplugin->config->{$configvariable}) and
                 ( $authplugin->config->{$configvariable} === 'locked' or
                   ( $authplugin->config->{$configvariable} === 'unlockedifempty' and !empty($user->$field)) ) ) {
                $freezefields[] = $field;
            }
        }
        $mform->hardFreeze($freezefields);

        /// Next the customisable categories
        if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
            foreach ($categories as $category) {
                if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {

                    $mform->addElement('header', 'category_'.$category->id, $category->name);

                    foreach ($fields as $field) {

                        require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                        $newfield = 'profile_field_'.$field->datatype;
                        $formfield = new $newfield($field->id,$user->id);
                        $formfield->display_field($mform);
                        unset($formfield);

                    }
                } /// End of $fields if
            } /// End of $categories foreach
        } /// End of $categories if

        $this->add_action_buttons(false, get_string('updatemyprofile'));

    } /// End of function

    function definition_after_data () {
        /// nothing yet
    }


/// perform some moodle validation
    function validation ($usernew) {
        global $CFG;

        $usernew = (object)$usernew;
        $user    = $this->_customdata['user'];
        $err     = array();

        if (has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            if (empty($usernew->username)) { /// We should never get this
                //$err["username"] = get_string("missingusername");
                $err["username"] = 'testing';
            } else if (record_exists("user", "username", $usernew->username, 'mnethostid', $CFG->mnet_localhost_id) and $user->username == "changeme") {
                $err["username"] = get_string("usernameexists");
            } else {
                if (empty($CFG->extendedusernamechars)) {
                    $string = eregi_replace("[^(-\.[:alnum:])]", "", $usernew->username);
                    if (strcmp($usernew->username, $string)) {
                        $err["username"] = get_string("alphanumerical");
                    }
                }
            }

            // TODO: is_internal_auth() - what, the global auth? the user auth?
            if (empty($usernew->newpassword) and empty($user->password) and is_internal_auth()) {
                $err["newpassword"] = get_string("missingpassword");
            }
            if (($usernew->newpassword == "admin") or ($user->password == md5("admin") and empty($usernew->newpassword)) ) {
                $err["newpassword"] = get_string("unsafepassword");
            }
        }


        if (! validate_email($usernew->email)) {
            $err["email"] = get_string("invalidemail");
        } else if ($otheruser = get_record("user", "email", $usernew->email)) {
            if ($otheruser->id <> $user->id) {
                $err["email"] = get_string("emailexists");
            }
        }
        if (empty($err["email"]) and !has_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM, SITEID))) {
            if ($error = email_is_not_allowed($usernew->email)) {
                $err["email"] = $error;
            }
        }

        /// And now we validate the custom data fields

        if ($categories = get_records_select('user_info_category', '', 'sortorder ASC')) {
            foreach ($categories as $category) {

                if ($fields = get_records_select('user_info_field', "categoryid=$category->id", 'sortorder ASC')) {
                    foreach ($fields as $field) {

                        require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                        $newfield = 'profile_field_'.$field->datatype;
                        $formfield = new $newfield($field->id,$user->id);

                        if (isset($usernew->{$formfield->fieldname})) {

                            $errorstr = $formfield->validate_data($usernew->{$formfield->fieldname});
                            if (!empty($errorstr)) {
                                $err[$formfield->fieldname] = $errorstr;
                            }

                        }

                        unset($formfield);

                    }
                } /// End of $fields if
            } /// End of $categories foreach
        } /// End of $categories if

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }
}

?>

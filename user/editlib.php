<?php

function cancel_email_update($userid) {
    unset_user_preference('newemail', $userid);
    unset_user_preference('newemailkey', $userid);
    unset_user_preference('newemailattemptsleft', $userid);
}

function useredit_load_preferences(&$user, $reload=true) {
    global $USER;

    if (!empty($user->id)) {
        if ($reload and $USER->id == $user->id) {
            // reload preferences in case it was changed in other session
            unset($USER->preference);
        }

        if ($preferences = get_user_preferences(null, null, $user->id)) {
            foreach($preferences as $name=>$value) {
                $user->{'preference_'.$name} = $value;
            }
        }
    }
}

function useredit_update_user_preference($usernew) {
    $ua = (array)$usernew;
    foreach($ua as $key=>$value) {
        if (strpos($key, 'preference_') === 0) {
            $name = substr($key, strlen('preference_'));
            set_user_preference($name, $value, $usernew->id);
        }
    }
}

function useredit_update_picture(&$usernew, $userform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/gdlib.php");

    $fs = get_file_storage();
    $context = get_context_instance(CONTEXT_USER, $usernew->id, MUST_EXIST);

    if (isset($usernew->deletepicture) and $usernew->deletepicture) {
        $fs->delete_area_files($context->id, 'user', 'icon'); // drop all areas
        $DB->set_field('user', 'picture', 0, array('id'=>$usernew->id));

    } else if ($iconfile = $userform->save_temp_file('imagefile')) {
        if (process_new_icon($context, 'user', 'icon', 0, $iconfile)) {
            $DB->set_field('user', 'picture', 1, array('id'=>$usernew->id));
        }
        @unlink($iconfile);
    }
}

function useredit_update_bounces($user, $usernew) {
    if (!isset($usernew->email)) {
        //locked field
        return;
    }
    if (!isset($user->email) || $user->email !== $usernew->email) {
        set_bounce_count($usernew,true);
        set_send_count($usernew,true);
    }
}

function useredit_update_trackforums($user, $usernew) {
    global $CFG;
    if (!isset($usernew->trackforums)) {
        //locked field
        return;
    }
    if ((!isset($user->trackforums) || ($usernew->trackforums != $user->trackforums)) and !$usernew->trackforums) {
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        forum_tp_delete_read_records($usernew->id);
    }
}

function useredit_update_interests($user, $interests) {
    tag_set('user', $user->id, $interests);
}

function useredit_shared_definition(&$mform, $editoroptions = null) {
    global $CFG, $USER, $DB;

    $user = $DB->get_record('user', array('id' => $USER->id));
    useredit_load_preferences($user, false);

    $strrequired = get_string('required');

    $nameordercheck = new stdClass();
    $nameordercheck->firstname = 'a';
    $nameordercheck->lastname  = 'b';
    if (fullname($nameordercheck) == 'b a' ) {  // See MDL-4325
        $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
    } else {
        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
    }

    $mform->addRule('firstname', $strrequired, 'required', null, 'client');
    $mform->setType('firstname', PARAM_NOTAGS);

    $mform->addRule('lastname', $strrequired, 'required', null, 'client');
    $mform->setType('lastname', PARAM_NOTAGS);

    // Do not show email field if change confirmation is pending
    if (!empty($CFG->emailchangeconfirmation) and !empty($user->preference_newemail)) {
        $notice = get_string('emailchangepending', 'auth', $user);
        $notice .= '<br /><a href="edit.php?cancelemailchange=1&amp;id='.$user->id.'">'
                . get_string('emailchangecancel', 'auth') . '</a>';
        $mform->addElement('static', 'emailpending', get_string('email'), $notice);
    } else {
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
        $mform->addRule('email', $strrequired, 'required', null, 'client');
    }

    $choices = array();
    $choices['0'] = get_string('emaildisplayno');
    $choices['1'] = get_string('emaildisplayyes');
    $choices['2'] = get_string('emaildisplaycourse');
    $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
    $mform->setDefault('maildisplay', 2);

    $choices = array();
    $choices['0'] = get_string('textformat');
    $choices['1'] = get_string('htmlformat');
    $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
    $mform->setDefault('mailformat', 1);

    if (!empty($CFG->allowusermailcharset)) {
        $choices = array();
        $charsets = get_list_of_charsets();
        if (!empty($CFG->sitemailcharset)) {
            $choices['0'] = get_string('site').' ('.$CFG->sitemailcharset.')';
        } else {
            $choices['0'] = get_string('site').' (UTF-8)';
        }
        $choices = array_merge($choices, $charsets);
        $mform->addElement('select', 'preference_mailcharset', get_string('emailcharset'), $choices);
    }

    $choices = array();
    $choices['0'] = get_string('emaildigestoff');
    $choices['1'] = get_string('emaildigestcomplete');
    $choices['2'] = get_string('emaildigestsubjects');
    $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
    $mform->setDefault('maildigest', 0);

    $choices = array();
    $choices['1'] = get_string('autosubscribeyes');
    $choices['0'] = get_string('autosubscribeno');
    $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
    $mform->setDefault('autosubscribe', 1);

    if (!empty($CFG->forum_trackreadposts)) {
        $choices = array();
        $choices['0'] = get_string('trackforumsno');
        $choices['1'] = get_string('trackforumsyes');
        $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
        $mform->setDefault('trackforums', 0);
    }

    $editors = editors_get_enabled();
    if (count($editors) > 1) {
        $choices = array();
        $choices['0'] = get_string('texteditor');
        $choices['1'] = get_string('htmleditor');
        $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
        $mform->setDefault('htmleditor', 1);
    } else {
        $mform->addElement('hidden', 'htmleditor');
        $mform->setDefault('htmleditor', 1);
        $mform->setType('htmleditor', PARAM_INT);
    }

    if (empty($CFG->enableajax)) {
        $mform->addElement('static', 'ajaxdisabled', get_string('ajaxuse'), get_string('ajaxno'));
    } else {
        $choices = array();
        $choices['0'] = get_string('ajaxno');
        $choices['1'] = get_string('ajaxyes');
        $mform->addElement('select', 'ajax', get_string('ajaxuse'), $choices);
        $mform->setDefault('ajax', 0);
    }

    $choices = array();
    $choices['0'] = get_string('screenreaderno');
    $choices['1'] = get_string('screenreaderyes');
    $mform->addElement('select', 'screenreader', get_string('screenreaderuse'), $choices);
    $mform->setDefault('screenreader', 0);
    $mform->addHelpButton('screenreader', 'screenreaderuse');

    $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="21"');
    $mform->setType('city', PARAM_MULTILANG);
    $mform->addRule('city', $strrequired, 'required', null, 'client');
    if (!empty($CFG->defaultcity)) {
        $mform->setDefault('city', $CFG->defaultcity);
    }

    $choices = get_string_manager()->get_list_of_countries();
    $choices= array(''=>get_string('selectacountry').'...') + $choices;
    $mform->addElement('select', 'country', get_string('selectacountry'), $choices);
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
        $mform->setDefault('timezone', '99');
    }

    $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_string_manager()->get_list_of_translations());
    $mform->setDefault('lang', $CFG->lang);

    if (!empty($CFG->allowuserthemes)) {
        $choices = array();
        $choices[''] = get_string('default');
        $themes = get_list_of_themes();
        foreach ($themes as $key=>$theme) {
            if (empty($theme->hidefromselector)) {
                $choices[$key] = $theme->name;
            }
        }
        $mform->addElement('select', 'theme', get_string('preferredtheme'), $choices);
    }

    $mform->addElement('editor', 'description_editor', get_string('userdescription'), null, $editoroptions);
    $mform->setType('description_editor', PARAM_CLEANHTML);
    $mform->addHelpButton('description_editor', 'userdescription');

    if (!empty($CFG->gdversion) and empty($USER->newadminuser)) {
        $mform->addElement('header', 'moodle_picture', get_string('pictureofuser'));

        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

        $mform->addElement('checkbox', 'deletepicture', get_string('delete'));
        $mform->setDefault('deletepicture', 0);

        $mform->addElement('filepicker', 'imagefile', get_string('newpicture'), '', array('maxbytes'=>get_max_upload_file_size($CFG->maxbytes)));
        $mform->addHelpButton('imagefile', 'newpicture');

        $mform->addElement('text', 'imagealt', get_string('imagealt'), 'maxlength="100" size="30"');
        $mform->setType('imagealt', PARAM_MULTILANG);

    }

    if (!empty($CFG->usetags) and empty($USER->newadminuser)) {
        $mform->addElement('header', 'moodle_interests', get_string('interests'));
        $mform->addElement('tags', 'interests', get_string('interestslist'), array('display' => 'noofficial'));
        $mform->addHelpButton('interests', 'interestslist');
    }

    /// Moodle optional fields
    $mform->addElement('header', 'moodle_optional', get_string('optional', 'form'));

    $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');
    $mform->setType('url', PARAM_URL);

    $mform->addElement('text', 'icq', get_string('icqnumber'), 'maxlength="15" size="25"');
    $mform->setType('icq', PARAM_NOTAGS);

    $mform->addElement('text', 'skype', get_string('skypeid'), 'maxlength="50" size="25"');
    $mform->setType('skype', PARAM_NOTAGS);

    $mform->addElement('text', 'aim', get_string('aimid'), 'maxlength="50" size="25"');
    $mform->setType('aim', PARAM_NOTAGS);

    $mform->addElement('text', 'yahoo', get_string('yahooid'), 'maxlength="50" size="25"');
    $mform->setType('yahoo', PARAM_NOTAGS);

    $mform->addElement('text', 'msn', get_string('msnid'), 'maxlength="50" size="25"');
    $mform->setType('msn', PARAM_NOTAGS);

    $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="255" size="25"');
    $mform->setType('idnumber', PARAM_NOTAGS);

    $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
    $mform->setType('institution', PARAM_MULTILANG);

    $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
    $mform->setType('department', PARAM_MULTILANG);

    $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
    $mform->setType('phone1', PARAM_NOTAGS);

    $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="25"');
    $mform->setType('phone2', PARAM_NOTAGS);

    $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
    $mform->setType('address', PARAM_MULTILANG);


}



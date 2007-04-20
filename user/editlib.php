<?php  //$Id$


function useredit_load_preferences(&$user) {
    if (!empty($user->id) and $preferences = get_user_preferences(null, null, $user->id)) {
        foreach($preferences as $name=>$value) {
            $user->{'preference_'.$name} = $value;
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

function useredit_update_picture(&$usernew, &$userform) {
    global $CFG;

    if (isset($usernew->deletepicture) and $usernew->deletepicture) {
        $location = $CFG->dataroot.'/users/'.$usernew->id;
        @remove_dir($location);
        set_field('user', 'picture', 0, 'id', $usernew->id);
    } else if ($usernew->picture = save_profile_image($usernew->id, $userform->get_um(), 'users')) {
        set_field('user', 'picture', 1, 'id', $usernew->id);
    }
}

function useredit_update_bounces($user, $usernew) {
    if (!isset($usernew->email)) {
        //locked field
        return;
    }
    if ($user->email !== $usernew->email) {
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
    if (($usernew->trackforums != $user->trackforums) and !$usernew->trackforums) {
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        forum_tp_delete_read_records($usernew->id);
    }
}

function useredit_shared_definition(&$mform) {
    global $CFG;

    $strrequired = get_string('required');

    $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
    $mform->addRule('firstname', $strrequired, 'required', null, 'client');
    $mform->setType('firstname', PARAM_NOTAGS);

    $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="30"');
    $mform->addRule('lastname', $strrequired, 'required', null, 'client');
    $mform->setType('lastname', PARAM_NOTAGS);

    $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
    $mform->addRule('email', $strrequired, 'required', null, 'client');

    $choices = array();
    $choices['0'] = get_string('emaildisplayno');
    $choices['1'] = get_string('emaildisplayyes');
    $choices['2'] = get_string('emaildisplaycourse');
    $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
    $mform->setDefault('maildisplay', 2);

    $choices = array();
    $choices['0'] = get_string('emailenable');
    $choices['1'] = get_string('emaildisable');
    $mform->addElement('select', 'emailstop', get_string('emailactive'), $choices);
    $mform->setDefault('emailenable', 1);

    $choices = array();
    $choices['0'] = get_string('textformat');
    $choices['1'] = get_string('htmlformat');
    $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
    $mform->setDefault('mailformat', 1);
    $mform->setAdvanced('mailformat');

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
        $mform->setAdvanced('preference_mailcharset');
    }

    $choices = array();
    $choices['0'] = get_string('emaildigestoff');
    $choices['1'] = get_string('emaildigestcomplete');
    $choices['2'] = get_string('emaildigestsubjects');
    $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
    $mform->setDefault('maildigest', 0);
    $mform->setAdvanced('maildigest');

    $choices = array();
    $choices['1'] = get_string('autosubscribeyes');
    $choices['0'] = get_string('autosubscribeno');
    $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
    $mform->setDefault('autosubscribe', 1);
    $mform->setAdvanced('autosubscribe');

    if (!empty($CFG->forum_trackreadposts)) {
        $choices = array();
        $choices['0'] = get_string('trackforumsno');
        $choices['1'] = get_string('trackforumsyes');
        $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
        $mform->setDefault('trackforums', 0);
        $mform->setAdvanced('trackforums');
    }

    if ($CFG->htmleditor) {
        $choices = array();
        $choices['0'] = get_string('texteditor');
        $choices['1'] = get_string('htmleditor');
        $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
        $mform->setDefault('htmleditor', 1);
        $mform->setAdvanced('htmleditor');
    }

    if (empty($CFG->enableajax)) {
        $mform->addElement('static', 'ajaxdisabled', get_string('ajaxuse'), get_string('ajaxno'));
        $mform->setAdvanced('ajaxdisabled');
    } else {
        $choices = array();
        $choices['0'] = get_string('ajaxno');
        $choices['1'] = get_string('ajaxyes');
        $mform->addElement('select', 'ajax', get_string('ajaxuse'), $choices);
        $mform->setDefault('ajax', 0);
        $mform->setAdvanced('ajax');
    }

    $choices = array();
    $choices['0'] = get_string('screenreaderno');
    $choices['1'] = get_string('screenreaderyes');
    $mform->addElement('select', 'screenreader', get_string('screenreaderuse'), $choices);
    $mform->setDefault('screenreader', 0);
    $mform->setAdvanced('screenreader');

    $mform->addElement('text', 'city', get_string('city'), 'maxlength="100" size="25"');
    $mform->setType('city', PARAM_MULTILANG);
    $mform->addRule('city', $strrequired, 'required', null, 'client');


    $choices = get_list_of_countries();
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

    $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_list_of_languages());
    $mform->setDefault('lang', $CFG->lang);

    if (!empty($CFG->allowuserthemes)) {
        $choices = array();
        $choices[''] = get_string('default');
        $choices += get_list_of_themes();
        $mform->addElement('select', 'theme', get_string('preferredtheme'), $choices);
        $mform->setAdvanced('theme');
    }

    $mform->addElement('htmleditor', 'description', get_string('userdescription'));
    $mform->setType('description', PARAM_CLEAN);
    $mform->setHelpButton('description', array('text', get_string('helptext')));

    if (!empty($CFG->gdversion)) {
        $mform->addElement('header', 'moodle_picture', get_string('pictureof'));//TODO: Accessibility fix fieldset legend

        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

        $mform->addElement('checkbox', 'deletepicture', get_string('delete'));
        $mform->setDefault('deletepicture',false);

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
    $mform->setType('idnumber', PARAM_CLEAN);

    $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
    $mform->setType('institution', PARAM_MULTILANG);

    $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
    $mform->setType('department', PARAM_MULTILANG);

    $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
    $mform->setType('phone1', PARAM_CLEAN);

    $mform->addElement('text', 'phone2', get_string('phone'), 'maxlength="20" size="25"');
    $mform->setType('phone2', PARAM_CLEAN);

    $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
    $mform->setType('address', PARAM_MULTILANG);
}

?>
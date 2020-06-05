<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains function used when editing a users profile and preferences.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Cancels the requirement for a user to update their email address.
 *
 * @param int $userid
 */
function cancel_email_update($userid) {
    unset_user_preference('newemail', $userid);
    unset_user_preference('newemailkey', $userid);
    unset_user_preference('newemailattemptsleft', $userid);
}

/**
 * Performs the common access checks and page setup for all
 * user preference pages.
 *
 * @param int $userid The user id to edit taken from the page params.
 * @param int $courseid The optional course id if we came from a course context.
 * @return array containing the user and course records.
 */
function useredit_setup_preference_page($userid, $courseid) {
    global $PAGE, $SESSION, $DB, $CFG, $OUTPUT, $USER;

    // Guest can not edit.
    if (isguestuser()) {
        print_error('guestnoeditprofile');
    }

    if (!$course = $DB->get_record('course', array('id' => $courseid))) {
        print_error('invalidcourseid');
    }

    if ($course->id != SITEID) {
        require_login($course);
    } else if (!isloggedin()) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->wwwroot.'/user/preferences.php';
        }
        redirect(get_login_url());
    } else {
        $PAGE->set_context(context_system::instance());
    }

    // The user profile we are editing.
    if (!$user = $DB->get_record('user', array('id' => $userid))) {
        print_error('invaliduserid');
    }

    // Guest can not be edited.
    if (isguestuser($user)) {
        print_error('guestnoeditprofile');
    }

    // Remote users cannot be edited.
    if (is_mnet_remote_user($user)) {
        if (user_not_fully_set_up($user, false)) {
            $hostwwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $user->mnethostid));
            print_error('usernotfullysetup', 'mnet', '', $hostwwwroot);
        }
        redirect($CFG->wwwroot . "/user/view.php?course={$course->id}");
    }

    $systemcontext   = context_system::instance();
    $personalcontext = context_user::instance($user->id);

    // Check access control.
    if ($user->id == $USER->id) {
        // Editing own profile - require_login() MUST NOT be used here, it would result in infinite loop!
        if (!has_capability('moodle/user:editownprofile', $systemcontext)) {
            print_error('cannotedityourprofile');
        }

    } else {
        // Teachers, parents, etc.
        require_capability('moodle/user:editprofile', $personalcontext);

        // No editing of primary admin!
        if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
            print_error('useradmineditadmin');
        }
    }

    if ($user->deleted) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('userdeleted'));
        echo $OUTPUT->footer();
        die;
    }

    $PAGE->set_pagelayout('admin');
    $PAGE->set_context($personalcontext);
    if ($USER->id != $user->id) {
        $PAGE->navigation->extend_for_user($user);
    } else {
        if ($node = $PAGE->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE)) {
            $node->force_open();
        }
    }

    return array($user, $course);
}

/**
 * Loads the given users preferences into the given user object.
 *
 * @param stdClass $user The user object, modified by reference.
 * @param bool $reload
 */
function useredit_load_preferences(&$user, $reload=true) {
    global $USER;

    if (!empty($user->id)) {
        if ($reload and $USER->id == $user->id) {
            // Reload preferences in case it was changed in other session.
            unset($USER->preference);
        }

        if ($preferences = get_user_preferences(null, null, $user->id)) {
            foreach ($preferences as $name => $value) {
                $user->{'preference_'.$name} = $value;
            }
        }
    }
}

/**
 * Updates the user preferences for the given user
 *
 * Only preference that can be updated directly will be updated here. This method is called from various WS
 * updating users and should be used when updating user details. Plugins may whitelist preferences that can
 * be updated by defining 'user_preferences' callback, {@see core_user::fill_preferences_cache()}
 *
 * Some parts of code may use user preference table to store internal data, in these cases it is acceptable
 * to call set_user_preference()
 *
 * @param stdClass|array $usernew object or array that has user preferences as attributes with keys starting with preference_
 */
function useredit_update_user_preference($usernew) {
    global $USER;
    $ua = (array)$usernew;
    if (is_object($usernew) && isset($usernew->id) && isset($usernew->deleted) && isset($usernew->confirmed)) {
        // This is already a full user object, maybe not completely full but these fields are enough.
        $user = $usernew;
    } else if (empty($ua['id']) || $ua['id'] == $USER->id) {
        // We are updating current user.
        $user = $USER;
    } else {
        // Retrieve user object.
        $user = core_user::get_user($ua['id'], '*', MUST_EXIST);
    }

    foreach ($ua as $key => $value) {
        if (strpos($key, 'preference_') === 0) {
            $name = substr($key, strlen('preference_'));
            if (core_user::can_edit_preference($name, $user)) {
                $value = core_user::clean_preference($value, $name);
                set_user_preference($name, $value, $user->id);
            }
        }
    }
}

/**
 * @deprecated since Moodle 3.2
 * @see core_user::update_picture()
 */
function useredit_update_picture() {
    throw new coding_exception('useredit_update_picture() can not be used anymore. Please use ' .
        'core_user::update_picture() instead.');
}

/**
 * Updates the user email bounce + send counts when the user is edited.
 *
 * @param stdClass $user The current user object.
 * @param stdClass $usernew The updated user object.
 */
function useredit_update_bounces($user, $usernew) {
    if (!isset($usernew->email)) {
        // Locked field.
        return;
    }
    if (!isset($user->email) || $user->email !== $usernew->email) {
        set_bounce_count($usernew, true);
        set_send_count($usernew, true);
    }
}

/**
 * Updates the forums a user is tracking when the user is edited.
 *
 * @param stdClass $user The original user object.
 * @param stdClass $usernew The updated user object.
 */
function useredit_update_trackforums($user, $usernew) {
    global $CFG;
    if (!isset($usernew->trackforums)) {
        // Locked field.
        return;
    }
    if ((!isset($user->trackforums) || ($usernew->trackforums != $user->trackforums)) and !$usernew->trackforums) {
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        forum_tp_delete_read_records($usernew->id);
    }
}

/**
 * Updates a users interests.
 *
 * @param stdClass $user
 * @param array $interests
 */
function useredit_update_interests($user, $interests) {
    core_tag_tag::set_item_tags('core', 'user', $user->id,
            context_user::instance($user->id), $interests);
}

/**
 * Powerful function that is used by edit and editadvanced to add common form elements/rules/etc.
 *
 * @param moodleform $mform
 * @param array $editoroptions
 * @param array $filemanageroptions
 * @param stdClass $user
 */
function useredit_shared_definition(&$mform, $editoroptions, $filemanageroptions, $user) {
    global $CFG, $USER, $DB;

    if ($user->id > 0) {
        useredit_load_preferences($user, false);
    }

    $strrequired = get_string('required');
    $stringman = get_string_manager();

    // Add the necessary names.
    foreach (useredit_get_required_name_fields() as $fullname) {
        $purpose = user_edit_map_field_purpose($user->id, $fullname);
        $mform->addElement('text', $fullname,  get_string($fullname),  'maxlength="100" size="30"' . $purpose);
        if ($stringman->string_exists('missing'.$fullname, 'core')) {
            $strmissingfield = get_string('missing'.$fullname, 'core');
        } else {
            $strmissingfield = $strrequired;
        }
        $mform->addRule($fullname, $strmissingfield, 'required', null, 'client');
        $mform->setType($fullname, PARAM_NOTAGS);
    }

    $enabledusernamefields = useredit_get_enabled_name_fields();
    // Add the enabled additional name fields.
    foreach ($enabledusernamefields as $addname) {
        $purpose = user_edit_map_field_purpose($user->id, $addname);
        $mform->addElement('text', $addname,  get_string($addname), 'maxlength="100" size="30"' . $purpose);
        $mform->setType($addname, PARAM_NOTAGS);
    }

    // Do not show email field if change confirmation is pending.
    if ($user->id > 0 and !empty($CFG->emailchangeconfirmation) and !empty($user->preference_newemail)) {
        $notice = get_string('emailchangepending', 'auth', $user);
        $notice .= '<br /><a href="edit.php?cancelemailchange=1&amp;id='.$user->id.'">'
                . get_string('emailchangecancel', 'auth') . '</a>';
        $mform->addElement('static', 'emailpending', get_string('email'), $notice);
    } else {
        $purpose = user_edit_map_field_purpose($user->id, 'email');
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"' . $purpose);
        $mform->addRule('email', $strrequired, 'required', null, 'client');
        $mform->setType('email', PARAM_RAW_TRIMMED);
    }

    $choices = array();
    $choices['0'] = get_string('emaildisplayno');
    $choices['1'] = get_string('emaildisplayyes');
    $choices['2'] = get_string('emaildisplaycourse');
    $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
    $mform->setDefault('maildisplay', core_user::get_property_default('maildisplay'));
    $mform->addHelpButton('maildisplay', 'emaildisplay');

    $mform->addElement('text', 'moodlenetprofile', get_string('moodlenetprofile', 'user'));
    $mform->setType('moodlenetprofile', PARAM_RAW_TRIMMED);

    $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="21"');
    $mform->setType('city', PARAM_TEXT);
    if (!empty($CFG->defaultcity)) {
        $mform->setDefault('city', $CFG->defaultcity);
    }

    $purpose = user_edit_map_field_purpose($user->id, 'country');
    $choices = get_string_manager()->get_list_of_countries();
    $choices = array('' => get_string('selectacountry') . '...') + $choices;
    $mform->addElement('select', 'country', get_string('selectacountry'), $choices, $purpose);
    if (!empty($CFG->country)) {
        $mform->setDefault('country', core_user::get_property_default('country'));
    }

    if (isset($CFG->forcetimezone) and $CFG->forcetimezone != 99) {
        $choices = core_date::get_list_of_timezones($CFG->forcetimezone);
        $mform->addElement('static', 'forcedtimezone', get_string('timezone'), $choices[$CFG->forcetimezone]);
        $mform->addElement('hidden', 'timezone');
        $mform->setType('timezone', core_user::get_property_type('timezone'));
    } else {
        $choices = core_date::get_list_of_timezones($user->timezone, true);
        $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
    }

    if ($user->id < 0) {
        $purpose = user_edit_map_field_purpose($user->id, 'lang');
        $translations = get_string_manager()->get_list_of_translations();
        $mform->addElement('select', 'lang', get_string('preferredlanguage'), $translations, $purpose);
        $lang = empty($user->lang) ? $CFG->lang : $user->lang;
        $mform->setDefault('lang', $lang);
    }

    if (!empty($CFG->allowuserthemes)) {
        $choices = array();
        $choices[''] = get_string('default');
        $themes = get_list_of_themes();
        foreach ($themes as $key => $theme) {
            if (empty($theme->hidefromselector)) {
                $choices[$key] = get_string('pluginname', 'theme_'.$theme->name);
            }
        }
        $mform->addElement('select', 'theme', get_string('preferredtheme'), $choices);
    }

    $mform->addElement('editor', 'description_editor', get_string('userdescription'), null, $editoroptions);
    $mform->setType('description_editor', PARAM_RAW);
    $mform->addHelpButton('description_editor', 'userdescription');

    if (empty($USER->newadminuser)) {
        $mform->addElement('header', 'moodle_picture', get_string('pictureofuser'));
        $mform->setExpanded('moodle_picture', true);

        if (!empty($CFG->enablegravatar)) {
            $mform->addElement('html', html_writer::tag('p', get_string('gravatarenabled')));
        }

        $mform->addElement('static', 'currentpicture', get_string('currentpicture'));

        $mform->addElement('checkbox', 'deletepicture', get_string('deletepicture'));
        $mform->setDefault('deletepicture', 0);

        $mform->addElement('filemanager', 'imagefile', get_string('newpicture'), '', $filemanageroptions);
        $mform->addHelpButton('imagefile', 'newpicture');

        $mform->addElement('text', 'imagealt', get_string('imagealt'), 'maxlength="100" size="30"');
        $mform->setType('imagealt', PARAM_TEXT);

    }

    // Display user name fields that are not currenlty enabled here if there are any.
    $disabledusernamefields = useredit_get_disabled_name_fields($enabledusernamefields);
    if (count($disabledusernamefields) > 0) {
        $mform->addElement('header', 'moodle_additional_names', get_string('additionalnames'));
        foreach ($disabledusernamefields as $allname) {
            $purpose = user_edit_map_field_purpose($user->id, $allname);
            $mform->addElement('text', $allname, get_string($allname), 'maxlength="100" size="30"' . $purpose);
            $mform->setType($allname, PARAM_NOTAGS);
        }
    }

    if (core_tag_tag::is_enabled('core', 'user') and empty($USER->newadminuser)) {
        $mform->addElement('header', 'moodle_interests', get_string('interests'));
        $mform->addElement('tags', 'interests', get_string('interestslist'),
            array('itemtype' => 'user', 'component' => 'core'));
        $mform->addHelpButton('interests', 'interestslist');
    }

    // Moodle optional fields.
    $mform->addElement('header', 'moodle_optional', get_string('optional', 'form'));

    $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');
    $mform->setType('url', core_user::get_property_type('url'));

    $mform->addElement('text', 'icq', get_string('icqnumber'), 'maxlength="15" size="25"');
    $mform->setType('icq', core_user::get_property_type('icq'));
    $mform->setForceLtr('icq');

    $mform->addElement('text', 'skype', get_string('skypeid'), 'maxlength="50" size="25"');
    $mform->setType('skype', core_user::get_property_type('skype'));
    $mform->setForceLtr('skype');

    $mform->addElement('text', 'aim', get_string('aimid'), 'maxlength="50" size="25"');
    $mform->setType('aim', core_user::get_property_type('aim'));
    $mform->setForceLtr('aim');

    $mform->addElement('text', 'yahoo', get_string('yahooid'), 'maxlength="50" size="25"');
    $mform->setType('yahoo', core_user::get_property_type('yahoo'));
    $mform->setForceLtr('yahoo');

    $mform->addElement('text', 'msn', get_string('msnid'), 'maxlength="50" size="25"');
    $mform->setType('msn', core_user::get_property_type('msn'));
    $mform->setForceLtr('msn');

    $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="255" size="25"');
    $mform->setType('idnumber', core_user::get_property_type('idnumber'));

    $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="255" size="25"');
    $mform->setType('institution', core_user::get_property_type('institution'));

    $mform->addElement('text', 'department', get_string('department'), 'maxlength="255" size="25"');
    $mform->setType('department', core_user::get_property_type('department'));

    $mform->addElement('text', 'phone1', get_string('phone1'), 'maxlength="20" size="25"');
    $mform->setType('phone1', core_user::get_property_type('phone1'));
    $mform->setForceLtr('phone1');

    $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="25"');
    $mform->setType('phone2', core_user::get_property_type('phone2'));
    $mform->setForceLtr('phone2');

    $mform->addElement('text', 'address', get_string('address'), 'maxlength="255" size="25"');
    $mform->setType('address', core_user::get_property_type('address'));
}

/**
 * Return required user name fields for forms.
 *
 * @return array required user name fields in order according to settings.
 */
function useredit_get_required_name_fields() {
    global $CFG;

    // Get the name display format.
    $nameformat = $CFG->fullnamedisplay;

    // Names that are required fields on user forms.
    $necessarynames = array('firstname', 'lastname');
    $languageformat = get_string('fullnamedisplay');

    // Check that the language string and the $nameformat contain the necessary names.
    foreach ($necessarynames as $necessaryname) {
        $pattern = "/$necessaryname\b/";
        if (!preg_match($pattern, $languageformat)) {
            // If the language string has been altered then fall back on the below order.
            $languageformat = 'firstname lastname';
        }
        if (!preg_match($pattern, $nameformat)) {
            // If the nameformat doesn't contain the necessary name fields then use the languageformat.
            $nameformat = $languageformat;
        }
    }

    // Order all of the name fields in the postion they are written in the fullnamedisplay setting.
    $necessarynames = order_in_string($necessarynames, $nameformat);
    return $necessarynames;
}

/**
 * Gets enabled (from fullnameformate setting) user name fields in appropriate order.
 *
 * @return array Enabled user name fields.
 */
function useredit_get_enabled_name_fields() {
    global $CFG;

    // Get all of the other name fields which are not ranked as necessary.
    $additionalusernamefields = array_diff(get_all_user_name_fields(), array('firstname', 'lastname'));
    // Find out which additional name fields are actually being used from the fullnamedisplay setting.
    $enabledadditionalusernames = array();
    foreach ($additionalusernamefields as $enabledname) {
        if (strpos($CFG->fullnamedisplay, $enabledname) !== false) {
            $enabledadditionalusernames[] = $enabledname;
        }
    }

    // Order all of the name fields in the postion they are written in the fullnamedisplay setting.
    $enabledadditionalusernames = order_in_string($enabledadditionalusernames, $CFG->fullnamedisplay);
    return $enabledadditionalusernames;
}

/**
 * Gets user name fields not enabled from the setting fullnamedisplay.
 *
 * @param array $enabledadditionalusernames Current enabled additional user name fields.
 * @return array Disabled user name fields.
 */
function useredit_get_disabled_name_fields($enabledadditionalusernames = null) {
    // If we don't have enabled additional user name information then go and fetch it (try to avoid).
    if (!isset($enabledadditionalusernames)) {
        $enabledadditionalusernames = useredit_get_enabled_name_fields();
    }

    // These are the additional fields that are not currently enabled.
    $nonusednamefields = array_diff(get_all_user_name_fields(),
            array_merge(array('firstname', 'lastname'), $enabledadditionalusernames));
    return $nonusednamefields;
}

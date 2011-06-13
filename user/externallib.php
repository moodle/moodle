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
 * External user API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

class moodle_user_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_users_parameters() {
        global $CFG;

        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config'),
                            'password'    => new external_value(PARAM_RAW, 'Plain text password consisting of any characters'),
                            'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                            'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user'),
                            'email'       => new external_value(PARAM_EMAIL, 'A valid and unique email address'),
                            'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', VALUE_DEFAULT, 'manual', NULL_NOT_ALLOWED),
                            'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_DEFAULT, ''),
                            'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_DEFAULT, $CFG->lang, NULL_NOT_ALLOWED),
                            'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                            'timezone'    => new external_value(PARAM_ALPHANUMEXT, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                            'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL)
                        )
                    )
                )
            )
        );
    }

    /**
     * Create one or more users
     *
     * @param array $users  An array of users to create.
     * @return array An array of arrays
     */
    public static function create_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); //required for customfields related function
                                                             //TODO: move the functions somewhere else as
                                                             //they are "user" related

        // Ensure the current user is allowed to run this function
        $context = get_context_instance(CONTEXT_SYSTEM);
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::create_users_parameters(), array('users'=>$users));

        $availableauths  = get_plugin_list('auth');
        unset($availableauths['mnet']);       // these would need mnethostid too
        unset($availableauths['webservice']); // we do not want new webservice users for now

        $availablethemes = get_plugin_list('theme');
        $availablelangs  = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        $userids = array();
        foreach ($params['users'] as $user) {
            // Make sure that the username doesn't already exist
            if ($DB->record_exists('user', array('username'=>$user['username'], 'mnethostid'=>$CFG->mnet_localhost_id))) {
                throw new invalid_parameter_exception('Username already exists: '.$user['username']);
            }

            // Make sure auth is valid
            if (empty($availableauths[$user['auth']])) {
                throw new invalid_parameter_exception('Invalid authentication type: '.$user['auth']);
            }

            // Make sure lang is valid
            if (empty($availablelangs[$user['lang']])) {
                throw new invalid_parameter_exception('Invalid language code: '.$user['lang']);
            }

            // Make sure lang is valid
            if (!empty($user['theme']) && empty($availablethemes[$user['theme']])) { //theme is VALUE_OPTIONAL,
                                                                                     // so no default value.
                                                                                     // We need to test if the client sent it
                                                                                     // => !empty($user['theme'])
                throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
            }

            // make sure there is no data loss during truncation
            $truncated = truncate_userinfo($user);
            foreach ($truncated as $key=>$value) {
                    if ($truncated[$key] !== $user[$key]) {
                        throw new invalid_parameter_exception('Property: '.$key.' is too long: '.$user[$key]);
                    }
            }

            $user['confirmed'] = true;
            $user['mnethostid'] = $CFG->mnet_localhost_id;
            $user['id'] = user_create_user($user);

            // custom fields
            if(!empty($user['customfields'])) {
                foreach($user['customfields'] as $customfield) {
                    $user["profile_field_".$customfield['type']] = $customfield['value']; //profile_save_data() saves profile file
                                                                                            //it's expecting a user with the correct id,
                                                                                            //and custom field to be named profile_field_"shortname"
                }
                profile_save_data((object) $user);
            }

            //preferences
            if (!empty($user['preferences'])) {
                foreach($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'],$user['id']);
                }
            }

            $userids[] = array('id'=>$user['id'], 'username'=>$user['username']);
        }

        $transaction->allow_commit();

        return $userids;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_RAW, 'user name'),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function delete_users_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user ID')),
            )
        );
    }

    public static function delete_users($userids) {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot."/user/lib.php");

        // Ensure the current user is allowed to run this function
        $context = get_context_instance(CONTEXT_SYSTEM);
        require_capability('moodle/user:delete', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::delete_users_parameters(), array('userids'=>$userids));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['userids'] as $userid) {
            $user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
            // must not allow deleting of admins or self!!!
            if (is_siteadmin($user)) {
                throw new moodle_exception('useradminodelete', 'error');
            }
            if ($USER->id == $user->id) {
                throw new moodle_exception('usernotdeletederror', 'error');
            }
            user_delete_user($user);
        }

        $transaction->allow_commit();

        return null;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function delete_users_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_users_parameters() {
        global $CFG;
       return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                            'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'password'    => new external_value(PARAM_RAW, 'Plain text password consisting of any characters', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                            'email'       => new external_value(PARAM_EMAIL, 'A valid and unique email address', VALUE_OPTIONAL, '',NULL_NOT_ALLOWED),
                            'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                            'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                            'timezone'    => new external_value(PARAM_ALPHANUMEXT, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                            'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                            'description' => new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    public static function update_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); //required for customfields related function
                                                             //TODO: move the functions somewhere else as
                                                             //they are "user" related

        // Ensure the current user is allowed to run this function
        $context = get_context_instance(CONTEXT_SYSTEM);
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_parameters(), array('users'=>$users));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['users'] as $user) {
            user_update_user($user);
            //update user custom fields
            if(!empty($user['customfields'])) {

                foreach($user['customfields'] as $customfield) {
                    $user["profile_field_".$customfield['type']] = $customfield['value']; //profile_save_data() saves profile file
                                                                                            //it's expecting a user with the correct id,
                                                                                            //and custom field to be named profile_field_"shortname"
                }
                profile_save_data((object) $user);
            }

            //preferences
            if (!empty($user['preferences'])) {
                foreach($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'],$user['id']);
                }
            }
        }

        $transaction->allow_commit();

        return null;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_users_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_by_id_parameters() {
        return new external_function_parameters(
                array(
                    'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user ID')),
                )
        );
    }

    /**
     * Get user information
     * - This function is matching the permissions of /user/profil.php
     * - It is also matching some permissions from /user/editadvanced.php for the following fields:
     *   auth, confirmed, idnumber, lang, theme, timezone, mailformat
     * @param array $userids  array of user ids
     * @return array An array of arrays describing users
     */
    public static function get_users_by_id($userids) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot . "/user/profile/lib.php"); //custom field library

        $isadmin = is_siteadmin($USER);

        $params = self::validate_parameters(self::get_users_by_id_parameters(),
                array('userids'=>$userids));

        $userscontexts = get_context_instance(CONTEXT_USER, $params['userids']);

        $users = user_get_users_by_id($params['userids']);
        $result = array();
        foreach ($users as $user) {

            $context = $userscontexts[$user->id];
            $hasviewdetailscap = has_capability('moodle/user:viewdetails', $context);
            $hasuserupdatecap = has_capability('moodle/user:update', get_system_context());

            self::validate_context($context);

            $currentuser = ($user->id == $USER->id);

            if (empty($user->deleted)) {

                if (!$currentuser && !$hasviewdetailscap && !has_coursecontact_role($user->id)) {
                    throw new moodle_exception('usernotavailable', 'error');
                }

                $userarray = array();

                //basic fields
                $userarray['id'] = $user->id;
                if ($isadmin) {
                    $userarray['username'] = $user->username;
                }
                if ($isadmin or has_capability('moodle/site:viewfullnames', $context)) {
                    $userarray['firstname'] = $user->firstname;
                    $userarray['lastname'] = $user->lastname;
                }
                $userarray['fullname'] = fullname($user);

                //fields matching permissions from /user/editadvanced.php
                if ($currentuser or $hasuserupdatecap) {
                    $userarray['auth'] = $user->auth;
                    $userarray['confirmed'] = $user->confirmed;
                    $userarray['idnumber'] = $user->idnumber;
                    $userarray['lang'] = $user->lang;
                    $userarray['theme'] = $user->theme;
                    $userarray['timezone'] = $user->timezone;
                    $userarray['mailformat'] = $user->mailformat;
                }

                //Custom fields (matching /user/profil/lib.php - profile_display_fields code logic)
                $userarray['customfields'] = array();
                if ($categories = $DB->get_records('user_info_category', null, 'sortorder ASC')) {
                    foreach ($categories as $category) {
                        if ($fields = $DB->get_records('user_info_field', array('categoryid'=>$category->id), 'sortorder ASC')) {
                            foreach ($fields as $field) {
                                require_once($CFG->dirroot.'/user/profile/field/'.$field->datatype.'/field.class.php');
                                $newfield = 'profile_field_'.$field->datatype;
                                $formfield = new $newfield($field->id, $user->id);
                                if ($formfield->is_visible() and !$formfield->is_empty()) {
                                    $userarray['customfields'][] =
                                        array('name' => $formfield->field->name, 'value' => $formfield->data,
                                            'type' => $field->datatype, 'shortname' => $formfield->field->shortname);
                                }
                            }
                        }
                    }
                }

                //image profiles urls (public, no permission required in fact)
                $profileimageurl = moodle_url::make_pluginfile_url($context->id, 'user', 'icon', NULL, '/', 'f1');
                $userarray['profileimageurl'] = $profileimageurl->out(false);
                $profileimageurlsmall = moodle_url::make_pluginfile_url($context->id, 'user', 'icon', NULL, '/', 'f2');
                $userarray['profileimageurlsmall'] = $profileimageurlsmall->out(false);

                //hidden user field
                if (has_capability('moodle/user:viewhiddendetails', $context)) {
                    $hiddenfields = array();
                } else {
                    $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
                }

                if (isset($user->description) && (!isset($hiddenfields['description']) or $isadmin)) {
                    if (empty($CFG->profilesforenrolledusersonly) || $currentuser) {
                        $user->description = file_rewrite_pluginfile_urls($user->description, 'pluginfile.php', $context->id, 'user', 'profile', null);
                        $userarray['description'] = $user->description;
                        $userarray['descriptionformat'] = $user->descriptionformat;
                    }
                }

                if ((! isset($hiddenfields['country']) or $isadmin) && $user->country) {
                    $userarray['country'] = $user->country;
                }

                if ((! isset($hiddenfields['city']) or $isadmin) && $user->city) {
                    $userarray['city'] = $user->city;
                }

                if (has_capability('moodle/user:viewhiddendetails', $context)) {
                    if ($user->address) {
                        $userarray['address'] = $user->address;
                    }
                    if ($user->phone1) {
                        $userarray['phone1'] = $user->phone1;
                    }
                    if ($user->phone2) {
                        $userarray['phone2'] = $user->phone2;
                    }
                }

                if ($currentuser
                  or $user->maildisplay == 1
                  or has_capability('moodle/course:useremail', $context)
                  or ($user->maildisplay == 2 and enrol_sharing_course($user, $USER))) {
                    $userarray['email'] = $user->email;;
                }

                if ($user->url && (!isset($hiddenfields['webpage']) or $isadmin)) {
                    $url = $user->url;
                    if (strpos($user->url, '://') === false) {
                        $url = 'http://'. $url;
                    }
                    $user->url = clean_param($user->url, PARAM_URL);
                    $userarray['url'] = $user->url;
                }

                if ($user->icq && (!isset($hiddenfields['icqnumber']) or $isadmin)) {
                    $userarray['icq'] = $user->icq;
                }

                if ($user->skype && (!isset($hiddenfields['skypeid']) or $isadmin)) {
                    $userarray['skype'] = $user->skype;
                }
                if ($user->yahoo && (!isset($hiddenfields['yahooid']) or $isadmin)) {
                    $userarray['yahoo'] = $user->yahoo;
                }
                if ($user->aim && (!isset($hiddenfields['aimid']) or $isadmin)) {
                    $userarray['aim'] = $user->aim;
                }
                if ($user->msn && (!isset($hiddenfields['msnid']) or $isadmin)) {
                    $userarray['msn'] = $user->msn;
                }

                if ((!isset($hiddenfields['firstaccess'])) or $isadmin) {
                    if ($user->firstaccess) {
                        $userarray['firstaccess'] = $user->firstaccess;
                    } else {
                        $userarray['firstaccess'] = 0;
                    }
                }
                if ((!isset($hiddenfields['lastaccess'])) or $isadmin) {
                    if ($user->lastaccess) {
                        $userarray['lastaccess'] = $user->lastaccess;
                    } else {
                        $userarray['lastaccess'] = 0;
                    }
                }
                /// Printing tagged interests
                if (!empty($CFG->usetags)) {
                    require_once($CFG->dirroot . '/tag/lib.php');
                    if ($interests = tag_get_tags_csv('user', $user->id, TAG_RETURN_TEXT) ) {
                        $userarray['interests'] = $interests;
                    }
                }

                //Departement/Institution are not displayed on any profile, however you can get them from editing profile.
                if ($isadmin or $currentuser) {
                    if ($user->institution) {
                        $userarray['institution'] = $user->institution;
                    }
                    if (isset($user->department)) { //isset because it's ok to have department 0
                        $userarray['department'] = $user->department;
                    }
                }

                //list of courses where the user is enrolled
                $enrolledcourses = array();
                if (!isset($hiddenfields['mycourses'])) {
                    if ($mycourses = enrol_get_users_courses($user->id, true, NULL, 'visible DESC,sortorder ASC')) {
                        $courselisting = '';
                        foreach ($mycourses as $mycourse) {
                            if ($mycourse->category) {
                                if ($mycourse->visible == 0) {
                                    $ccontext = get_context_instance(CONTEXT_COURSE, $mycourse->id);
                                    if (!has_capability('moodle/course:viewhiddencourses', $ccontext)) {
                                        continue;
                                    }
                                }
                                $enrolledcourse = array();
                                $enrolledcourse['id'] = $mycourse->id;
                                $enrolledcourse['fullname'] = $mycourse->fullname;
                                $enrolledcourses[] = $enrolledcourse;
                            }
                        }
                        $userarray['enrolledcourses'] = $enrolledcourses;
                    }
                }

                //user preferences
                if ($currentuser) {
                    $preferences = array();
                    $userpreferences = get_user_preferences();
                     foreach($userpreferences as $prefname => $prefvalue) {
                        $preferences[] = array('name' => $prefname, 'value' => $prefvalue);
                     }
                     $userarray['preferences'] = $preferences;
                }
            }

            $result[] = $userarray;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_by_id_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                        array(
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
                    'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
                    'timezone'    => new external_value(PARAM_ALPHANUMEXT, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
                    'customfields' => new external_multiple_structure(
                                    new external_single_structure(
                                            array(
                                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
                                            )
                                    ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                            new external_single_structure(
                                    array(
                                        'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                    )
                            ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                            new external_single_structure(
                                    array(
                                        'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                        'fullname' => new external_value(PARAM_RAW, 'Fullname of the course')
                                    )
                            ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                        )
                )
        );
    }
}

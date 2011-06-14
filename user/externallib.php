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
                            'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
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
                            'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
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
     *
     * @param array $userids  array of user ids
     * @return array An array of arrays describing users
     */
    public static function get_users_by_id($userids) {
        global $CFG;
        require_once($CFG->dirroot . "/user/lib.php");
        //required for customfields related function
        //TODO: move the functions somewhere else as
        //they are "user" related
        require_once($CFG->dirroot . "/user/profile/lib.php");

        $params = self::validate_parameters(self::get_users_by_id_parameters(),
                array('userids'=>$userids));

        //TODO: check if there is any performance issue: we do one DB request to retrieve
        //  all user, then for each user the profile_load_data does at least two DB requests

        $users = user_get_users_by_id($params['userids']);
        $result = array();
        foreach ($users as $user) {

            $context = get_context_instance(CONTEXT_USER, $user->id);
            require_capability('moodle/user:viewalldetails', $context);
            self::validate_context($context);

            if (empty($user->deleted)) {

                $userarray = array();
               //we want to return an array not an object
                /// now we transfert all profile_field_xxx into the customfields
                // external_multiple_structure required by description
                $userarray['id'] = $user->id;
                $userarray['username'] = $user->username;
                $userarray['firstname'] = $user->firstname;
                $userarray['lastname'] = $user->lastname;
                $userarray['email'] = $user->email;
                $userarray['auth'] = $user->auth;
                $userarray['confirmed'] = $user->confirmed;
                $userarray['idnumber'] = $user->idnumber;
                $userarray['lang'] = $user->lang;
                $userarray['theme'] = $user->theme;
                $userarray['timezone'] = $user->timezone;
                $userarray['mailformat'] = $user->mailformat;
                $userarray['description'] = $user->description;
                $userarray['descriptionformat'] = $user->descriptionformat;
                $userarray['city'] = $user->city;
                $userarray['country'] = $user->country;
                $userarray['customfields'] = array();
                $customfields = profile_user_record($user->id);
                $customfields = (array) $customfields;
                foreach ($customfields as $key => $value) {
                    $userarray['customfields'][] = array('type' => $key, 'value' => $value);
                }

                $result[] = $userarray;
            }
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
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config'),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost'),
                    'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc'),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise'),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution'),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server'),
                    'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server'),
                    'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default'),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc'),
                    'description' => new external_value(PARAM_RAW, 'User profile description'),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format'),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user'),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ'),
                    'customfields' => new external_multiple_structure(
                                    new external_single_structure(
                                            array(
                                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                                'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                            )
                                    ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL)
                        )
                )
        );
    }
}

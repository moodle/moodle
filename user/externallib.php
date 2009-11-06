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
                            'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', false, 'manual', false),
                            'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', false),
                            'emailstop'   => new external_value(PARAM_NUMBER, 'Email is blocked: 1 is blocked and 0 otherwise', false),
                            'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en_utf8", must exist on server', false, $CFG->lang, false),
                            'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server', false),
                            'timezone'    => new external_value(PARAM_ALPHANUMEXT, 'Timezone code such as Australia/Perth, or 99 for default', false),
                            'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', false),
                            'description' => new external_value(PARAM_TEXT, 'User profile description, as HTML', false),
                            'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', false),
                            'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', false),
                            'preferences' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preference'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                                    )
                                ), 'User preferences', false),
                            'customfields' => new external_multiple_structure(
                                new external_single_structure(
                                    array(
                                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                                    )
                                ), 'User custom fields', false)
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

        // Ensure the current user is allowed to run this function
        $context = get_context_instance(CONTEXT_SYSTEM);
        require_capability('moodle/user:create', $context);
        self::validate_context($context);

        // Do basic automatic PARAM checks on incoming data, using params description
        // This checks to make sure that:
        //      1) No extra data was sent
        //      2) All required items were sent
        //      3) All data passes clean_param without changes (yes this is strict)
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::create_users_parameters(), array('users'=>$users));

        $availableauths  = get_plugin_list('auth');
        unset($availableauths['mnet']);       // these would need mnethostid too
        unset($availableauths['webservice']); // we do not want new webservice users for now

        $availablethemes = get_plugin_list('theme');
        $availablelangs  = get_list_of_languages();

        // TODO start delegated transaction

        $users = array();
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
            if (empty($availablethemes[$user['theme']])) {
                throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
            }

            //TODO: validate username, auth, lang and theme

            // finally create user
            $record = create_user_record($user['username'], $user['password'], $user['auth']);

            // update using given data
            $user['confirmed'] = 1;

            unset($user['username']);
            unset($user['password']);
            unset($user['auth']);

            $preferences = $user['preferences'];
            unset($user['preferences']);

            $customfields = $user['customfields'];
            unset($custom['customfields']);

            foreach ($user as $key=>$value) {
                if (is_null($value)) {
                    //ignore missing fields
                    continue;
                }
                $record->$key = $value;
            }
            $DB->update_record('user', $record);

            //TODO: preferences and custom fields

            $users[] = array('id'=>$record->id, 'username'=>$record->username);
        }

        // TODO allow commit of delegated transaction

        return $users;
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

    public static function delete_users($params) {
        //TODO
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
        //TODO
    }
    public static function update_users($params) {
        //TODO
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
    public static function get_users_parameters() {
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
    public static function get_users($userids) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        require_capability('moodle/user:viewdetails', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::get_users_parameters(), array('userids'=>$userids));

        //TODO: this search is probably useless for external systems because it is not exact
        //      1/ we should specify multiple search parameters including the mnet host id
        //      2/ custom profile fileds not included

        $result = array();
/*
        $users = get_users(true, $params['search'], false, null, 'firstname ASC','', '', '', 1000, 'id, mnethostid, auth, confirmed, username, idnumber, firstname, lastname, email, emailstop, lang, theme, timezone, mailformat, city, description, country');
        foreach ($users as $user) {
            $result[] = (array)$user;
        }*/

        return $result;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config'),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user'),
                    'email'       => new external_value(PARAM_EMAIL, 'A valid and unique email address'),
                    'auth'        => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', false),
                    'confirmed'   => new external_value(PARAM_NUMBER, 'Active user: 1 if confirmed, 0 otherwise', false),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', false),
                    'emailstop'   => new external_value(PARAM_NUMBER, 'Email is blocked: 1 is blocked and 0 otherwise', false),
                    'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en_utf8", must exist on server', false),
                    'theme'       => new external_value(PARAM_SAFEDIR, 'Theme name such as "standard", must exist on server', false),
                    'timezone'    => new external_value(PARAM_ALPHANUMEXT, 'Timezone code such as Australia/Perth, or 99 for default', false),
                    'mailformat'  => new external_value(PARAM_INTEGER, 'Mail format code is 0 for plain text, 1 for HTML etc', false),
                    'description' => new external_value(PARAM_TEXT, 'User profile description, as HTML', false),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', false),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', false),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                            )
                        ), 'User custom fields', false)
                )
            )
        );
    }
}

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
 * @package    core_user
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

/**
 * User external functions
 *
 * @package    core_user
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_user_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function create_users_parameters() {
        global $CFG;

        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'username' =>
                                new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config.'),
                            'password' =>
                                new external_value(PARAM_RAW, 'Plain text password consisting of any characters', VALUE_OPTIONAL),
                            'createpassword' =>
                                new external_value(PARAM_BOOL, 'True if password should be created and mailed to user.',
                                    VALUE_OPTIONAL),
                            'firstname' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) of the user'),
                            'lastname' =>
                                new external_value(PARAM_NOTAGS, 'The family name of the user'),
                            'email' =>
                                new external_value(PARAM_EMAIL, 'A valid and unique email address'),
                            'auth' =>
                                new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_DEFAULT,
                                    'manual', NULL_NOT_ALLOWED),
                            'idnumber' =>
                                new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                                    VALUE_DEFAULT, ''),
                            'lang' =>
                                new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_DEFAULT,
                                    $CFG->lang, NULL_NOT_ALLOWED),
                            'calendartype' =>
                                new external_value(PARAM_PLUGIN, 'Calendar type such as "gregorian", must exist on server',
                                    VALUE_DEFAULT, $CFG->calendartype, VALUE_OPTIONAL),
                            'theme' =>
                                new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server',
                                    VALUE_OPTIONAL),
                            'timezone' =>
                                new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default',
                                    VALUE_OPTIONAL),
                            'mailformat' =>
                                new external_value(PARAM_INT, 'Mail format code is 0 for plain text, 1 for HTML etc',
                                    VALUE_OPTIONAL),
                            'description' =>
                                new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city' =>
                                new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country' =>
                                new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'firstnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) phonetically of the user', VALUE_OPTIONAL),
                            'lastnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The family name phonetically of the user', VALUE_OPTIONAL),
                            'middlename' =>
                                new external_value(PARAM_NOTAGS, 'The middle name of the user', VALUE_OPTIONAL),
                            'alternatename' =>
                                new external_value(PARAM_NOTAGS, 'The alternate name of the user', VALUE_OPTIONAL),
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
     * Create one or more users.
     *
     * @throws invalid_parameter_exception
     * @param array $users An array of users to create.
     * @return array An array of arrays
     * @since Moodle 2.2
     */
    public static function create_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/lib/weblib.php");
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/user:create', $context);

        // Do basic automatic PARAM checks on incoming data, using params description.
        // If any problems are found then exceptions are thrown with helpful error messages.
        $params = self::validate_parameters(self::create_users_parameters(), array('users' => $users));

        $availableauths  = core_component::get_plugin_list('auth');
        unset($availableauths['mnet']);       // These would need mnethostid too.
        unset($availableauths['webservice']); // We do not want new webservice users for now.

        $availablethemes = core_component::get_plugin_list('theme');
        $availablelangs  = get_string_manager()->get_list_of_translations();

        $transaction = $DB->start_delegated_transaction();

        $userids = array();
        $createpassword = false;
        foreach ($params['users'] as $user) {
            // Make sure that the username doesn't already exist.
            if ($DB->record_exists('user', array('username' => $user['username'], 'mnethostid' => $CFG->mnet_localhost_id))) {
                throw new invalid_parameter_exception('Username already exists: '.$user['username']);
            }

            // Make sure auth is valid.
            if (empty($availableauths[$user['auth']])) {
                throw new invalid_parameter_exception('Invalid authentication type: '.$user['auth']);
            }

            // Make sure lang is valid.
            if (empty($availablelangs[$user['lang']])) {
                throw new invalid_parameter_exception('Invalid language code: '.$user['lang']);
            }

            // Make sure lang is valid.
            if (!empty($user['theme']) && empty($availablethemes[$user['theme']])) { // Theme is VALUE_OPTIONAL,
                                                                                     // so no default value
                                                                                     // We need to test if the client sent it
                                                                                     // => !empty($user['theme']).
                throw new invalid_parameter_exception('Invalid theme: '.$user['theme']);
            }

            // Make sure we have a password or have to create one.
            if (empty($user['password']) && empty($user['createpassword'])) {
                throw new invalid_parameter_exception('Invalid password: you must provide a password, or set createpassword.');
            }

            $user['confirmed'] = true;
            $user['mnethostid'] = $CFG->mnet_localhost_id;

            // Start of user info validation.
            // Make sure we validate current user info as handled by current GUI. See user/editadvanced_form.php func validation().
            if (!validate_email($user['email'])) {
                throw new invalid_parameter_exception('Email address is invalid: '.$user['email']);
            } else if (empty($CFG->allowaccountssameemail) &&
                    $DB->record_exists('user', array('email' => $user['email'], 'mnethostid' => $user['mnethostid']))) {
                throw new invalid_parameter_exception('Email address already exists: '.$user['email']);
            }
            // End of user info validation.

            $createpassword = !empty($user['createpassword']);
            unset($user['createpassword']);
            if ($createpassword) {
                $user['password'] = '';
                $updatepassword = false;
            } else {
                $updatepassword = true;
            }

            // Create the user data now!
            $user['id'] = user_create_user($user, $updatepassword, false);

            // Custom fields.
            if (!empty($user['customfields'])) {
                foreach ($user['customfields'] as $customfield) {
                    // Profile_save_data() saves profile file it's expecting a user with the correct id,
                    // and custom field to be named profile_field_"shortname".
                    $user["profile_field_".$customfield['type']] = $customfield['value'];
                }
                profile_save_data((object) $user);
            }

            if ($createpassword) {
                $userobject = (object)$user;
                setnew_password_and_mail($userobject);
                unset_user_preference('create_password', $userobject);
                set_user_preference('auth_forcepasswordchange', 1, $userobject);
            }

            // Trigger event.
            \core\event\user_created::create_from_userid($user['id'])->trigger();

            // Preferences.
            if (!empty($user['preferences'])) {
                foreach ($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'], $user['id']);
                }
            }

            $userids[] = array('id' => $user['id'], 'username' => $user['username']);
        }

        $transaction->allow_commit();

        return $userids;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function create_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'       => new external_value(PARAM_INT, 'user id'),
                    'username' => new external_value(PARAM_USERNAME, 'user name'),
                )
            )
        );
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function delete_users_parameters() {
        return new external_function_parameters(
            array(
                'userids' => new external_multiple_structure(new external_value(PARAM_INT, 'user ID')),
            )
        );
    }

    /**
     * Delete users
     *
     * @throws moodle_exception
     * @param array $userids
     * @return null
     * @since Moodle 2.2
     */
    public static function delete_users($userids) {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot."/user/lib.php");

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        require_capability('moodle/user:delete', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::delete_users_parameters(), array('userids' => $userids));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['userids'] as $userid) {
            $user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0), '*', MUST_EXIST);
            // Must not allow deleting of admins or self!!!
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
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function delete_users_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function update_users_parameters() {
        return new external_function_parameters(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' =>
                                new external_value(PARAM_INT, 'ID of the user'),
                            'username' =>
                                new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config.',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'password' =>
                                new external_value(PARAM_RAW, 'Plain text password consisting of any characters', VALUE_OPTIONAL,
                                    '', NULL_NOT_ALLOWED),
                            'firstname' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'lastname' =>
                                new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                            'email' =>
                                new external_value(PARAM_EMAIL, 'A valid and unique email address', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'auth' =>
                                new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL, '',
                                    NULL_NOT_ALLOWED),
                            'idnumber' =>
                                new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                                    VALUE_OPTIONAL),
                            'lang' =>
                                new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'calendartype' =>
                                new external_value(PARAM_PLUGIN, 'Calendar type such as "gregorian", must exist on server',
                                    VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
                            'theme' =>
                                new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server',
                                    VALUE_OPTIONAL),
                            'timezone' =>
                                new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default',
                                    VALUE_OPTIONAL),
                            'mailformat' =>
                                new external_value(PARAM_INT, 'Mail format code is 0 for plain text, 1 for HTML etc',
                                    VALUE_OPTIONAL),
                            'description' =>
                                new external_value(PARAM_TEXT, 'User profile description, no HTML', VALUE_OPTIONAL),
                            'city' =>
                                new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                            'country' =>
                                new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                            'firstnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The first name(s) phonetically of the user', VALUE_OPTIONAL),
                            'lastnamephonetic' =>
                                new external_value(PARAM_NOTAGS, 'The family name phonetically of the user', VALUE_OPTIONAL),
                            'middlename' =>
                                new external_value(PARAM_NOTAGS, 'The middle name of the user', VALUE_OPTIONAL),
                            'alternatename' =>
                                new external_value(PARAM_NOTAGS, 'The alternate name of the user', VALUE_OPTIONAL),
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

    /**
     * Update users
     *
     * @param array $users
     * @return null
     * @since Moodle 2.2
     */
    public static function update_users($users) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_parameters(), array('users' => $users));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['users'] as $user) {
            user_update_user($user, true, false);
            // Update user custom fields.
            if (!empty($user['customfields'])) {

                foreach ($user['customfields'] as $customfield) {
                    // Profile_save_data() saves profile file it's expecting a user with the correct id,
                    // and custom field to be named profile_field_"shortname".
                    $user["profile_field_".$customfield['type']] = $customfield['value'];
                }
                profile_save_data((object) $user);
            }

            // Trigger event.
            \core\event\user_updated::create_from_userid($user['id'])->trigger();

            // Preferences.
            if (!empty($user['preferences'])) {
                foreach ($user['preferences'] as $preference) {
                    set_user_preference($preference['type'], $preference['value'], $user['id']);
                }
            }
        }

        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.2
     */
    public static function update_users_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.4
     */
    public static function get_users_by_field_parameters() {
        return new external_function_parameters(
            array(
                'field' => new external_value(PARAM_ALPHA, 'the search field can be
                    \'id\' or \'idnumber\' or \'username\' or \'email\''),
                'values' => new external_multiple_structure(
                        new external_value(PARAM_RAW, 'the value to match'))
            )
        );
    }

    /**
     * Get user information for a unique field.
     *
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @param string $field
     * @param array $values
     * @return array An array of arrays containg user profiles.
     * @since Moodle 2.4
     */
    public static function get_users_by_field($field, $values) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::get_users_by_field_parameters(),
                array('field' => $field, 'values' => $values));

        // This array will keep all the users that are allowed to be searched,
        // according to the current user's privileges.
        $cleanedvalues = array();

        switch ($field) {
            case 'id':
                $paramtype = PARAM_INT;
                break;
            case 'idnumber':
                $paramtype = PARAM_RAW;
                break;
            case 'username':
                $paramtype = PARAM_RAW;
                break;
            case 'email':
                $paramtype = PARAM_EMAIL;
                break;
            default:
                throw new coding_exception('invalid field parameter',
                        'The search field \'' . $field . '\' is not supported, look at the web service documentation');
        }

        // Clean the values.
        foreach ($values as $value) {
            $cleanedvalue = clean_param($value, $paramtype);
            if ( $value != $cleanedvalue) {
                throw new invalid_parameter_exception('The field \'' . $field .
                        '\' value is invalid: ' . $value . '(cleaned value: '.$cleanedvalue.')');
            }
            $cleanedvalues[] = $cleanedvalue;
        }

        // Retrieve the users.
        $users = $DB->get_records_list('user', $field, $cleanedvalues, 'id');

        // Finally retrieve each users information.
        $returnedusers = array();
        foreach ($users as $user) {
            $userdetails = user_get_user_details_courses($user);

            // Return the user only if the searched field is returned.
            // Otherwise it means that the $USER was not allowed to search the returned user.
            if (!empty($userdetails) and !empty($userdetails[$field])) {
                $returnedusers[] = $userdetails;
            }
        }

        return $returnedusers;
    }

    /**
     * Returns description of method result value
     *
     * @return external_multiple_structure
     * @since Moodle 2.4
     */
    public static function get_users_by_field_returns() {
        return new external_multiple_structure(self::user_description());
    }


    /**
     * Returns description of get_users() parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_users_parameters() {
        return new external_function_parameters(
            array(
                'criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_ALPHA, 'the user column to search, expected keys (value format) are:
                                "id" (int) matching user id,
                                "lastname" (string) user last name (Note: you can use % for searching but it may be considerably slower!),
                                "firstname" (string) user first name (Note: you can use % for searching but it may be considerably slower!),
                                "idnumber" (string) matching user idnumber,
                                "username" (string) matching user username,
                                "email" (string) user email (Note: you can use % for searching but it may be considerably slower!),
                                "auth" (string) matching user auth plugin'),
                            'value' => new external_value(PARAM_RAW, 'the value to search')
                        )
                    ), 'the key/value pairs to be considered in user search. Values can not be empty.
                        Specify different keys only once (fullname => \'user1\', auth => \'manual\', ...) -
                        key occurences are forbidden.
                        The search is executed with AND operator on the criterias. Invalid criterias (keys) are ignored,
                        the search is still executed on the valid criterias.
                        You can search without criteria, but the function is not designed for it.
                        It could very slow or timeout. The function is designed to search some specific users.'
                )
            )
        );
    }

    /**
     * Retrieve matching user.
     *
     * @throws moodle_exception
     * @param array $criteria the allowed array keys are id/lastname/firstname/idnumber/username/email/auth.
     * @return array An array of arrays containing user profiles.
     * @since Moodle 2.5
     */
    public static function get_users($criteria = array()) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::get_users_parameters(),
                array('criteria' => $criteria));

        // Validate the criteria and retrieve the users.
        $users = array();
        $warnings = array();
        $sqlparams = array();
        $usedkeys = array();

        // Do not retrieve deleted users.
        $sql = ' deleted = 0';

        foreach ($params['criteria'] as $criteriaindex => $criteria) {

            // Check that the criteria has never been used.
            if (array_key_exists($criteria['key'], $usedkeys)) {
                throw new moodle_exception('keyalreadyset', '', '', null, 'The key ' . $criteria['key'] . ' can only be sent once');
            } else {
                $usedkeys[$criteria['key']] = true;
            }

            $invalidcriteria = false;
            // Clean the parameters.
            $paramtype = PARAM_RAW;
            switch ($criteria['key']) {
                case 'id':
                    $paramtype = PARAM_INT;
                    break;
                case 'idnumber':
                    $paramtype = PARAM_RAW;
                    break;
                case 'username':
                    $paramtype = PARAM_RAW;
                    break;
                case 'email':
                    // We use PARAM_RAW to allow searches with %.
                    $paramtype = PARAM_RAW;
                    break;
                case 'auth':
                    $paramtype = PARAM_AUTH;
                    break;
                case 'lastname':
                case 'firstname':
                    $paramtype = PARAM_TEXT;
                    break;
                default:
                    // Send back a warning that this search key is not supported in this version.
                    // This warning will make the function extandable without breaking clients.
                    $warnings[] = array(
                        'item' => $criteria['key'],
                        'warningcode' => 'invalidfieldparameter',
                        'message' =>
                            'The search key \'' . $criteria['key'] . '\' is not supported, look at the web service documentation'
                    );
                    // Do not add this invalid criteria to the created SQL request.
                    $invalidcriteria = true;
                    unset($params['criteria'][$criteriaindex]);
                    break;
            }

            if (!$invalidcriteria) {
                $cleanedvalue = clean_param($criteria['value'], $paramtype);

                $sql .= ' AND ';

                // Create the SQL.
                switch ($criteria['key']) {
                    case 'id':
                    case 'idnumber':
                    case 'username':
                    case 'auth':
                        $sql .= $criteria['key'] . ' = :' . $criteria['key'];
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    case 'email':
                    case 'lastname':
                    case 'firstname':
                        $sql .= $DB->sql_like($criteria['key'], ':' . $criteria['key'], false);
                        $sqlparams[$criteria['key']] = $cleanedvalue;
                        break;
                    default:
                        break;
                }
            }
        }

        $users = $DB->get_records_select('user', $sql, $sqlparams, 'id ASC');

        // Finally retrieve each users information.
        $returnedusers = array();
        foreach ($users as $user) {
            $userdetails = user_get_user_details_courses($user);

            // Return the user only if all the searched fields are returned.
            // Otherwise it means that the $USER was not allowed to search the returned user.
            if (!empty($userdetails)) {
                $validuser = true;

                foreach ($params['criteria'] as $criteria) {
                    if (empty($userdetails[$criteria['key']])) {
                        $validuser = false;
                    }
                }

                if ($validuser) {
                    $returnedusers[] = $userdetails;
                }
            }
        }

        return array('users' => $returnedusers, 'warnings' => $warnings);
    }

    /**
     * Returns description of get_users result value.
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function get_users_returns() {
        return new external_single_structure(
            array('users' => new external_multiple_structure(
                                self::user_description()
                             ),
                  'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     * @deprecated Moodle 2.5 MDL-38030 - Please do not call this function any more.
     * @see core_user_external::get_users_by_field_parameters()
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
     *
     * @param array $userids  array of user ids
     * @return array An array of arrays describing users
     * @since Moodle 2.2
     * @deprecated Moodle 2.5 MDL-38030 - Please do not call this function any more.
     * @see core_user_external::get_users_by_field()
     */
    public static function get_users_by_id($userids) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::get_users_by_id_parameters(),
                array('userids' => $userids));

        list($sqluserids, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $uselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ujoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_USER;
        $usersql = "SELECT u.* $uselect
                      FROM {user} u $ujoin
                     WHERE u.id $sqluserids";
        $users = $DB->get_recordset_sql($usersql, $params);

        $result = array();
        $hasuserupdatecap = has_capability('moodle/user:update', context_system::instance());
        foreach ($users as $user) {
            if (!empty($user->deleted)) {
                continue;
            }
            context_helper::preload_from_record($user);
            $usercontext = context_user::instance($user->id, IGNORE_MISSING);
            self::validate_context($usercontext);
            $currentuser = ($user->id == $USER->id);

            if ($userarray  = user_get_user_details($user)) {
                // Fields matching permissions from /user/editadvanced.php.
                if ($currentuser or $hasuserupdatecap) {
                    $userarray['auth']       = $user->auth;
                    $userarray['confirmed']  = $user->confirmed;
                    $userarray['idnumber']   = $user->idnumber;
                    $userarray['lang']       = $user->lang;
                    $userarray['theme']      = $user->theme;
                    $userarray['timezone']   = $user->timezone;
                    $userarray['mailformat'] = $user->mailformat;
                }
                $result[] = $userarray;
            }
        }
        $users->close();

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     * @deprecated Moodle 2.5 MDL-38030 - Please do not call this function any more.
     * @see core_user_external::get_users_by_field_returns()
     */
    public static function get_users_by_id_returns() {
        $additionalfields = array (
            'enrolledcourses' => new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'  => new external_value(PARAM_INT, 'Id of the course'),
                    'fullname'  => new external_value(PARAM_RAW, 'Fullname of the course'),
                    'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                )
            ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL));
        return new external_multiple_structure(self::user_description($additionalfields));
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_users_by_id_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function get_course_user_profiles_parameters() {
        return new external_function_parameters(
            array(
                'userlist' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid'    => new external_value(PARAM_INT, 'userid'),
                            'courseid'    => new external_value(PARAM_INT, 'courseid'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Get course participant's details
     *
     * @param array $userlist  array of user ids and according course ids
     * @return array An array of arrays describing course participants
     * @since Moodle 2.2
     */
    public static function get_course_user_profiles($userlist) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");
        $params = self::validate_parameters(self::get_course_user_profiles_parameters(), array('userlist' => $userlist));

        $userids = array();
        $courseids = array();
        foreach ($params['userlist'] as $value) {
            $userids[] = $value['userid'];
            $courseids[$value['userid']] = $value['courseid'];
        }

        // Cache all courses.
        $courses = array();
        list($sqlcourseids, $params) = $DB->get_in_or_equal(array_unique($courseids), SQL_PARAMS_NAMED);
        $cselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $cjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_COURSE;
        $coursesql = "SELECT c.* $cselect
                        FROM {course} c $cjoin
                       WHERE c.id $sqlcourseids";
        $rs = $DB->get_recordset_sql($coursesql, $params);
        foreach ($rs as $course) {
            // Adding course contexts to cache.
            context_helper::preload_from_record($course);
            // Cache courses.
            $courses[$course->id] = $course;
        }
        $rs->close();

        list($sqluserids, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $uselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ujoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
        $params['contextlevel'] = CONTEXT_USER;
        $usersql = "SELECT u.* $uselect
                      FROM {user} u $ujoin
                     WHERE u.id $sqluserids";
        $users = $DB->get_recordset_sql($usersql, $params);
        $result = array();
        foreach ($users as $user) {
            if (!empty($user->deleted)) {
                continue;
            }
            context_helper::preload_from_record($user);
            $course = $courses[$courseids[$user->id]];
            $context = context_course::instance($courseids[$user->id], IGNORE_MISSING);
            self::validate_context($context);
            if ($userarray = user_get_user_details($user, $course)) {
                $result[] = $userarray;
            }
        }

        $users->close();

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function get_course_user_profiles_returns() {
        $additionalfields = array(
            'groups' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id'  => new external_value(PARAM_INT, 'group id'),
                        'name' => new external_value(PARAM_RAW, 'group name'),
                        'description' => new external_value(PARAM_RAW, 'group description'),
                        'descriptionformat' => new external_format_value('description'),
                    )
                ), 'user groups', VALUE_OPTIONAL),
            'roles' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'roleid'       => new external_value(PARAM_INT, 'role id'),
                        'name'         => new external_value(PARAM_RAW, 'role name'),
                        'shortname'    => new external_value(PARAM_ALPHANUMEXT, 'role shortname'),
                        'sortorder'    => new external_value(PARAM_INT, 'role sortorder')
                    )
                ), 'user roles', VALUE_OPTIONAL),
            'enrolledcourses' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id'  => new external_value(PARAM_INT, 'Id of the course'),
                        'fullname'  => new external_value(PARAM_RAW, 'Fullname of the course'),
                        'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                    )
                ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
        );

        return new external_multiple_structure(self::user_description($additionalfields));
    }

    /**
     * Create user return value description.
     *
     * @param array $additionalfields some additional field
     * @return single_structure_description
     */
    public static function user_description($additionalfields = array()) {
        $userfields = array(
            'id'    => new external_value(PARAM_INT, 'ID of the user'),
            'username'    => new external_value(PARAM_RAW, 'The username', VALUE_OPTIONAL),
            'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
            'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
            'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
            'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
            'address'     => new external_value(PARAM_TEXT, 'Postal address', VALUE_OPTIONAL),
            'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
            'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
            'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
            'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
            'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
            'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
            'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
            'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
            'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
            'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
            'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
            'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
            'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
            'auth'        => new external_value(PARAM_PLUGIN, 'Auth plugins include manual, ldap, imap, etc', VALUE_OPTIONAL),
            'confirmed'   => new external_value(PARAM_INT, 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
            'lang'        => new external_value(PARAM_SAFEDIR, 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
            'calendartype' => new external_value(PARAM_PLUGIN, 'Calendar type such as "gregorian", must exist on server', VALUE_OPTIONAL),
            'theme'       => new external_value(PARAM_PLUGIN, 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'timezone'    => new external_value(PARAM_TIMEZONE, 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'mailformat'  => new external_value(PARAM_INT, 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
            'descriptionformat' => new external_format_value('description', VALUE_OPTIONAL),
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
                ), 'User custom fields (also known as user profile fields)', VALUE_OPTIONAL),
            'preferences' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                    )
            ), 'Users preferences', VALUE_OPTIONAL)
        );
        if (!empty($additionalfields)) {
            $userfields = array_merge($userfields, $additionalfields);
        }
        return new external_single_structure($userfields);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.6
     */
    public static function add_user_private_files_parameters() {
        return new external_function_parameters(
            array(
                'draftid' => new external_value(PARAM_INT, 'draft area id')
            )
        );
    }

    /**
     * Copy files from a draft area to users private files area.
     *
     * @throws invalid_parameter_exception
     * @param int $draftid Id of a draft area containing files.
     * @return array An array of warnings
     * @since Moodle 2.6
     */
    public static function add_user_private_files($draftid) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . "/user/lib.php");
        $params = self::validate_parameters(self::add_user_private_files_parameters(), array('draftid' => $draftid));

        if (isguestuser()) {
            throw new invalid_parameter_exception('Guest users cannot upload files');
        }

        $context = context_user::instance($USER->id);
        require_capability('moodle/user:manageownfiles', $context);

        $maxbytes = $CFG->userquota;
        $maxareabytes = $CFG->userquota;
        if (has_capability('moodle/user:ignoreuserquota', $context)) {
            $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
            $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
        }

        $options = array('subdirs' => 1,
                         'maxbytes' => $maxbytes,
                         'maxfiles' => -1,
                         'accepted_types' => '*',
                         'areamaxbytes' => $maxareabytes);

        file_save_draft_area_files($draftid, $context->id, 'user', 'private', 0, $options);

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     */
    public static function add_user_private_files_returns() {
        return null;
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.6
     */
    public static function add_user_device_parameters() {
        return new external_function_parameters(
            array(
                'appid'     => new external_value(PARAM_NOTAGS, 'the app id, usually something like com.moodle.moodlemobile'),
                'name'      => new external_value(PARAM_NOTAGS, 'the device name, \'occam\' or \'iPhone\' etc.'),
                'model'     => new external_value(PARAM_NOTAGS, 'the device model \'Nexus4\' or \'iPad1,1\' etc.'),
                'platform'  => new external_value(PARAM_NOTAGS, 'the device platform \'iOS\' or \'Android\' etc.'),
                'version'   => new external_value(PARAM_NOTAGS, 'the device version \'6.1.2\' or \'4.2.2\' etc.'),
                'pushid'    => new external_value(PARAM_RAW, 'the device PUSH token/key/identifier/registration id'),
                'uuid'      => new external_value(PARAM_RAW, 'the device UUID')
            )
        );
    }

    /**
     * Add a user device in Moodle database (for PUSH notifications usually).
     *
     * @throws moodle_exception
     * @param string $appid The app id, usually something like com.moodle.moodlemobile.
     * @param string $name The device name, occam or iPhone etc.
     * @param string $model The device model Nexus4 or iPad1.1 etc.
     * @param string $platform The device platform iOs or Android etc.
     * @param string $version The device version 6.1.2 or 4.2.2 etc.
     * @param string $pushid The device PUSH token/key/identifier/registration id.
     * @param string $uuid The device UUID.
     * @return array List of possible warnings.
     * @since Moodle 2.6
     */
    public static function add_user_device($appid, $name, $model, $platform, $version, $pushid, $uuid) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::add_user_device_parameters(),
                array('appid' => $appid,
                      'name' => $name,
                      'model' => $model,
                      'platform' => $platform,
                      'version' => $version,
                      'pushid' => $pushid,
                      'uuid' => $uuid
                      ));

        $warnings = array();

        // Prevent duplicate keys for users.
        if ($DB->get_record('user_devices', array('pushid' => $params['pushid'], 'userid' => $USER->id))) {
            $warnings['warning'][] = array(
                'item' => $params['pushid'],
                'warningcode' => 'existingkeyforthisuser',
                'message' => 'This key is already stored for this user'
            );
            return $warnings;
        }

        // Notice that we can have multiple devices because previously it was allowed to have repeated ones.
        // Since we don't have a clear way to decide which one is the more appropiate, we update all.
        if ($userdevices = $DB->get_records('user_devices', array('uuid' => $params['uuid'],
                'appid' => $params['appid'], 'userid' => $USER->id))) {

            foreach ($userdevices as $userdevice) {
                $userdevice->version    = $params['version'];   // Maybe the user upgraded the device.
                $userdevice->pushid     = $params['pushid'];
                $userdevice->timemodified  = time();
                $DB->update_record('user_devices', $userdevice);
            }

        } else {
            $userdevice = new stdclass;
            $userdevice->userid     = $USER->id;
            $userdevice->appid      = $params['appid'];
            $userdevice->name       = $params['name'];
            $userdevice->model      = $params['model'];
            $userdevice->platform   = $params['platform'];
            $userdevice->version    = $params['version'];
            $userdevice->pushid     = $params['pushid'];
            $userdevice->uuid       = $params['uuid'];
            $userdevice->timecreated  = time();
            $userdevice->timemodified = $userdevice->timecreated;

            if (!$DB->insert_record('user_devices', $userdevice)) {
                throw new moodle_exception("There was a problem saving in the database the device with key: " . $params['pushid']);
            }
        }

        return $warnings;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_multiple_structure
     * @since Moodle 2.6
     */
    public static function add_user_device_returns() {
        return new external_multiple_structure(
           new external_warnings()
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function remove_user_device_parameters() {
        return new external_function_parameters(
            array(
                'uuid'  => new external_value(PARAM_RAW, 'the device UUID'),
                'appid' => new external_value(PARAM_NOTAGS,
                                                'the app id, if empty devices matching the UUID for the user will be removed',
                                                VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Remove a user device from the Moodle database (for PUSH notifications usually).
     *
     * @param string $uuid The device UUID.
     * @param string $appid The app id, opitonal parameter. If empty all the devices fmatching the UUID or the user will be removed.
     * @return array List of possible warnings and removal status.
     * @since Moodle 2.9
     */
    public static function remove_user_device($uuid, $appid = "") {
        global $CFG;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::remove_user_device_parameters(), array('uuid' => $uuid, 'appid' => $appid));

        $context = context_system::instance();
        self::validate_context($context);

        // Warnings array, it can be empty at the end but is mandatory.
        $warnings = array();

        $removed = user_remove_user_device($params['uuid'], $params['appid']);

        if (!$removed) {
            $warnings[] = array(
                'item' => $params['uuid'],
                'warningcode' => 'devicedoesnotexist',
                'message' => 'The device doesn\'t exists in the database'
            );
        }

        $result = array(
            'removed' => $removed,
            'warnings' => $warnings
        );

        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_multiple_structure
     * @since Moodle 2.9
     */
    public static function remove_user_device_returns() {
        return new external_single_structure(
            array(
                'removed' => new external_value(PARAM_BOOL, 'True if removed, false if not removed because it doesn\'t exists'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_user_list_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of the course, 0 for site')
            )
        );
    }

    /**
     * Trigger the user_list_viewed event.
     *
     * @param int $courseid id of course
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_user_list($courseid) {
        global $CFG;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::view_user_list_parameters(),
                                            array(
                                                'courseid' => $courseid
                                            ));

        $warnings = array();

        if (empty($params['courseid'])) {
            $params['courseid'] = SITEID;
        }

        $course = get_course($params['courseid']);

        if ($course->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($course->id);
        }
        self::validate_context($context);

        if ($course->id == SITEID) {
            require_capability('moodle/site:viewparticipants', $context);
        } else {
            require_capability('moodle/course:viewparticipants', $context);
        }

        user_list_view($course, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_user_list_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function view_user_profile_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, 0 for current user', VALUE_REQUIRED),
                'courseid' => new external_value(PARAM_INT, 'id of the course, default site course', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Trigger the user profile viewed event.
     *
     * @param int $userid id of user
     * @param int $courseid id of course
     * @return array of warnings and status result
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function view_user_profile($userid, $courseid = 0) {
        global $CFG, $USER;
        require_once($CFG->dirroot . "/user/profile/lib.php");

        $params = self::validate_parameters(self::view_user_profile_parameters(),
                                            array(
                                                'userid' => $userid,
                                                'courseid' => $courseid
                                            ));

        $warnings = array();

        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        if (empty($params['courseid'])) {
            $params['courseid'] = SITEID;
        }

        $course = get_course($params['courseid']);
        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        if ($course->id == SITEID) {
            $coursecontext = context_system::instance();;
        } else {
            $coursecontext = context_course::instance($course->id);
        }
        self::validate_context($coursecontext);

        $currentuser = $USER->id == $user->id;
        $usercontext = context_user::instance($user->id);

        if (!$currentuser and
                !has_capability('moodle/user:viewdetails', $coursecontext) and
                !has_capability('moodle/user:viewdetails', $usercontext)) {
            throw new moodle_exception('cannotviewprofile');
        }

        // Case like user/profile.php.
        if ($course->id == SITEID) {
            profile_view($user, $usercontext);
        } else {
            // Case like user/view.php.
            if (!$currentuser and !can_access_course($course, $user, '', true)) {
                throw new moodle_exception('notenrolledprofile');
            }

            profile_view($user, $coursecontext, $course);
        }

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function view_user_profile_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

}

 /**
  * Deprecated user external functions
  *
  * @package    core_user
  * @copyright  2009 Petr Skodak
  * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
  * @since Moodle 2.0
  * @deprecated Moodle 2.2 MDL-29106 - Please do not use this class any more.
  * @see core_user_external
  */
class moodle_user_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::create_users_parameters()
     */
    public static function create_users_parameters() {
        return core_user_external::create_users_parameters();
    }

    /**
     * Create one or more users
     *
     * @param array $users  An array of users to create.
     * @return array An array of arrays
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::create_users()
     */
    public static function create_users($users) {
        return core_user_external::create_users($users);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::create_users_returns()
     */
    public static function create_users_returns() {
        return core_user_external::create_users_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function create_users_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::delete_users_parameters()
     */
    public static function delete_users_parameters() {
        return core_user_external::delete_users_parameters();
    }

    /**
     * Delete users
     *
     * @param array $userids
     * @return null
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::delete_users()
     */
    public static function delete_users($userids) {
        return core_user_external::delete_users($userids);
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::delete_users_returns()
     */
    public static function delete_users_returns() {
        return core_user_external::delete_users_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function delete_users_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::update_users_parameters()
     */
    public static function update_users_parameters() {
        return core_user_external::update_users_parameters();
    }

    /**
     * Update users
     *
     * @param array $users
     * @return null
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::update_users()
     */
    public static function update_users($users) {
        return core_user_external::update_users($users);
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::update_users_returns()
     */
    public static function update_users_returns() {
        return core_user_external::update_users_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function update_users_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_users_by_id_parameters()
     */
    public static function get_users_by_id_parameters() {
        return core_user_external::get_users_by_id_parameters();
    }

    /**
     * Get user information
     * - This function is matching the permissions of /user/profil.php
     * - It is also matching some permissions from /user/editadvanced.php for the following fields:
     *   auth, confirmed, idnumber, lang, theme, timezone, mailformat
     *
     * @param array $userids  array of user ids
     * @return array An array of arrays describing users
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_users_by_id()
     */
    public static function get_users_by_id($userids) {
        return core_user_external::get_users_by_id($userids);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.0
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_users_by_id_returns()
     */
    public static function get_users_by_id_returns() {
        return core_user_external::get_users_by_id_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_users_by_id_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_course_user_profiles_parameters()
     */
    public static function get_course_participants_by_id_parameters() {
        return core_user_external::get_course_user_profiles_parameters();
    }

    /**
     * Get course participant's details
     *
     * @param array $userlist  array of user ids and according course ids
     * @return array An array of arrays describing course participants
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_course_user_profiles()
     */
    public static function get_course_participants_by_id($userlist) {
        return core_user_external::get_course_user_profiles($userlist);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_user_external::get_course_user_profiles_returns()
     */
    public static function get_course_participants_by_id_returns() {
        return core_user_external::get_course_user_profiles_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_course_participants_by_id_is_deprecated() {
        return true;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_enrol_external::get_enrolled_users_parameters()
     */
    public static function get_users_by_courseid_parameters() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/externallib.php');
        return core_enrol_external::get_enrolled_users_parameters();
    }

    /**
     * Get course participants details
     *
     * @param int $courseid  course id
     * @param array $options options {
     *                                'name' => option name
     *                                'value' => option value
     *                               }
     * @return array An array of users
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_enrol_external::get_enrolled_users()
     */
    public static function get_users_by_courseid($courseid, $options = array()) {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/externallib.php');
        return core_enrol_external::get_enrolled_users($courseid, $options);
    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.1
     * @deprecated Moodle 2.2 MDL-29106 - Please do not call this function any more.
     * @see core_enrol_external::get_enrolled_users_returns()
     */
    public static function get_users_by_courseid_returns() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/externallib.php');
        return core_enrol_external::get_enrolled_users_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_users_by_courseid_is_deprecated() {
        return true;
    }
}

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

defined('MOODLE_INTERNAL') || die();

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
        $userfields = [
            'createpassword' => new external_value(PARAM_BOOL, 'True if password should be created and mailed to user.',
                VALUE_OPTIONAL),
            // General.
            'username' => new external_value(core_user::get_property_type('username'),
                'Username policy is defined in Moodle security config.'),
            'auth' => new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, etc',
                VALUE_DEFAULT, 'manual', core_user::get_property_null('auth')),
            'password' => new external_value(core_user::get_property_type('password'),
                'Plain text password consisting of any characters', VALUE_OPTIONAL),
            'firstname' => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user'),
            'lastname' => new external_value(core_user::get_property_type('lastname'), 'The family name of the user'),
            'email' => new external_value(core_user::get_property_type('email'), 'A valid and unique email address'),
            'maildisplay' => new external_value(core_user::get_property_type('maildisplay'), 'Email display', VALUE_OPTIONAL),
            'city' => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country' => new external_value(core_user::get_property_type('country'),
                'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
            'timezone' => new external_value(core_user::get_property_type('timezone'),
                'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'description' => new external_value(core_user::get_property_type('description'), 'User profile description, no HTML',
                VALUE_OPTIONAL),
            // Additional names.
            'firstnamephonetic' => new external_value(core_user::get_property_type('firstnamephonetic'),
                'The first name(s) phonetically of the user', VALUE_OPTIONAL),
            'lastnamephonetic' => new external_value(core_user::get_property_type('lastnamephonetic'),
                'The family name phonetically of the user', VALUE_OPTIONAL),
            'middlename' => new external_value(core_user::get_property_type('middlename'), 'The middle name of the user',
                VALUE_OPTIONAL),
            'alternatename' => new external_value(core_user::get_property_type('alternatename'), 'The alternate name of the user',
                VALUE_OPTIONAL),
            // Interests.
            'interests' => new external_value(PARAM_TEXT, 'User interests (separated by commas)', VALUE_OPTIONAL),
            // Optional.
            'idnumber' => new external_value(core_user::get_property_type('idnumber'),
                'An arbitrary ID code number perhaps from the institution', VALUE_DEFAULT, ''),
            'institution' => new external_value(core_user::get_property_type('institution'), 'institution', VALUE_OPTIONAL),
            'department' => new external_value(core_user::get_property_type('department'), 'department', VALUE_OPTIONAL),
            'phone1' => new external_value(core_user::get_property_type('phone1'), 'Phone 1', VALUE_OPTIONAL),
            'phone2' => new external_value(core_user::get_property_type('phone2'), 'Phone 2', VALUE_OPTIONAL),
            'address' => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            // Other user preferences stored in the user table.
            'lang' => new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server',
                VALUE_DEFAULT, core_user::get_property_default('lang'), core_user::get_property_null('lang')),
            'calendartype' => new external_value(core_user::get_property_type('calendartype'),
                'Calendar type such as "gregorian", must exist on server', VALUE_DEFAULT, $CFG->calendartype, VALUE_OPTIONAL),
            'theme' => new external_value(core_user::get_property_type('theme'),
                'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'mailformat' => new external_value(core_user::get_property_type('mailformat'),
                'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            // Custom user profile fields.
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                    ]
                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
            // User preferences.
            'preferences' => new external_multiple_structure(
            new external_single_structure(
                [
                    'type'  => new external_value(PARAM_RAW, 'The name of the preference'),
                    'value' => new external_value(PARAM_RAW, 'The value of the preference')
                ]
            ), 'User preferences', VALUE_OPTIONAL),
        ];
        return new external_function_parameters(
            [
                'users' => new external_multiple_structure(
                    new external_single_structure($userfields)
                )
            ]
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
        require_once($CFG->dirroot."/user/editlib.php");
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
        foreach ($params['users'] as $user) {
            // Make sure that the username, firstname and lastname are not blank.
            foreach (array('username', 'firstname', 'lastname') as $fieldname) {
                if (trim($user[$fieldname]) === '') {
                    throw new invalid_parameter_exception('The field '.$fieldname.' cannot be blank');
                }
            }

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
            $authplugin = get_auth_plugin($user['auth']);
            if ($authplugin->is_internal() && empty($user['password']) && empty($user['createpassword'])) {
                throw new invalid_parameter_exception('Invalid password: you must provide a password, or set createpassword.');
            }

            $user['confirmed'] = true;
            $user['mnethostid'] = $CFG->mnet_localhost_id;

            // Start of user info validation.
            // Make sure we validate current user info as handled by current GUI. See user/editadvanced_form.php func validation().
            if (!validate_email($user['email'])) {
                throw new invalid_parameter_exception('Email address is invalid: '.$user['email']);
            } else if (empty($CFG->allowaccountssameemail)) {
                // Make a case-insensitive query for the given email address.
                $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid';
                $params = array(
                    'email' => $user['email'],
                    'mnethostid' => $user['mnethostid']
                );
                // If there are other user(s) that already have the same email, throw an error.
                if ($DB->record_exists_select('user', $select, $params)) {
                    throw new invalid_parameter_exception('Email address already exists: '.$user['email']);
                }
            }
            // End of user info validation.

            $createpassword = !empty($user['createpassword']);
            unset($user['createpassword']);
            $updatepassword = false;
            if ($authplugin->is_internal()) {
                if ($createpassword) {
                    $user['password'] = '';
                } else {
                    $updatepassword = true;
                }
            } else {
                $user['password'] = AUTH_PASSWORD_NOT_CACHED;
            }

            // Create the user data now!
            $user['id'] = user_create_user($user, $updatepassword, false);

            $userobject = (object)$user;

            // Set user interests.
            if (!empty($user['interests'])) {
                $trimmedinterests = array_map('trim', explode(',', $user['interests']));
                $interests = array_filter($trimmedinterests, function($value) {
                    return !empty($value);
                });
                useredit_update_interests($userobject, $interests);
            }

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
                setnew_password_and_mail($userobject);
                unset_user_preference('create_password', $userobject);
                set_user_preference('auth_forcepasswordchange', 1, $userobject);
            }

            // Trigger event.
            \core\event\user_created::create_from_userid($user['id'])->trigger();

            // Preferences.
            if (!empty($user['preferences'])) {
                $userpref = (object)$user;
                foreach ($user['preferences'] as $preference) {
                    $userpref->{'preference_'.$preference['type']} = $preference['value'];
                }
                useredit_update_user_preference($userpref);
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
                    'id'       => new external_value(core_user::get_property_type('id'), 'user id'),
                    'username' => new external_value(core_user::get_property_type('username'), 'user name'),
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
                'userids' => new external_multiple_structure(new external_value(core_user::get_property_type('id'), 'user ID')),
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
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function update_user_preferences_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'id of the user, default to current user', VALUE_DEFAULT, 0),
                'emailstop' => new external_value(core_user::get_property_type('emailstop'),
                    'Enable or disable notifications for this user', VALUE_DEFAULT, null),
                'preferences' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'type'  => new external_value(PARAM_RAW, 'The name of the preference'),
                            'value' => new external_value(PARAM_RAW, 'The value of the preference, do not set this field if you
                                want to remove (unset) the current value.', VALUE_DEFAULT, null),
                        )
                    ), 'User preferences', VALUE_DEFAULT, array()
                )
            )
        );
    }

    /**
     * Update the user's preferences.
     *
     * @param int $userid
     * @param bool|null $emailstop
     * @param array $preferences
     * @return null
     * @since Moodle 3.2
     */
    public static function update_user_preferences($userid = 0, $emailstop = null, $preferences = array()) {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/user/editlib.php');
        require_once($CFG->dirroot . '/message/lib.php');

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $systemcontext = context_system::instance();
        self::validate_context($systemcontext);
        $params = array(
            'userid' => $userid,
            'emailstop' => $emailstop,
            'preferences' => $preferences
        );
        $params = self::validate_parameters(self::update_user_preferences_parameters(), $params);
        $preferences = $params['preferences'];

        // Preferences.
        if (!empty($preferences)) {
            $userpref = ['id' => $userid];
            foreach ($preferences as $preference) {
                $userpref['preference_' . $preference['type']] = $preference['value'];
            }
            useredit_update_user_preference($userpref);
        }

        // Check if they want to update the email.
        if ($emailstop !== null) {
            $otheruser = ($userid == $USER->id) ? $USER : core_user::get_user($userid, '*', MUST_EXIST);
            core_user::require_active_user($otheruser);
            if (core_message_can_edit_message_profile($otheruser) && $otheruser->emailstop != $emailstop) {
                $user = new stdClass();
                $user->id = $userid;
                $user->emailstop = $emailstop;
                user_update_user($user);

                // Update the $USER if we should.
                if ($userid == $USER->id) {
                    $USER->emailstop = $emailstop;
                }
            }
        }

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return null
     * @since Moodle 3.2
     */
    public static function update_user_preferences_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.2
     */
    public static function update_users_parameters() {
        $userfields = [
            'id' => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            // General.
            'username' => new external_value(core_user::get_property_type('username'),
                'Username policy is defined in Moodle security config.', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'auth' => new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, etc',
                VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'suspended' => new external_value(core_user::get_property_type('suspended'),
                'Suspend user account, either false to enable user login or true to disable it', VALUE_OPTIONAL),
            'password' => new external_value(core_user::get_property_type('password'),
                'Plain text password consisting of any characters', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'firstname' => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user',
                VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'lastname' => new external_value(core_user::get_property_type('lastname'), 'The family name of the user',
                VALUE_OPTIONAL),
            'email' => new external_value(core_user::get_property_type('email'), 'A valid and unique email address', VALUE_OPTIONAL,
                '', NULL_NOT_ALLOWED),
            'maildisplay' => new external_value(core_user::get_property_type('maildisplay'), 'Email display', VALUE_OPTIONAL),
            'city' => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country' => new external_value(core_user::get_property_type('country'),
                'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
            'timezone' => new external_value(core_user::get_property_type('timezone'),
                'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'description' => new external_value(core_user::get_property_type('description'), 'User profile description, no HTML',
                VALUE_OPTIONAL),
            // User picture.
            'userpicture' => new external_value(PARAM_INT,
                'The itemid where the new user picture has been uploaded to, 0 to delete', VALUE_OPTIONAL),
            // Additional names.
            'firstnamephonetic' => new external_value(core_user::get_property_type('firstnamephonetic'),
                'The first name(s) phonetically of the user', VALUE_OPTIONAL),
            'lastnamephonetic' => new external_value(core_user::get_property_type('lastnamephonetic'),
                'The family name phonetically of the user', VALUE_OPTIONAL),
            'middlename' => new external_value(core_user::get_property_type('middlename'), 'The middle name of the user',
                VALUE_OPTIONAL),
            'alternatename' => new external_value(core_user::get_property_type('alternatename'), 'The alternate name of the user',
                VALUE_OPTIONAL),
            // Interests.
            'interests' => new external_value(PARAM_TEXT, 'User interests (separated by commas)', VALUE_OPTIONAL),
            // Optional.
            'idnumber' => new external_value(core_user::get_property_type('idnumber'),
                'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
            'institution' => new external_value(core_user::get_property_type('institution'), 'Institution', VALUE_OPTIONAL),
            'department' => new external_value(core_user::get_property_type('department'), 'Department', VALUE_OPTIONAL),
            'phone1' => new external_value(core_user::get_property_type('phone1'), 'Phone', VALUE_OPTIONAL),
            'phone2' => new external_value(core_user::get_property_type('phone2'), 'Mobile phone', VALUE_OPTIONAL),
            'address' => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            // Other user preferences stored in the user table.
            'lang' => new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server',
                VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'calendartype' => new external_value(core_user::get_property_type('calendartype'),
                'Calendar type such as "gregorian", must exist on server', VALUE_OPTIONAL, '', NULL_NOT_ALLOWED),
            'theme' => new external_value(core_user::get_property_type('theme'),
                'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'mailformat' => new external_value(core_user::get_property_type('mailformat'),
                'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            // Custom user profile fields.
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the custom field'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field')
                    ]
                ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
            // User preferences.
            'preferences' => new external_multiple_structure(
                new external_single_structure(
                    [
                        'type'  => new external_value(PARAM_RAW, 'The name of the preference'),
                        'value' => new external_value(PARAM_RAW, 'The value of the preference')
                    ]
                ), 'User preferences', VALUE_OPTIONAL),
        ];
        return new external_function_parameters(
            [
                'users' => new external_multiple_structure(
                    new external_single_structure($userfields)
                )
            ]
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
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot."/user/lib.php");
        require_once($CFG->dirroot."/user/profile/lib.php"); // Required for customfields related function.
        require_once($CFG->dirroot.'/user/editlib.php');

        // Ensure the current user is allowed to run this function.
        $context = context_system::instance();
        require_capability('moodle/user:update', $context);
        self::validate_context($context);

        $params = self::validate_parameters(self::update_users_parameters(), array('users' => $users));

        $filemanageroptions = array('maxbytes' => $CFG->maxbytes,
                'subdirs'        => 0,
                'maxfiles'       => 1,
                'accepted_types' => 'optimised_image');

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['users'] as $user) {
            // First check the user exists.
            if (!$existinguser = core_user::get_user($user['id'])) {
                continue;
            }
            // Check if we are trying to update an admin.
            if ($existinguser->id != $USER->id and is_siteadmin($existinguser) and !is_siteadmin($USER)) {
                continue;
            }
            // Other checks (deleted, remote or guest users).
            if ($existinguser->deleted or is_mnet_remote_user($existinguser) or isguestuser($existinguser->id)) {
                continue;
            }
            // Check duplicated emails.
            if (isset($user['email']) && $user['email'] !== $existinguser->email) {
                if (!validate_email($user['email'])) {
                    continue;
                } else if (empty($CFG->allowaccountssameemail)) {
                    // Make a case-insensitive query for the given email address and make sure to exclude the user being updated.
                    $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND id <> :userid';
                    $params = array(
                        'email' => $user['email'],
                        'mnethostid' => $CFG->mnet_localhost_id,
                        'userid' => $user['id']
                    );
                    // Skip if there are other user(s) that already have the same email.
                    if ($DB->record_exists_select('user', $select, $params)) {
                        continue;
                    }
                }
            }

            user_update_user($user, true, false);

            $userobject = (object)$user;

            // Update user picture if it was specified for this user.
            if (empty($CFG->disableuserimages) && isset($user['userpicture'])) {
                $userobject->deletepicture = null;

                if ($user['userpicture'] == 0) {
                    $userobject->deletepicture = true;
                } else {
                    $userobject->imagefile = $user['userpicture'];
                }

                core_user::update_picture($userobject, $filemanageroptions);
            }

            // Update user interests.
            if (!empty($user['interests'])) {
                $trimmedinterests = array_map('trim', explode(',', $user['interests']));
                $interests = array_filter($trimmedinterests, function($value) {
                    return !empty($value);
                });
                useredit_update_interests($userobject, $interests);
            }

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
                $userpref = clone($existinguser);
                foreach ($user['preferences'] as $preference) {
                    $userpref->{'preference_'.$preference['type']} = $preference['value'];
                }
                useredit_update_user_preference($userpref);
            }
            if (isset($user['suspended']) and $user['suspended']) {
                \core\session\manager::kill_user_sessions($user['id']);
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
                $paramtype = core_user::get_property_type('id');
                break;
            case 'idnumber':
                $paramtype = core_user::get_property_type('idnumber');
                break;
            case 'username':
                $paramtype = core_user::get_property_type('username');
                break;
            case 'email':
                $paramtype = core_user::get_property_type('email');
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

        $context = context_system::instance();
        self::validate_context($context);

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
                    $paramtype = core_user::get_property_type('id');
                    break;
                case 'idnumber':
                    $paramtype = core_user::get_property_type('idnumber');
                    break;
                case 'username':
                    $paramtype = core_user::get_property_type('username');
                    break;
                case 'email':
                    // We use PARAM_RAW to allow searches with %.
                    $paramtype = core_user::get_property_type('email');
                    break;
                case 'auth':
                    $paramtype = core_user::get_property_type('auth');
                    break;
                case 'lastname':
                case 'firstname':
                    $paramtype = core_user::get_property_type('firstname');
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
     */
    public static function get_course_user_profiles_parameters() {
        return new external_function_parameters(
            array(
                'userlist' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid'    => new external_value(core_user::get_property_type('id'), 'userid'),
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
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'username'    => new external_value(core_user::get_property_type('username'), 'The username', VALUE_OPTIONAL),
            'firstname'   => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user', VALUE_OPTIONAL),
            'lastname'    => new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),
            'fullname'    => new external_value(core_user::get_property_type('firstname'), 'The fullname of the user'),
            'email'       => new external_value(core_user::get_property_type('email'), 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
            'address'     => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            'phone1'      => new external_value(core_user::get_property_type('phone1'), 'Phone 1', VALUE_OPTIONAL),
            'phone2'      => new external_value(core_user::get_property_type('phone2'), 'Phone 2', VALUE_OPTIONAL),
            'department'  => new external_value(core_user::get_property_type('department'), 'department', VALUE_OPTIONAL),
            'institution' => new external_value(core_user::get_property_type('institution'), 'institution', VALUE_OPTIONAL),
            'idnumber'    => new external_value(core_user::get_property_type('idnumber'), 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
            'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
            'firstaccess' => new external_value(core_user::get_property_type('firstaccess'), 'first access to the site (0 if never)', VALUE_OPTIONAL),
            'lastaccess'  => new external_value(core_user::get_property_type('lastaccess'), 'last access to the site (0 if never)', VALUE_OPTIONAL),
            'auth'        => new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, etc', VALUE_OPTIONAL),
            'suspended'   => new external_value(core_user::get_property_type('suspended'), 'Suspend user account, either false to enable user login or true to disable it', VALUE_OPTIONAL),
            'confirmed'   => new external_value(core_user::get_property_type('confirmed'), 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
            'lang'        => new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
            'calendartype' => new external_value(core_user::get_property_type('calendartype'), 'Calendar type such as "gregorian", must exist on server', VALUE_OPTIONAL),
            'theme'       => new external_value(core_user::get_property_type('theme'), 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'timezone'    => new external_value(core_user::get_property_type('timezone'), 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'mailformat'  => new external_value(core_user::get_property_type('mailformat'), 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            'description' => new external_value(core_user::get_property_type('description'), 'User profile description', VALUE_OPTIONAL),
            'descriptionformat' => new external_format_value(core_user::get_property_type('descriptionformat'), VALUE_OPTIONAL),
            'city'        => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country'     => new external_value(core_user::get_property_type('country'), 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
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
                        'name'  => new external_value(PARAM_RAW, 'The name of the preferences'),
                        'value' => new external_value(PARAM_RAW, 'The value of the preference'),
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
        global $CFG, $USER;
        require_once($CFG->libdir . "/filelib.php");

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
                         'areamaxbytes' => $maxareabytes);

        file_merge_files_from_draft_area_into_filearea($draftid, $context->id, 'user', 'private', 0, $options);

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
        require_once($CFG->dirroot . '/course/lib.php');

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

        course_require_view_participants($context);

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

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function get_user_preferences_parameters() {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_RAW, 'preference name, empty for all', VALUE_DEFAULT, ''),
                'userid' => new external_value(PARAM_INT, 'id of the user, default to current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Return user preferences.
     *
     * @param string $name preference name, empty for all
     * @param int $userid id of the user, 0 for current user
     * @return array of warnings and preferences
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function get_user_preferences($name = '', $userid = 0) {
        global $USER;

        $params = self::validate_parameters(self::get_user_preferences_parameters(),
                                            array(
                                                'name' => $name,
                                                'userid' => $userid
                                            ));
        $preferences = array();
        $warnings = array();

        $context = context_system::instance();
        self::validate_context($context);

        if (empty($params['name'])) {
            $name = null;
        }
        if (empty($params['userid'])) {
            $user = null;
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            if ($user->id != $USER->id) {
                // Only admins can retrieve other users preferences.
                require_capability('moodle/site:config', $context);
            }
        }

        $userpreferences = get_user_preferences($name, null, $user);
        // Check if we received just one preference.
        if (!is_array($userpreferences)) {
            $userpreferences = array($name => $userpreferences);
        }

        foreach ($userpreferences as $name => $value) {
            $preferences[] = array(
                'name' => $name,
                'value' => $value,
            );
        }

        $result = array();
        $result['preferences'] = $preferences;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function get_user_preferences_returns() {
        return new external_single_structure(
            array(
                'preferences' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                            'value' => new external_value(PARAM_RAW, 'The value of the preference'),
                        )
                    ),
                    'User custom fields (also known as user profile fields)'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function update_picture_parameters() {
        return new external_function_parameters(
            array(
                'draftitemid' => new external_value(PARAM_INT, 'Id of the user draft file to use as image'),
                'delete' => new external_value(PARAM_BOOL, 'If we should delete the user picture', VALUE_DEFAULT, false),
                'userid' => new external_value(PARAM_INT, 'Id of the user, 0 for current user', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Update or delete the user picture in the site
     *
     * @param  int  $draftitemid id of the user draft file to use as image
     * @param  bool $delete      if we should delete the user picture
     * @param  int $userid       id of the user, 0 for current user
     * @return array warnings and success status
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function update_picture($draftitemid, $delete = false, $userid = 0) {
        global $CFG, $USER, $PAGE;

        $params = self::validate_parameters(
            self::update_picture_parameters(),
            array(
                'draftitemid' => $draftitemid,
                'delete' => $delete,
                'userid' => $userid
            )
        );

        $context = context_system::instance();
        self::validate_context($context);

        if (!empty($CFG->disableuserimages)) {
            throw new moodle_exception('userimagesdisabled', 'admin');
        }

        if (empty($params['userid']) or $params['userid'] == $USER->id) {
            $user = $USER;
            require_capability('moodle/user:editownprofile', $context);
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            $personalcontext = context_user::instance($user->id);

            require_capability('moodle/user:editprofile', $personalcontext);
            if (is_siteadmin($user) and !is_siteadmin($USER)) {  // Only admins may edit other admins.
                throw new moodle_exception('useradmineditadmin');
            }
        }

        // Load the appropriate auth plugin.
        $userauth = get_auth_plugin($user->auth);
        if (is_mnet_remote_user($user) or !$userauth->can_edit_profile() or $userauth->edit_profile_url()) {
            throw new moodle_exception('noprofileedit', 'auth');
        }

        $filemanageroptions = array(
            'maxbytes' => $CFG->maxbytes,
            'subdirs' => 0,
            'maxfiles' => 1,
            'accepted_types' => 'optimised_image'
        );
        $user->deletepicture = $params['delete'];
        $user->imagefile = $params['draftitemid'];
        $success = core_user::update_picture($user, $filemanageroptions);

        $result = array(
            'success' => $success,
            'warnings' => array(),
        );
        if ($success) {
            $userpicture = new user_picture(core_user::get_user($user->id));
            $userpicture->size = 1; // Size f1.
            $result['profileimageurl'] = $userpicture->get_url($PAGE)->out(false);
        }
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function update_picture_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'True if the image was updated, false otherwise.'),
                'profileimageurl' => new external_value(PARAM_URL, 'New profile user image url', VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function set_user_preferences_parameters() {
        return new external_function_parameters(
            array(
                'preferences' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                            'value' => new external_value(PARAM_RAW, 'The value of the preference'),
                            'userid' => new external_value(PARAM_INT, 'Id of the user to set the preference'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Set user preferences.
     *
     * @param array $preferences list of preferences including name, value and userid
     * @return array of warnings and preferences saved
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function set_user_preferences($preferences) {
        global $USER;

        $params = self::validate_parameters(self::set_user_preferences_parameters(), array('preferences' => $preferences));
        $warnings = array();
        $saved = array();

        $context = context_system::instance();
        self::validate_context($context);

        $userscache = array();
        foreach ($params['preferences'] as $pref) {
            // Check to which user set the preference.
            if (!empty($userscache[$pref['userid']])) {
                $user = $userscache[$pref['userid']];
            } else {
                try {
                    $user = core_user::get_user($pref['userid'], '*', MUST_EXIST);
                    core_user::require_active_user($user);
                    $userscache[$pref['userid']] = $user;
                } catch (Exception $e) {
                    $warnings[] = array(
                        'item' => 'user',
                        'itemid' => $pref['userid'],
                        'warningcode' => 'invaliduser',
                        'message' => $e->getMessage()
                    );
                    continue;
                }
            }

            try {
                if (core_user::can_edit_preference($pref['name'], $user)) {
                    $value = core_user::clean_preference($pref['value'], $pref['name']);
                    set_user_preference($pref['name'], $value, $user->id);
                    $saved[] = array(
                        'name' => $pref['name'],
                        'userid' => $user->id,
                    );
                } else {
                    $warnings[] = array(
                        'item' => 'user',
                        'itemid' => $user->id,
                        'warningcode' => 'nopermission',
                        'message' => 'You are not allowed to change the preference '.s($pref['name']).' for user '.$user->id
                    );
                }
            } catch (Exception $e) {
                $warnings[] = array(
                    'item' => 'user',
                    'itemid' => $user->id,
                    'warningcode' => 'errorsavingpreference',
                    'message' => $e->getMessage()
                );
            }
        }

        $result = array();
        $result['saved'] = $saved;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function set_user_preferences_returns() {
        return new external_single_structure(
            array(
                'saved' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'The name of the preference'),
                            'userid' => new external_value(PARAM_INT, 'The user the preference was set for'),
                        )
                    ), 'Preferences saved'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.2
     */
    public static function agree_site_policy_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Agree the site policy for the current user.
     *
     * @return array of warnings and status result
     * @since Moodle 3.2
     * @throws moodle_exception
     */
    public static function agree_site_policy() {
        global $CFG, $DB, $USER;

        $warnings = array();

        $context = context_system::instance();
        try {
            // We expect an exception here since the user didn't agree the site policy yet.
            self::validate_context($context);
        } catch (Exception $e) {
            // We are expecting only a sitepolicynotagreed exception.
            if (!($e instanceof moodle_exception) or $e->errorcode != 'sitepolicynotagreed') {
                // In case we receive a different exception, throw it.
                throw $e;
            }
        }

        $manager = new \core_privacy\local\sitepolicy\manager();
        if (!empty($USER->policyagreed)) {
            $status = false;
            $warnings[] = array(
                'item' => 'user',
                'itemid' => $USER->id,
                'warningcode' => 'alreadyagreed',
                'message' => 'The user already agreed the site policy.'
            );
        } else if (!$manager->is_defined()) {
            $status = false;
            $warnings[] = array(
                'item' => 'user',
                'itemid' => $USER->id,
                'warningcode' => 'nositepolicy',
                'message' => 'The site does not have a site policy configured.'
            );
        } else {
            $status = $manager->accept();
        }

        $result = array();
        $result['status'] = $status;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function agree_site_policy_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true only if we set the policyagreed to 1 for the user'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function get_private_files_info_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'Id of the user, default to current user.', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Returns general information about files in the user private files area.
     *
     * @param int $userid Id of the user, default to current user.
     * @return array of warnings and file area information
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function get_private_files_info($userid = 0) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/filelib.php');

        $params = self::validate_parameters(self::get_private_files_info_parameters(), array('userid' => $userid));
        $warnings = array();

        $context = context_system::instance();
        self::validate_context($context);

        if (empty($params['userid']) || $params['userid'] == $USER->id) {
            $usercontext = context_user::instance($USER->id);
            require_capability('moodle/user:manageownfiles', $usercontext);
        } else {
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            // Only admins can retrieve other users information.
            require_capability('moodle/site:config', $context);
            $usercontext = context_user::instance($user->id);
        }

        $fileareainfo = file_get_file_area_info($usercontext->id, 'user', 'private');

        $result = array();
        $result['filecount'] = $fileareainfo['filecount'];
        $result['foldercount'] = $fileareainfo['foldercount'];
        $result['filesize'] = $fileareainfo['filesize'];
        $result['filesizewithoutreferences'] = $fileareainfo['filesize_without_references'];
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function get_private_files_info_returns() {
        return new external_single_structure(
            array(
                'filecount' => new external_value(PARAM_INT, 'Number of files in the area.'),
                'foldercount' => new external_value(PARAM_INT, 'Number of folders in the area.'),
                'filesize' => new external_value(PARAM_INT, 'Total size of the files in the area.'),
                'filesizewithoutreferences' => new external_value(PARAM_INT, 'Total size of the area excluding file references'),
                'warnings' => new external_warnings()
            )
        );
    }
}

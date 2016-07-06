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
 * User class
 *
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * User class to access user details.
 *
 * @todo       move api's from user/lib.php and depreciate old ones.
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_user {
    /**
     * No reply user id.
     */
    const NOREPLY_USER = -10;

    /**
     * Support user id.
     */
    const SUPPORT_USER = -20;

    /** @var stdClass keep record of noreply user */
    public static $noreplyuser = false;

    /** @var stdClass keep record of support user */
    public static $supportuser = false;

    /** @var array store user fields properties cache. */
    protected static $propertiescache = null;

    /**
     * Return user object from db or create noreply or support user,
     * if userid matches corse_user::NOREPLY_USER or corse_user::SUPPORT_USER
     * respectively. If userid is not found, then return false.
     *
     * @param int $userid user id
     * @param string $fields A comma separated list of user fields to be returned, support and noreply user
     *                       will not be filtered by this.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
     *                        MUST_EXIST means throw an exception if no user record or multiple records found.
     * @return stdClass|bool user record if found, else false.
     * @throws dml_exception if user record not found and respective $strictness is set.
     */
    public static function get_user($userid, $fields = '*', $strictness = IGNORE_MISSING) {
        global $DB;

        // If noreply user then create fake record and return.
        switch ($userid) {
            case self::NOREPLY_USER:
                return self::get_noreply_user();
                break;
            case self::SUPPORT_USER:
                return self::get_support_user();
                break;
            default:
                return $DB->get_record('user', array('id' => $userid), $fields, $strictness);
        }
    }


    /**
     * Return user object from db based on their username.
     *
     * @param string $username The username of the user searched.
     * @param string $fields A comma separated list of user fields to be returned, support and noreply user.
     * @param int $mnethostid The id of the remote host.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
     *                        MUST_EXIST means throw an exception if no user record or multiple records found.
     * @return stdClass|bool user record if found, else false.
     * @throws dml_exception if user record not found and respective $strictness is set.
     */
    public static function get_user_by_username($username, $fields = '*', $mnethostid = null, $strictness = IGNORE_MISSING) {
        global $DB, $CFG;

        // Because we use the username as the search criteria, we must also restrict our search based on mnet host.
        if (empty($mnethostid)) {
            // If empty, we restrict to local users.
            $mnethostid = $CFG->mnet_localhost_id;
        }

        return $DB->get_record('user', array('username' => $username, 'mnethostid' => $mnethostid), $fields, $strictness);
    }

    /**
     * Helper function to return dummy noreply user record.
     *
     * @return stdClass
     */
    protected static function get_dummy_user_record() {
        global $CFG;

        $dummyuser = new stdClass();
        $dummyuser->id = self::NOREPLY_USER;
        $dummyuser->email = $CFG->noreplyaddress;
        $dummyuser->firstname = get_string('noreplyname');
        $dummyuser->username = 'noreply';
        $dummyuser->lastname = '';
        $dummyuser->confirmed = 1;
        $dummyuser->suspended = 0;
        $dummyuser->deleted = 0;
        $dummyuser->picture = 0;
        $dummyuser->auth = 'manual';
        $dummyuser->firstnamephonetic = '';
        $dummyuser->lastnamephonetic = '';
        $dummyuser->middlename = '';
        $dummyuser->alternatename = '';
        $dummyuser->imagealt = '';
        return $dummyuser;
    }

    /**
     * Return noreply user record, this is currently used in messaging
     * system only for sending messages from noreply email.
     * It will return record of $CFG->noreplyuserid if set else return dummy
     * user object with hard-coded $user->emailstop = 1 so noreply can be sent to user.
     *
     * @return stdClass user record.
     */
    public static function get_noreply_user() {
        global $CFG;

        if (!empty(self::$noreplyuser)) {
            return self::$noreplyuser;
        }

        // If noreply user is set then use it, else create one.
        if (!empty($CFG->noreplyuserid)) {
            self::$noreplyuser = self::get_user($CFG->noreplyuserid);
            self::$noreplyuser->emailstop = 1; // Force msg stop for this user.
            return self::$noreplyuser;
        } else {
            // Do not cache the dummy user record to avoid language internationalization issues.
            $noreplyuser = self::get_dummy_user_record();
            $noreplyuser->maildisplay = '1'; // Show to all.
            $noreplyuser->emailstop = 1;
            return $noreplyuser;
        }
    }

    /**
     * Return support user record, this is currently used in messaging
     * system only for sending messages to support email.
     * $CFG->supportuserid is set then returns user record
     * $CFG->supportemail is set then return dummy record with $CFG->supportemail
     * else return admin user record with hard-coded $user->emailstop = 0, so user
     * gets support message.
     *
     * @return stdClass user record.
     */
    public static function get_support_user() {
        global $CFG;

        if (!empty(self::$supportuser)) {
            return self::$supportuser;
        }

        // If custom support user is set then use it, else if supportemail is set then use it, else use noreply.
        if (!empty($CFG->supportuserid)) {
            self::$supportuser = self::get_user($CFG->supportuserid, '*', MUST_EXIST);
        } else if (empty(self::$supportuser) && !empty($CFG->supportemail)) {
            // Try sending it to support email if support user is not set.
            $supportuser = self::get_dummy_user_record();
            $supportuser->id = self::SUPPORT_USER;
            $supportuser->email = $CFG->supportemail;
            if ($CFG->supportname) {
                $supportuser->firstname = $CFG->supportname;
            }
            $supportuser->username = 'support';
            $supportuser->maildisplay = '1'; // Show to all.
            // Unset emailstop to make sure support message is sent.
            $supportuser->emailstop = 0;
            return $supportuser;
        }

        // Send support msg to admin user if nothing is set above.
        if (empty(self::$supportuser)) {
            self::$supportuser = get_admin();
        }

        // Unset emailstop to make sure support message is sent.
        self::$supportuser->emailstop = 0;
        return self::$supportuser;
    }

    /**
     * Reset self::$noreplyuser and self::$supportuser.
     * This is only used by phpunit, and there is no other use case for this function.
     * Please don't use it outside phpunit.
     */
    public static function reset_internal_users() {
        if (PHPUNIT_TEST) {
            self::$noreplyuser = false;
            self::$supportuser = false;
        } else {
            debugging('reset_internal_users() should not be used outside phpunit.', DEBUG_DEVELOPER);
        }
    }

    /**
     * Return true is user id is greater than self::NOREPLY_USER and
     * alternatively check db.
     *
     * @param int $userid user id.
     * @param bool $checkdb if true userid will be checked in db. By default it's false, and
     *                      userid is compared with NOREPLY_USER for performance.
     * @return bool true is real user else false.
     */
    public static function is_real_user($userid, $checkdb = false) {
        global $DB;

        if ($userid < 0) {
            return false;
        }
        if ($checkdb) {
            return $DB->record_exists('user', array('id' => $userid));
        } else {
            return true;
        }
    }

    /**
     * Check if the given user is an active user in the site.
     *
     * @param  stdClass  $user         user object
     * @param  boolean $checksuspended whether to check if the user has the account suspended
     * @param  boolean $checknologin   whether to check if the user uses the nologin auth method
     * @throws moodle_exception
     * @since  Moodle 3.0
     */
    public static function require_active_user($user, $checksuspended = false, $checknologin = false) {

        if (!self::is_real_user($user->id)) {
            throw new moodle_exception('invaliduser', 'error');
        }

        if ($user->deleted) {
            throw new moodle_exception('userdeleted');
        }

        if (empty($user->confirmed)) {
            throw new moodle_exception('usernotconfirmed', 'moodle', '', $user->username);
        }

        if (isguestuser($user)) {
            throw new moodle_exception('guestsarenotallowed', 'error');
        }

        if ($checksuspended and $user->suspended) {
            throw new moodle_exception('suspended', 'auth');
        }

        if ($checknologin and $user->auth == 'nologin') {
            throw new moodle_exception('suspended', 'auth');
        }
    }

    /**
     * Definition of user profile fields and the expected parameter type for data validation.
     *
     * array(
     *     'property_name' => array(       // The user property to be checked. Should match the field on the user table.
     *          'null' => NULL_ALLOWED,    // Defaults to NULL_NOT_ALLOWED. Takes NULL_NOT_ALLOWED or NULL_ALLOWED.
     *          'type' => PARAM_TYPE,      // Expected parameter type of the user field.
     *          'choices' => array(1, 2..) // An array of accepted values of the user field.
     *          'default' => $CFG->setting // An default value for the field.
     *     )
     * )
     *
     * The fields choices and default are optional.
     *
     * @return void
     */
    protected static function fill_properties_cache() {
        global $CFG;
        if (self::$propertiescache !== null) {
            return;
        }

        // Array of user fields properties and expected parameters.
        // Every new field on the user table should be added here otherwise it won't be validated.
        $fields = array();
        $fields['id'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['auth'] = array('type' => PARAM_AUTH, 'null' => NULL_NOT_ALLOWED);
        $fields['confirmed'] = array('type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED);
        $fields['policyagreed'] = array('type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED);
        $fields['deleted'] = array('type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED);
        $fields['suspended'] = array('type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED);
        $fields['mnethostid'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['username'] = array('type' => PARAM_USERNAME, 'null' => NULL_NOT_ALLOWED);
        $fields['password'] = array('type' => PARAM_RAW, 'null' => NULL_NOT_ALLOWED);
        $fields['idnumber'] = array('type' => PARAM_RAW, 'null' => NULL_NOT_ALLOWED);
        $fields['firstname'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['lastname'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['surname'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['email'] = array('type' => PARAM_RAW_TRIMMED, 'null' => NULL_NOT_ALLOWED);
        $fields['emailstop'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['icq'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['skype'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED);
        $fields['aim'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['yahoo'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['msn'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['phone1'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['phone2'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['institution'] = array('type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED);
        $fields['department'] = array('type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED);
        $fields['address'] = array('type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED);
        $fields['city'] = array('type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->defaultcity);
        $fields['country'] = array('type' => PARAM_ALPHA, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->country,
                'choices' => array_merge(array('' => ''), get_string_manager()->get_list_of_countries(true, true)));
        $fields['lang'] = array('type' => PARAM_LANG, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->lang,
                'choices' => array_merge(array('' => ''), get_string_manager()->get_list_of_translations(false)));
        $fields['calendartype'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->calendartype,
                'choices' => array_merge(array('' => ''), \core_calendar\type_factory::get_list_of_calendar_types()));
        $fields['theme'] = array('type' => PARAM_THEME, 'null' => NULL_NOT_ALLOWED,
                'default' => theme_config::DEFAULT_THEME, 'choices' => array_merge(array('' => ''), get_list_of_themes()));
        $fields['timezone'] = array('type' => PARAM_TIMEZONE, 'null' => NULL_NOT_ALLOWED,
                'default' => core_date::get_server_timezone()); // Must not use choices here: timezones can come and go.
        $fields['firstaccess'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['lastaccess'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['lastlogin'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['currentlogin'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['lastip'] = array('type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED);
        $fields['secret'] = array('type' => PARAM_RAW, 'null' => NULL_NOT_ALLOWED);
        $fields['picture'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['url'] = array('type' => PARAM_URL, 'null' => NULL_NOT_ALLOWED);
        $fields['description'] = array('type' => PARAM_RAW, 'null' => NULL_ALLOWED);
        $fields['descriptionformat'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['mailformat'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED,
                'default' => $CFG->defaultpreference_mailformat);
        $fields['maildigest'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED,
                'default' => $CFG->defaultpreference_maildigest);
        $fields['maildisplay'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED,
                'default' => $CFG->defaultpreference_maildisplay);
        $fields['autosubscribe'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED,
                'default' => $CFG->defaultpreference_autosubscribe);
        $fields['trackforums'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED,
                'default' => $CFG->defaultpreference_trackforums);
        $fields['timecreated'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['timemodified'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['trustbitmask'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED);
        $fields['imagealt'] = array('type' => PARAM_TEXT, 'null' => NULL_ALLOWED);
        $fields['lastnamephonetic'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED);
        $fields['firstnamephonetic'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED);
        $fields['middlename'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED);
        $fields['alternatename'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED);

        self::$propertiescache = $fields;
    }

    /**
     * Get properties of a user field.
     *
     * @param string $property property name to be retrieved.
     * @throws coding_exception if the requested property name is invalid.
     * @return array the property definition.
     */
    public static function get_property_definition($property) {

        self::fill_properties_cache();

        if (!array_key_exists($property, self::$propertiescache)) {
            throw new coding_exception('Invalid property requested.');
        }

        return self::$propertiescache[$property];
    }

    /**
     * Validate user data.
     *
     * This method just validates each user field and return an array of errors. It doesn't clean the data,
     * the methods clean() and clean_field() should be used for this purpose.
     *
     * @param stdClass|array $data user data object or array to be validated.
     * @return array|true $errors array of errors found on the user object, true if the validation passed.
     */
    public static function validate($data) {
        // Get all user profile fields definition.
        self::fill_properties_cache();

        foreach ($data as $property => $value) {
            try {
                if (isset(self::$propertiescache[$property])) {
                    validate_param($value, self::$propertiescache[$property]['type'], self::$propertiescache[$property]['null']);
                }
                // Check that the value is part of a list of allowed values.
                if (!empty(self::$propertiescache[$property]['choices']) &&
                        !isset(self::$propertiescache[$property]['choices'][$value])) {
                    throw new invalid_parameter_exception($value);
                }
            } catch (invalid_parameter_exception $e) {
                $errors[$property] = $e->getMessage();
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Clean the properties cache.
     *
     * During unit tests we need to be able to reset all caches so that each new test starts in a known state.
     * Intended for use only for testing, phpunit calls this before every test.
     */
    public static function reset_caches() {
        self::$propertiescache = null;
    }

    /**
     * Clean the user data.
     *
     * @param stdClass|array $user the user data to be validated against properties definition.
     * @return stdClass $user the cleaned user data.
     */
    public static function clean_data($user) {
        if (empty($user)) {
            return $user;
        }

        foreach ($user as $field => $value) {
            // Get the property parameter type and do the cleaning.
            try {
                $user->$field = core_user::clean_field($value, $field);
            } catch (coding_exception $e) {
                debugging("The property '$field' could not be cleaned.", DEBUG_DEVELOPER);
            }
        }

        return $user;
    }

    /**
     * Clean a specific user field.
     *
     * @param string $data the user field data to be cleaned.
     * @param string $field the user field name on the property definition cache.
     * @return string the cleaned user data.
     */
    public static function clean_field($data, $field) {
        if (empty($data) || empty($field)) {
            return $data;
        }

        try {
            $type = core_user::get_property_type($field);

            if (isset(self::$propertiescache[$field]['choices'])) {
                if (!array_key_exists($data, self::$propertiescache[$field]['choices'])) {
                    if (isset(self::$propertiescache[$field]['default'])) {
                        $data = self::$propertiescache[$field]['default'];
                    } else {
                        $data = '';
                    }
                } else {
                    return $data;
                }
            } else {
                $data = clean_param($data, $type);
            }
        } catch (coding_exception $e) {
            debugging("The property '$field' could not be cleaned.", DEBUG_DEVELOPER);
        }

        return $data;
    }

    /**
     * Get the parameter type of the property.
     *
     * @param string $property property name to be retrieved.
     * @throws coding_exception if the requested property name is invalid.
     * @return int the property parameter type.
     */
    public static function get_property_type($property) {

        self::fill_properties_cache();

        if (!array_key_exists($property, self::$propertiescache)) {
            throw new coding_exception('Invalid property requested: ' . $property);
        }

        return self::$propertiescache[$property]['type'];
    }

    /**
     * Discover if the property is NULL_ALLOWED or NULL_NOT_ALLOWED.
     *
     * @param string $property property name to be retrieved.
     * @throws coding_exception if the requested property name is invalid.
     * @return bool true if the property is NULL_ALLOWED, false otherwise.
     */
    public static function get_property_null($property) {

        self::fill_properties_cache();

        if (!array_key_exists($property, self::$propertiescache)) {
            throw new coding_exception('Invalid property requested: ' . $property);
        }

        return self::$propertiescache[$property]['null'];
    }

    /**
     * Get the choices of the property.
     *
     * This is a helper method to validate a value against a list of acceptable choices.
     * For instance: country, language, themes and etc.
     *
     * @param string $property property name to be retrieved.
     * @throws coding_exception if the requested property name is invalid or if it does not has a list of choices.
     * @return array the property parameter type.
     */
    public static function get_property_choices($property) {

        self::fill_properties_cache();

        if (!array_key_exists($property, self::$propertiescache) && !array_key_exists('choices',
                self::$propertiescache[$property])) {

            throw new coding_exception('Invalid property requested, or the property does not has a list of choices.');
        }

        return self::$propertiescache[$property]['choices'];
    }

    /**
     * Get the property default.
     *
     * This method gets the default value of a field (if exists).
     *
     * @param string $property property name to be retrieved.
     * @throws coding_exception if the requested property name is invalid or if it does not has a default value.
     * @return string the property default value.
     */
    public static function get_property_default($property) {

        self::fill_properties_cache();

        if (!array_key_exists($property, self::$propertiescache) || !isset(self::$propertiescache[$property]['default'])) {
            throw new coding_exception('Invalid property requested, or the property does not has a default value.');
        }

        return self::$propertiescache[$property]['default'];
    }
}

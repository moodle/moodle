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
 * @todo       move api's from user/lib.php and deprecate old ones.
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

    /**
     * Hide email address from everyone.
     */
    const MAILDISPLAY_HIDE = 0;

    /**
     * Display email address to everyone.
     */
    const MAILDISPLAY_EVERYONE = 1;

    /**
     * Display email address to course members only.
     */
    const MAILDISPLAY_COURSE_MEMBERS_ONLY = 2;

    /**
     * List of fields that can be synched/locked during authentication.
     */
    const AUTHSYNCFIELDS = [
        'firstname',
        'lastname',
        'email',
        'city',
        'country',
        'lang',
        'description',
        'url',
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address',
        'firstnamephonetic',
        'lastnamephonetic',
        'middlename',
        'alternatename'
    ];

    /** @var int Indicates that user profile view should be prevented */
    const VIEWPROFILE_PREVENT = -1;
    /** @var int Indicates that user profile view should not be prevented */
    const VIEWPROFILE_DO_NOT_PREVENT = 0;
    /** @var int Indicates that user profile view should be allowed even if Moodle would prevent it */
    const VIEWPROFILE_FORCE_ALLOW = 1;

    /** @var stdClass keep record of noreply user */
    public static $noreplyuser = false;

    /** @var stdClass keep record of support user */
    public static $supportuser = false;

    /** @var array store user fields properties cache. */
    protected static $propertiescache = null;

    /** @var array store user preferences cache. */
    protected static $preferencescache = null;

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
     * Return user object from db based on their email.
     *
     * @param string $email The email of the user searched.
     * @param string $fields A comma separated list of user fields to be returned, support and noreply user.
     * @param int $mnethostid The id of the remote host.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
     *                        MUST_EXIST means throw an exception if no user record or multiple records found.
     * @return stdClass|bool user record if found, else false.
     * @throws dml_exception if user record not found and respective $strictness is set.
     */
    public static function get_user_by_email($email, $fields = '*', $mnethostid = null, $strictness = IGNORE_MISSING) {
        global $DB, $CFG;

        // Because we use the username as the search criteria, we must also restrict our search based on mnet host.
        if (empty($mnethostid)) {
            // If empty, we restrict to local users.
            $mnethostid = $CFG->mnet_localhost_id;
        }

        return $DB->get_record('user', array('email' => $email, 'mnethostid' => $mnethostid), $fields, $strictness);
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
     * Searches for users by name, possibly within a specified context, with current user's access.
     *
     * Deciding which users to search is complicated because it relies on user permissions;
     * ideally, we shouldn't show names if you aren't allowed to see their profile. The permissions
     * for seeing profile are really complicated.
     *
     * Even if search is restricted to a course, it's possible that other people might have
     * been able to contribute within the course (e.g. they were enrolled before and not now;
     * or people with system-level roles) so if the user has permission we do want to include
     * everyone. However, if there are multiple results then we prioritise the ones who are
     * enrolled in the course.
     *
     * If you have moodle/user:viewdetails at system level, you can search everyone.
     * Otherwise we check which courses you *do* have that permission and search everyone who is
     * enrolled on those courses.
     *
     * Normally you can only search the user's name. If you have the moodle/site:viewuseridentity
     * capability then we also let you search the fields which are listed as identity fields in
     * the 'showuseridentity' config option. For example, this might include the user's ID number
     * or email.
     *
     * The $max parameter controls the maximum number of users returned. If users are restricted
     * from view for some reason, multiple runs of the main query might be made; the $querylimit
     * parameter allows this to be restricted. Both parameters can be zero to remove limits.
     *
     * The returned user objects include id, username, all fields required for user pictures, and
     * user identity fields.
     *
     * @param string $query Search query text
     * @param \context_course|null $coursecontext Course context or null if system-wide
     * @param int $max Max number of users to return, default 30 (zero = no limit)
     * @param int $querylimit Max number of database queries, default 5 (zero = no limit)
     * @return array Array of user objects with limited fields
     */
    public static function search($query, \context_course $coursecontext = null,
            $max = 30, $querylimit = 5) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/lib.php');

        // Allow limits to be turned off.
        if (!$max) {
            $max = PHP_INT_MAX;
        }
        if (!$querylimit) {
            $querylimit = PHP_INT_MAX;
        }

        // Check permission to view profiles at each context.
        $systemcontext = \context_system::instance();
        $viewsystem = has_capability('moodle/user:viewdetails', $systemcontext);
        if ($viewsystem) {
            $userquery = 'SELECT id FROM {user}';
            $userparams = [];
        }
        if (!$viewsystem) {
            list($userquery, $userparams) = self::get_enrolled_sql_on_courses_with_capability(
                    'moodle/user:viewdetails');
            if (!$userquery) {
                // No permissions anywhere, return nothing.
                return [];
            }
        }

        // Start building the WHERE clause based on name.
        list ($where, $whereparams) = users_search_sql($query, 'u', false);

        // We allow users to search with extra identity fields (as well as name) but only if they
        // have the permission to display those identity fields.
        $extrasql = '';
        $extraparams = [];

        if (empty($CFG->showuseridentity)) {
            // Explode gives wrong result with empty string.
            $extra = [];
        } else {
            $extra = explode(',', $CFG->showuseridentity);
        }

        // We need the username just to skip guests.
        $extrafieldlist = $extra;
        if (!in_array('username', $extra)) {
            $extrafieldlist[] = 'username';
        }
        // The deleted flag will always be false because users_search_sql excludes deleted users,
        // but it must be present or it causes PHP warnings in some functions below.
        if (!in_array('deleted', $extra)) {
            $extrafieldlist[] = 'deleted';
        }
        $selectfields = \user_picture::fields('u',
                array_merge(get_all_user_name_fields(), $extrafieldlist));

        $index = 1;
        foreach ($extra as $fieldname) {
            if ($extrasql) {
                $extrasql .= ' OR ';
            }
            $extrasql .= $DB->sql_like('u.' . $fieldname, ':extra' . $index, false);
            $extraparams['extra' . $index] = $query . '%';
            $index++;
        }

        $identitysystem = has_capability('moodle/site:viewuseridentity', $systemcontext);
        $usingshowidentity = false;
        if ($identitysystem) {
            // They have permission everywhere so just add the extra query to the normal query.
            $where .= ' OR ' . $extrasql;
            $whereparams = array_merge($whereparams, $extraparams);
        } else {
            // Get all courses where user can view full user identity.
            list($sql, $params) = self::get_enrolled_sql_on_courses_with_capability(
                    'moodle/site:viewuseridentity');
            if ($sql) {
                // Join that with the user query to get an extra field indicating if we can.
                $userquery = "
                        SELECT innerusers.id, COUNT(identityusers.id) AS showidentity
                          FROM ($userquery) innerusers
                     LEFT JOIN ($sql) identityusers ON identityusers.id = innerusers.id
                      GROUP BY innerusers.id";
                $userparams = array_merge($userparams, $params);
                $usingshowidentity = true;

                // Query on the extra fields only in those places.
                $where .= ' OR (users.showidentity > 0 AND (' . $extrasql . '))';
                $whereparams = array_merge($whereparams, $extraparams);
            }
        }

        // Default order is just name order. But if searching within a course then we show users
        // within the course first.
        list ($order, $orderparams) = users_order_by_sql('u', $query, $systemcontext);
        if ($coursecontext) {
            list ($sql, $params) = get_enrolled_sql($coursecontext);
            $mainfield = 'innerusers2.id';
            if ($usingshowidentity) {
                $mainfield .= ', innerusers2.showidentity';
            }
            $userquery = "
                    SELECT $mainfield, COUNT(courseusers.id) AS incourse
                      FROM ($userquery) innerusers2
                 LEFT JOIN ($sql) courseusers ON courseusers.id = innerusers2.id
                  GROUP BY $mainfield";
            $userparams = array_merge($userparams, $params);

            $order = 'incourse DESC, ' . $order;
        }

        // Get result (first 30 rows only) from database. Take a couple spare in case we have to
        // drop some.
        $result = [];
        $got = 0;
        $pos = 0;
        $readcount = $max + 2;
        for ($i = 0; $i < $querylimit; $i++) {
            $rawresult = $DB->get_records_sql("
                    SELECT $selectfields
                      FROM ($userquery) users
                      JOIN {user} u ON u.id = users.id
                     WHERE $where
                  ORDER BY $order", array_merge($userparams, $whereparams, $orderparams),
                    $pos, $readcount);
            foreach ($rawresult as $user) {
                // Skip guest.
                if ($user->username === 'guest') {
                    continue;
                }
                // Check user can really view profile (there are per-user cases where this could
                // be different for some reason, this is the same check used by the profile view pages
                // to double-check that it is OK).
                if (!user_can_view_profile($user)) {
                    continue;
                }
                $result[] = $user;
                $got++;
                if ($got >= $max) {
                    break;
                }
            }

            if ($got >= $max) {
                // All necessary results obtained.
                break;
            }
            if (count($rawresult) < $readcount) {
                // No more results from database.
                break;
            }
            $pos += $readcount;
        }

        return $result;
    }

    /**
     * Gets an SQL query that lists all enrolled user ids on any course where the current
     * user has the specified capability. Helper function used for searching users.
     *
     * @param string $capability Required capability
     * @return array Array containing SQL and params, or two nulls if there are no courses
     */
    protected static function get_enrolled_sql_on_courses_with_capability($capability) {
        // Get all courses where user have the capability.
        $courses = get_user_capability_course($capability, null, true,
                implode(',', array_values(context_helper::get_preload_record_columns('ctx'))));
        if (!$courses) {
            return [null, null];
        }

        // Loop around all courses getting the SQL for enrolled users. Note: This query could
        // probably be more efficient (without the union) if get_enrolled_sql had a way to
        // pass an array of courseids, but it doesn't.
        $unionsql = '';
        $unionparams = [];
        foreach ($courses as $course) {
            // Get SQL to list user ids enrolled in this course.
            \context_helper::preload_from_record($course);
            list ($sql, $params) = get_enrolled_sql(\context_course::instance($course->id));

            // Combine to a big union query.
            if ($unionsql) {
                $unionsql .= ' UNION ';
            }
            $unionsql .= $sql;
            $unionparams = array_merge($unionparams, $params);
        }

        return [$unionsql, $unionparams];
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
     * Return true if user id is greater than 0 and alternatively check db.
     *
     * @param int $userid user id.
     * @param bool $checkdb if true userid will be checked in db. By default it's false, and
     *                      userid is compared with 0 for performance.
     * @return bool true is real user else false.
     */
    public static function is_real_user($userid, $checkdb = false) {
        global $DB;

        if ($userid <= 0) {
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
     * Updates the provided users profile picture based upon the expected fields returned from the edit or edit_advanced forms.
     *
     * @param stdClass $usernew An object that contains some information about the user being updated
     * @param array $filemanageroptions
     * @return bool True if the user was updated, false if it stayed the same.
     */
    public static function update_picture(stdClass $usernew, $filemanageroptions = array()) {
        global $CFG, $DB;
        require_once("$CFG->libdir/gdlib.php");

        $context = context_user::instance($usernew->id, MUST_EXIST);
        $user = core_user::get_user($usernew->id, 'id, picture', MUST_EXIST);

        $newpicture = $user->picture;
        // Get file_storage to process files.
        $fs = get_file_storage();
        if (!empty($usernew->deletepicture)) {
            // The user has chosen to delete the selected users picture.
            $fs->delete_area_files($context->id, 'user', 'icon'); // Drop all images in area.
            $newpicture = 0;

        } else {
            // Save newly uploaded file, this will avoid context mismatch for newly created users.
            file_save_draft_area_files($usernew->imagefile, $context->id, 'user', 'newicon', 0, $filemanageroptions);
            if (($iconfiles = $fs->get_area_files($context->id, 'user', 'newicon')) && count($iconfiles) == 2) {
                // Get file which was uploaded in draft area.
                foreach ($iconfiles as $file) {
                    if (!$file->is_directory()) {
                        break;
                    }
                }
                // Copy file to temporary location and the send it for processing icon.
                if ($iconfile = $file->copy_content_to_temp()) {
                    // There is a new image that has been uploaded.
                    // Process the new image and set the user to make use of it.
                    // NOTE: Uploaded images always take over Gravatar.
                    $newpicture = (int)process_new_icon($context, 'user', 'icon', 0, $iconfile);
                    // Delete temporary file.
                    @unlink($iconfile);
                    // Remove uploaded file.
                    $fs->delete_area_files($context->id, 'user', 'newicon');
                } else {
                    // Something went wrong while creating temp file.
                    // Remove uploaded file.
                    $fs->delete_area_files($context->id, 'user', 'newicon');
                    return false;
                }
            }
        }

        if ($newpicture != $user->picture) {
            $DB->set_field('user', 'picture', $newpicture, array('id' => $user->id));
            return true;
        } else {
            return false;
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
        global $CFG, $SESSION;
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
        $fields['emailstop'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 0);
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
        $fields['lang'] = array('type' => PARAM_LANG, 'null' => NULL_NOT_ALLOWED,
                'default' => (!empty($CFG->autolangusercreation) && !empty($SESSION->lang)) ? $SESSION->lang : $CFG->lang,
                'choices' => array_merge(array('' => ''), get_string_manager()->get_list_of_translations(false)));
        $fields['calendartype'] = array('type' => PARAM_PLUGIN, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->calendartype,
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

    /**
     * Definition of updateable user preferences and rules for data and access validation.
     *
     * array(
     *     'preferencename' => array(      // Either exact preference name or a regular expression.
     *          'null' => NULL_ALLOWED,    // Defaults to NULL_NOT_ALLOWED. Takes NULL_NOT_ALLOWED or NULL_ALLOWED.
     *          'type' => PARAM_TYPE,      // Expected parameter type of the user field - mandatory
     *          'choices' => array(1, 2..) // An array of accepted values of the user field - optional
     *          'default' => $CFG->setting // An default value for the field - optional
     *          'isregex' => false/true    // Whether the name of the preference is a regular expression (default false).
     *          'permissioncallback' => callable // Function accepting arguments ($user, $preferencename) that checks if current user
     *                                     // is allowed to modify this preference for given user.
     *                                     // If not specified core_user::default_preference_permission_check() will be assumed.
     *          'cleancallback' => callable // Custom callback for cleaning value if something more difficult than just type/choices is needed
     *                                     // accepts arguments ($value, $preferencename)
     *     )
     * )
     *
     * @return void
     */
    protected static function fill_preferences_cache() {
        if (self::$preferencescache !== null) {
            return;
        }

        // Array of user preferences and expected types/values.
        // Every preference that can be updated directly by user should be added here.
        $preferences = array();
        $preferences['auth_forcepasswordchange'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'choices' => array(0, 1),
            'permissioncallback' => function($user, $preferencename) {
                global $USER;
                $systemcontext = context_system::instance();
                return ($USER->id != $user->id && (has_capability('moodle/user:update', $systemcontext) ||
                        ($user->timecreated > time() - 10 && has_capability('moodle/user:create', $systemcontext))));
            });
        $preferences['usemodchooser'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 1,
            'choices' => array(0, 1));
        $preferences['forum_markasreadonnotification'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 1,
            'choices' => array(0, 1));
        $preferences['htmleditor'] = array('type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED,
            'cleancallback' => function($value, $preferencename) {
                if (empty($value) || !array_key_exists($value, core_component::get_plugin_list('editor'))) {
                    return null;
                }
                return $value;
            });
        $preferences['badgeprivacysetting'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 1,
            'choices' => array(0, 1), 'permissioncallback' => function($user, $preferencename) {
                global $CFG, $USER;
                return !empty($CFG->enablebadges) && $user->id == $USER->id;
            });
        $preferences['blogpagesize'] = array('type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 10,
            'permissioncallback' => function($user, $preferencename) {
                global $USER;
                return $USER->id == $user->id && has_capability('moodle/blog:view', context_system::instance());
            });
        $preferences['user_home_page_preference'] = array('type' => PARAM_INT, 'null' => NULL_ALLOWED, 'default' => HOMEPAGE_MY,
            'choices' => array(HOMEPAGE_SITE, HOMEPAGE_MY),
            'permissioncallback' => function ($user, $preferencename) {
                global $CFG;
                return (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER));
            }
        );

        // Core components that may want to define their preferences.
        // List of core components implementing callback is hardcoded here for performance reasons.
        // TODO MDL-58184 cache list of core components implementing a function.
        $corecomponents = ['core_message', 'core_calendar', 'core_contentbank'];
        foreach ($corecomponents as $component) {
            if (($pluginpreferences = component_callback($component, 'user_preferences')) && is_array($pluginpreferences)) {
                $preferences += $pluginpreferences;
            }
        }

        // Plugins that may define their preferences.
        if ($pluginsfunction = get_plugins_with_function('user_preferences')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $function) {
                    if (($pluginpreferences = call_user_func($function)) && is_array($pluginpreferences)) {
                        $preferences += $pluginpreferences;
                    }
                }
            }
        }

        self::$preferencescache = $preferences;
    }

    /**
     * Retrieves the preference definition
     *
     * @param string $preferencename
     * @return array
     */
    protected static function get_preference_definition($preferencename) {
        self::fill_preferences_cache();

        foreach (self::$preferencescache as $key => $preference) {
            if (empty($preference['isregex'])) {
                if ($key === $preferencename) {
                    return $preference;
                }
            } else {
                if (preg_match($key, $preferencename)) {
                    return $preference;
                }
            }
        }

        throw new coding_exception('Invalid preference requested.');
    }

    /**
     * Default callback used for checking if current user is allowed to change permission of user $user
     *
     * @param stdClass $user
     * @param string $preferencename
     * @return bool
     */
    protected static function default_preference_permission_check($user, $preferencename) {
        global $USER;
        if (is_mnet_remote_user($user)) {
            // Can't edit MNET user.
            return false;
        }

        if ($user->id == $USER->id) {
            // Editing own profile.
            $systemcontext = context_system::instance();
            return has_capability('moodle/user:editownprofile', $systemcontext);
        } else  {
            // Teachers, parents, etc.
            $personalcontext = context_user::instance($user->id);
            if (!has_capability('moodle/user:editprofile', $personalcontext)) {
                return false;
            }
            if (is_siteadmin($user->id) and !is_siteadmin($USER)) {
                // Only admins may edit other admins.
                return false;
            }
            return true;
        }
    }

    /**
     * Can current user edit preference of this/another user
     *
     * @param string $preferencename
     * @param stdClass $user
     * @return bool
     */
    public static function can_edit_preference($preferencename, $user) {
        if (!isloggedin() || isguestuser()) {
            // Guests can not edit anything.
            return false;
        }

        try {
            $definition = self::get_preference_definition($preferencename);
        } catch (coding_exception $e) {
            return false;
        }

        if ($user->deleted || !context_user::instance($user->id, IGNORE_MISSING)) {
            // User is deleted.
            return false;
        }

        if (isset($definition['permissioncallback'])) {
            $callback = $definition['permissioncallback'];
            if (is_callable($callback)) {
                return call_user_func_array($callback, [$user, $preferencename]);
            } else {
                throw new coding_exception('Permission callback for preference ' . s($preferencename) . ' is not callable');
                return false;
            }
        } else {
            return self::default_preference_permission_check($user, $preferencename);
        }
    }

    /**
     * Clean value of a user preference
     *
     * @param string $value the user preference value to be cleaned.
     * @param string $preferencename the user preference name
     * @return string the cleaned preference value
     */
    public static function clean_preference($value, $preferencename) {

        $definition = self::get_preference_definition($preferencename);

        if (isset($definition['type']) && $value !== null) {
            $value = clean_param($value, $definition['type']);
        }

        if (isset($definition['cleancallback'])) {
            $callback = $definition['cleancallback'];
            if (is_callable($callback)) {
                return $callback($value, $preferencename);
            } else {
                throw new coding_exception('Clean callback for preference ' . s($preferencename) . ' is not callable');
            }
        } else if ($value === null && (!isset($definition['null']) || $definition['null'] == NULL_ALLOWED)) {
            return null;
        } else if (isset($definition['choices'])) {
            if (!in_array($value, $definition['choices'])) {
                if (isset($definition['default'])) {
                    return $definition['default'];
                } else {
                    $first = reset($definition['choices']);
                    return $first;
                }
            } else {
                return $value;
            }
        } else {
            if ($value === null) {
                return isset($definition['default']) ? $definition['default'] : '';
            }
            return $value;
        }
    }

}

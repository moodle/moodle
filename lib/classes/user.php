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

namespace core;

use core\context\user as context_user;
use core\context\course as context_course;
use core\context\system as context_system;
use core_user\fields;
use core\exception\invalid_parameter_exception;
use core\exception\moodle_exception;
use core\exception\coding_exception;
use core\output\theme_config;
use core\output\user_picture;
use core_date;
use dml_exception;
use stdClass;

/**
 * User class to access user details.
 *
 * @todo       MDL-82650 Move api's from user/lib.php and deprecate old ones.
 * @package    core
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user {
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
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address',
        'firstnamephonetic',
        'lastnamephonetic',
        'middlename',
        'alternatename',
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
     * if userid matches \core\user::NOREPLY_USER or \core\user::SUPPORT_USER
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
                return $DB->get_record('user', ['id' => $userid], $fields, $strictness);
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

        return $DB->get_record('user', ['email' => $email, 'mnethostid' => $mnethostid], $fields, $strictness);
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

        return $DB->get_record('user', ['username' => $username, 'mnethostid' => $mnethostid], $fields, $strictness);
    }

    /**
     * Return User object based on their idnumber.
     *
     * @param string $idnumber The idnumber of the user searched.
     * @param string $fields A comma separated list of user fields to be returned, support and noreply user.
     * @param null|int $mnethostid The id of the remote host.
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
     *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
     *                        MUST_EXIST means throw an exception if no user record or multiple records found.
     * @return stdClass|bool user record if found, else false.
     */
    public static function get_user_by_idnumber(
        string $idnumber,
        string $fields = '*',
        ?int $mnethostid = null,
        int $strictness = IGNORE_MISSING,
    ): stdClass|bool {
        global $DB, $CFG;

        // Because we use the username as the search criteria, we must also restrict our search based on mnet host.
        if (empty($mnethostid)) {
            // If empty, we restrict to local users.
            $mnethostid = $CFG->mnet_localhost_id;
        }

        return $DB->get_record('user', [
            'idnumber' => $idnumber,
            'mnethostid' => $mnethostid,
        ], $fields, $strictness);
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
     * @param context_course|null $coursecontext Course context or null if system-wide
     * @param int $max Max number of users to return, default 30 (zero = no limit)
     * @param int $querylimit Max number of database queries, default 5 (zero = no limit)
     * @return array Array of user objects with limited fields
     */
    public static function search(
        $query,
        ?context_course $coursecontext = null,
        $max = 30,
        $querylimit = 5
    ) {
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
        $systemcontext = context_system::instance();
        $viewsystem = has_capability('moodle/user:viewdetails', $systemcontext);
        if ($viewsystem) {
            $userquery = 'SELECT id FROM {user}';
            $userparams = [];
        }
        if (!$viewsystem) {
            [$userquery, $userparams] = self::get_enrolled_sql_on_courses_with_capability(
                'moodle/user:viewdetails'
            );
            if (!$userquery) {
                // No permissions anywhere, return nothing.
                return [];
            }
        }

        // Start building the WHERE clause based on name.
         [$where, $whereparams] = users_search_sql($query, 'u');

        // We allow users to search with extra identity fields (as well as name) but only if they
        // have the permission to display those identity fields.
        $extrasql = '';
        $extraparams = [];

        // TODO Does not support custom user profile fields (MDL-70456).
        $userfieldsapi = \core_user\fields::for_identity(null, false)->with_userpic()->with_name()
            ->including('username', 'deleted');
        $selectfields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
        $extra = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        $index = 1;
        foreach ($extra as $fieldname) {
            if ($extrasql) {
                $extrasql .= ' OR ';
            }
            $extrasql .= $DB->sql_like('u.' . $fieldname, ':extra' . $index, false);
            $extraparams['extra' . $index] = $query . '%';
            $index++;
        }

        $usingshowidentity = false;
        // Only do this code if there actually are some identity fields being searched.
        if ($extrasql) {
            $identitysystem = has_capability('moodle/site:viewuseridentity', $systemcontext);
            if ($identitysystem) {
                // They have permission everywhere so just add the extra query to the normal query.
                $where .= ' OR ' . $extrasql;
                $whereparams = array_merge($whereparams, $extraparams);
            } else {
                // Get all courses where user can view full user identity.
                [$sql, $params] = self::get_enrolled_sql_on_courses_with_capability(
                    'moodle/site:viewuseridentity'
                );
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
        }

        // Default order is just name order. But if searching within a course then we show users
        // within the course first.
         [$order, $orderparams] = users_order_by_sql('u', $query, $systemcontext);
        if ($coursecontext) {
             [$sql, $params] = get_enrolled_sql($coursecontext);
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
            $rawresult = $DB->get_records_sql(
                "
                    SELECT $selectfields
                      FROM ($userquery) users
                      JOIN {user} u ON u.id = users.id
                     WHERE $where
                  ORDER BY $order",
                array_merge($userparams, $whereparams, $orderparams),
                $pos,
                $readcount
            );
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
        $courses = get_user_capability_course(
            $capability,
            null,
            true,
            implode(',', array_values(context_helper::get_preload_record_columns('ctx')))
        );
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
            context_helper::preload_from_record($course);
             [$sql, $params] = get_enrolled_sql(context_course::instance($course->id));

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
            return $DB->record_exists('user', ['id' => $userid]);
        } else {
            return true;
        }
    }

    /**
     * Determine whether the given user ID is that of the current user. Useful for components implementing permission callbacks
     * for preferences consumed by {@see fill_preferences_cache}
     *
     * @param stdClass $user
     * @return bool
     */
    public static function is_current_user(stdClass $user): bool {
        global $USER;
        return $user->id == $USER->id;
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

        if ($checksuspended && $user->suspended) {
            throw new moodle_exception('suspended', 'auth');
        }

        if ($checknologin && $user->auth == 'nologin') {
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
    public static function update_picture(stdClass $usernew, $filemanageroptions = []) {
        global $CFG, $DB;
        require_once("$CFG->libdir/gdlib.php");

        $context = context_user::instance($usernew->id, MUST_EXIST);
        $user = self::get_user($usernew->id, 'id, picture', MUST_EXIST);

        $newpicture = $user->picture;
        // Get file_storage to process files.
        $fs = get_file_storage();
        if (!empty($usernew->deletepicture)) {
            // The user has chosen to delete the selected users picture.
            $fs->delete_area_files($context->id, 'user', 'icon'); // Drop all images in area.
            $newpicture = 0;
        }

        // Save newly uploaded file, this will avoid context mismatch for newly created users.
        if (!isset($usernew->imagefile)) {
            $usernew->imagefile = 0;
        }
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

        if ($newpicture != $user->picture) {
            $DB->set_field('user', 'picture', $newpicture, ['id' => $user->id]);
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
        $fields = [];
        $fields['id'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['auth'] = ['type' => PARAM_AUTH, 'null' => NULL_NOT_ALLOWED];
        $fields['confirmed'] = ['type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED];
        $fields['policyagreed'] = ['type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED];
        $fields['deleted'] = ['type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED];
        $fields['suspended'] = ['type' => PARAM_BOOL, 'null' => NULL_NOT_ALLOWED];
        $fields['mnethostid'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['username'] = ['type' => PARAM_USERNAME, 'null' => NULL_NOT_ALLOWED];
        $fields['password'] = ['type' => PARAM_RAW, 'null' => NULL_NOT_ALLOWED];
        $fields['idnumber'] = ['type' => PARAM_RAW, 'null' => NULL_NOT_ALLOWED];
        $fields['firstname'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['lastname'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['surname'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['email'] = ['type' => PARAM_RAW_TRIMMED, 'null' => NULL_NOT_ALLOWED];
        $fields['emailstop'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 0];
        $fields['phone1'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['phone2'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['institution'] = ['type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED];
        $fields['department'] = ['type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED];
        $fields['address'] = ['type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED];
        $fields['city'] = ['type' => PARAM_TEXT, 'null' => NULL_NOT_ALLOWED, 'default' => $CFG->defaultcity];
        $fields['country'] = [
            'type' => PARAM_ALPHA,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->country,
            'choices' => array_merge(
                ['' => ''],
                get_string_manager()->get_list_of_countries(true, true)
            ),
        ];
        $fields['lang'] = [
            'type' => PARAM_LANG,
            'null' => NULL_NOT_ALLOWED,
            'default' => (!empty($CFG->autolangusercreation) && !empty($SESSION->lang)) ? $SESSION->lang : $CFG->lang,
            'choices' => array_merge(
                ['' => ''],
                get_string_manager()->get_list_of_translations(false)
            ),
        ];
        $fields['calendartype'] = [
            'type' => PARAM_PLUGIN,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->calendartype,
            'choices' => array_merge(
                ['' => ''],
                \core_calendar\type_factory::get_list_of_calendar_types()
            ),
        ];
        $fields['theme'] = [
            'type' => PARAM_THEME,
            'null' => NULL_NOT_ALLOWED,
            'default' => theme_config::DEFAULT_THEME,
            'choices' => array_merge(
                ['' => ''],
                get_list_of_themes()
            ),
        ];
        $fields['timezone'] = [
            // Must not use choices here: timezones can come and go.
            'type' => PARAM_TIMEZONE,
            'null' => NULL_NOT_ALLOWED,
            'default' => core_date::get_server_timezone(),
        ];
        $fields['firstaccess'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['lastaccess'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['lastlogin'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['currentlogin'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['lastip'] = ['type' => PARAM_NOTAGS, 'null' => NULL_NOT_ALLOWED];
        $fields['secret'] = ['type' => PARAM_ALPHANUM, 'null' => NULL_NOT_ALLOWED];
        $fields['picture'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['description'] = ['type' => PARAM_RAW, 'null' => NULL_ALLOWED];
        $fields['descriptionformat'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['mailformat'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->defaultpreference_mailformat,
        ];
        $fields['maildigest'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->defaultpreference_maildigest,
        ];
        $fields['maildisplay'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->defaultpreference_maildisplay,
        ];
        $fields['autosubscribe'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->defaultpreference_autosubscribe,
        ];
        $fields['trackforums'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => $CFG->defaultpreference_trackforums,
        ];
        $fields['timecreated'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['timemodified'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['trustbitmask'] = ['type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED];
        $fields['imagealt'] = ['type' => PARAM_TEXT, 'null' => NULL_ALLOWED];
        $fields['lastnamephonetic'] = ['type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED];
        $fields['firstnamephonetic'] = ['type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED];
        $fields['middlename'] = ['type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED];
        $fields['alternatename'] = ['type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED];

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
                if (
                    !empty(self::$propertiescache[$property]['choices']) &&
                    !isset(self::$propertiescache[$property]['choices'][$value])
                ) {
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
        self::$preferencescache = null;
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
                $user->$field = self::clean_field($value, $field);
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
            $type = self::get_property_type($field);

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

        if (
            !array_key_exists($property, self::$propertiescache) &&
            !array_key_exists('choices', self::$propertiescache[$property])
        ) {
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
     *          'permissioncallback' => callable // Function accepting arguments ($user, $preferencename) that checks if current
     *                                     // user
     *                                     // is allowed to modify this preference for given user.
     *                                     // If not specified \core\user::default_preference_permission_check() will be assumed.
     *          'cleancallback' => callable // Custom callback for cleaning value if something more difficult than just type/choices
     *                                      // is needed accepts arguments ($value, $preferencename)
     *     )
     * )
     *
     * @return void
     */
    protected static function fill_preferences_cache() {
        global $CFG;

        if (self::$preferencescache !== null) {
            return;
        }

        // Array of user preferences and expected types/values.
        // Every preference that can be updated directly by user should be added here.
        $preferences = [];
        $preferences['auth_forcepasswordchange'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'choices' => [0, 1],
            'permissioncallback' => function ($user, $preferencename) {
                global $USER;
                $systemcontext = context_system::instance();
                return ($USER->id != $user->id && (has_capability('moodle/user:update', $systemcontext) ||
                        ($user->timecreated > time() - 10 && has_capability('moodle/user:create', $systemcontext))));
            },
        ];
        $preferences['forum_markasreadonnotification'] = [
            'type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 1,
            'choices' => [0, 1],
        ];
        $preferences['htmleditor'] = [
            'type' => PARAM_NOTAGS, 'null' => NULL_ALLOWED,
            'cleancallback' => function ($value, $preferencename) {
                if (empty($value) || !array_key_exists($value, component::get_plugin_list('editor'))) {
                    return null;
                }
                return $value;
            },
        ];
        $preferences['badgeprivacysetting'] = [
            'type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 1,
            'choices' => [0, 1], 'permissioncallback' => function ($user, $preferencename) {
                global $CFG;
                return !empty($CFG->enablebadges) && self::is_current_user($user);
            },
        ];
        $preferences['blogpagesize'] = [
            'type' => PARAM_INT, 'null' => NULL_NOT_ALLOWED, 'default' => 10,
            'permissioncallback' => function ($user, $preferencename) {
                return self::is_current_user($user) && has_capability('moodle/blog:view', context_system::instance());
            },
        ];
        $preferences['filemanager_recentviewmode'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 1,
            'choices' => [1, 2, 3],
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['filepicker_recentrepository'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['filepicker_recentlicense'] = [
            'type' => PARAM_SAFEDIR,
            'null' => NULL_NOT_ALLOWED,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['filepicker_recentviewmode'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => 1,
            'choices' => [1, 2, 3],
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['userselector_optionscollapsed'] = [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['userselector_autoselectunique'] = [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['userselector_preserveselected'] = [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['userselector_searchtype'] = [
            'type' => PARAM_INT,
            'null' => NULL_NOT_ALLOWED,
            'default' => USER_SEARCH_STARTS_WITH,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];
        $preferences['question_bank_advanced_search'] = [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [static::class, 'is_current_user'],
        ];

        $choices = [HOMEPAGE_SITE];
        if (!empty($CFG->enabledashboard)) {
            $choices[] = HOMEPAGE_MY;
        }
        $choices[] = HOMEPAGE_MYCOURSES;

        // Allow hook callbacks to extend options.
        $hook = new \core_user\hook\extend_default_homepage(true);
        \core\di::get(\core\hook\manager::class)->dispatch($hook);
        $choices = array_merge($choices, array_keys($hook->get_options()));

        $preferences['user_home_page_preference'] = [
            'null' => NULL_ALLOWED,
            'default' => get_default_home_page(),
            'choices' => $choices,
            'permissioncallback' => function ($user, $preferencename) {
                global $CFG;
                return self::is_current_user($user) &&
                    (!empty($CFG->defaulthomepage) && ($CFG->defaulthomepage == HOMEPAGE_USER));
            },
        ];

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
    public static function get_preference_definition($preferencename) {
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

        if (self::is_current_user($user)) {
            // Editing own profile.
            $systemcontext = context_system::instance();
            return has_capability('moodle/user:editownprofile', $systemcontext);
        } else {
            // Teachers, parents, etc.
            $personalcontext = context_user::instance($user->id);
            if (!has_capability('moodle/user:editprofile', $personalcontext)) {
                return false;
            }
            if (is_siteadmin($user->id) && !is_siteadmin($USER)) {
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

    /**
     * Is the user expected to perform an action to start using Moodle properly?
     *
     * This covers cases such as filling the profile, changing password or agreeing to the site policy.
     *
     * @param stdClass $user User object, defaults to the current user.
     * @return bool
     */
    public static function awaiting_action(?stdClass $user = null): bool {
        global $USER;

        if ($user === null) {
            $user = $USER;
        }

        if (user_not_fully_set_up($user)) {
            // Awaiting the user to fill all fields in the profile.
            return true;
        }

        if (get_user_preferences('auth_forcepasswordchange', false, $user)) {
            // Awaiting the user to change their password.
            return true;
        }

        if (empty($user->policyagreed) && !is_siteadmin($user)) {
            $manager = new \core_privacy\local\sitepolicy\manager();

            if ($manager->is_defined(isguestuser($user))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get welcome message.
     *
     * @return lang_string welcome message
     */
    public static function welcome_message(): ?lang_string {
        global $USER;

        $isloggedinas = \core\session\manager::is_loggedinas();
        if (!isloggedin() || isguestuser() || $isloggedinas) {
            return null;
        }
        if (empty($USER->core_welcome_message)) {
            $USER->core_welcome_message = true;
            $messagekey = 'welcomeback';
            if (empty(get_user_preferences('core_user_welcome', null))) {
                $messagekey = 'welcometosite';
                set_user_preference('core_user_welcome', time());
            }

            $namefields = [
                'fullname' => fullname($USER),
                'alternativefullname' => fullname($USER, true),
            ];

            foreach (\core_user\fields::get_name_fields() as $namefield) {
                $namefields[$namefield] = $USER->{$namefield};
            }

            return new lang_string($messagekey, 'core', $namefields);
        };
        return null;
    }

    /**
     * Return full name depending on context.
     * This function should be used for displaying purposes only as the details may not be the same as it is on database.
     *
     * @param stdClass $user the person to get details of.
     * @param context|null $context The context will be used to determine the visibility of the user's full name.
     * @param array $options can include: override - if true, will not use forced firstname/lastname settings
     * @return string Full name of the user
     */
    public static function get_fullname(stdClass $user, ?context $context = null, array $options = []): string {
        global $CFG, $SESSION;

        // Clone the user so that it does not mess up the original object.
        $user = clone($user);

        // Override options.
        $override = $options["override"] ?? false;

        if (!isset($user->firstname) && !isset($user->lastname)) {
            return '';
        }

        // Get all of the name fields.
        $allnames = \core_user\fields::get_name_fields();
        if ($CFG->debugdeveloper) {
            $missingfields = [];
            foreach ($allnames as $allname) {
                if (!property_exists($user, $allname)) {
                    $missingfields[] = $allname;
                }
            }
            if (!empty($missingfields)) {
                debugging('The following name fields are missing from the user object: ' . implode(', ', $missingfields));
            }
        }

        if (!$override) {
            if (!empty($CFG->forcefirstname)) {
                $user->firstname = $CFG->forcefirstname;
            }
            if (!empty($CFG->forcelastname)) {
                $user->lastname = $CFG->forcelastname;
            }
        }

        if (!empty($SESSION->fullnamedisplay)) {
            $CFG->fullnamedisplay = $SESSION->fullnamedisplay;
        }

        $template = null;
        // If the fullnamedisplay setting is available, set the template to that.
        if (isset($CFG->fullnamedisplay)) {
            $template = $CFG->fullnamedisplay;
        }
        // If the template is empty, or set to language, return the language string.
        if ((empty($template) || $template == 'language') && !$override) {
            return get_string('fullnamedisplay', null, $user);
        }

        // Check to see if we are displaying according to the alternative full name format.
        if ($override) {
            if (empty($CFG->alternativefullnameformat) || $CFG->alternativefullnameformat == 'language') {
                // Default to show just the user names according to the fullnamedisplay string.
                return get_string('fullnamedisplay', null, $user);
            } else {
                // If the override is true, then change the template to use the complete name.
                $template = $CFG->alternativefullnameformat;
            }
        }

        $requirednames = [];
        // With each name, see if it is in the display name template, and add it to the required names array if it is.
        foreach ($allnames as $allname) {
            if (strpos($template, $allname) !== false) {
                $requirednames[] = $allname;
            }
        }

        $displayname = $template;
        // Switch in the actual data into the template.
        foreach ($requirednames as $altname) {
            if (isset($user->$altname)) {
                // Using empty() on the below if statement causes breakages.
                if ((string)$user->$altname == '') {
                    $displayname = str_replace($altname, 'EMPTY', $displayname);
                } else {
                    $displayname = str_replace($altname, $user->$altname, $displayname);
                }
            } else {
                $displayname = str_replace($altname, 'EMPTY', $displayname);
            }
        }
        // Tidy up any misc. characters (Not perfect, but gets most characters).
        // Don't remove the "u" at the end of the first expression unless you want garbled characters when combining hiragana or
        // katakana and parenthesis.
        $patterns = [];
        // This regular expression replacement is to fix problems such as 'James () Kirk' Where 'Tiberius' (middlename) has not been
        // filled in by a user.
        // The special characters are Japanese brackets that are common enough to make allowances for them (not covered by :punct:).
        $patterns[] = '/[[:punct:]「」]*EMPTY[[:punct:]「」]*/u';
        // This regular expression is to remove any double spaces in the display name.
        $patterns[] = '/\s{2,}/u';
        foreach ($patterns as $pattern) {
            $displayname = preg_replace($pattern, ' ', $displayname);
        }

        // Trimming $displayname will help the next check to ensure that we don't have a display name with spaces.
        $displayname = trim($displayname);
        if (empty($displayname)) {
            // Going with just the first name if no alternate fields are filled out. May be changed later depending on what
            // people in general feel is a good setting to fall back on.
            $displayname = $user->firstname;
        }
        return $displayname;
    }

    /**
     * Return fullname of a dummy user comprised of configured name fields only
     *
     * @param context|null $context
     * @param array $options
     * @return string
     */
    public static function get_dummy_fullname(?context $context = null, array $options = []): string {

        // Create a dummy user object containing all name fields.
        $namefields = \core_user\fields::get_name_fields();
        $user = (object) array_combine($namefields, $namefields);

        return static::get_fullname($user, $context, $options);
    }

    /**
     * Return profile url depending on context.
     *
     * @param stdClass $user the person to get details of.
     * @param context|null $context The context will be used to determine the visibility of the user's profile url.
     * @return url Profile url of the user
     */
    public static function get_profile_url(stdClass $user, ?context $context = null): url {
        if (empty($user->id)) {
            throw new coding_exception('User id is required when displaying profile url.');
        }

        // Params to be passed to the user view page.
        $params = ['id' => $user->id];

        // Get courseid from context if provided.
        if ($context && $coursecontext = $context->get_course_context(false)) {
            $params['course'] = $coursecontext->instanceid;
        }

        // If courseid is not set or is set to site id, then return profile page, otherwise return view page.
        if (!isset($params['course']) || $params['course'] == SITEID) {
            return new url('/user/profile.php', $params);
        } else {
            return new url('/user/view.php', $params);
        }
    }

    /**
     * Return user picture depending on context.
     * This function should be used for displaying purposes only as the details may not be the same as it is on database.
     *
     * @param stdClass $user the person to get details of.
     * @param context|null $context The context will be used to determine the visibility of the user's picture.
     * @param array $options public properties of {@see user_picture} to be overridden
     *     - courseid = $this->page->course->id (course id of user profile in link)
     *     - size = 35 (size of image)
     *     - link = true (make image clickable - the link leads to user profile)
     *     - popup = false (open in popup)
     *     - alttext = true (add image alt attribute)
     *     - class = image class attribute (default 'userpicture')
     *     - visibletoscreenreaders = true (whether to be visible to screen readers)
     *     - includefullname = false (whether to include the user's full name together with the user picture)
     *     - includetoken = false (whether to use a token for authentication. True for current user, int value for other user id)
     * @return user_picture User picture object
     */
    public static function get_profile_picture(stdClass $user, ?context $context = null, array $options = []): user_picture {
        // Create a new user picture object.
        $userpicture = new user_picture($user);

        // Override the user picture object with the options provided.
        foreach ($options as $key => $value) {
            if (property_exists($userpicture, $key)) {
                $userpicture->$key = $value;
            }
        }

        // Return the user picture.
        return $userpicture;
    }

    /**
     * Get initials for users
     *
     * @param stdClass $user
     * @return string
     */
    public static function get_initials(stdClass $user): string {
        // Get the available name fields.
        $namefields = \core_user\fields::get_name_fields();

        // Determine the name format by using fullname() and passing the dummy user.
        $nameformat = static::get_dummy_fullname();

        // Fetch all the available username fields.
        $availablefields = order_in_string($namefields, $nameformat);
        // We only want the first and last name fields.
        if (!empty($availablefields) && count($availablefields) >= 2) {
            $availablefields = [reset($availablefields), end($availablefields)];
        }
        $initials = '';
        foreach ($availablefields as $userfieldname) {
            if (!empty($user->$userfieldname)) {
                $initials .= mb_substr($user->$userfieldname, 0, 1);
            }
        }
        return $initials;
    }

    /**
     * Prepare SQL where clause and associated parameters for any user searching being performed.
     * This mostly came from core_user\table\participants_search with some slight modifications four our use case.
     *
     * @param context $context Context we are in.
     * @param string $usersearch Array of field mappings (fieldname => SQL code for the value)
     * @return array SQL query data in the format ['where' => '', 'params' => []].
     */
    public static function get_users_search_sql(context $context, string $usersearch = ''): array {
        global $DB, $USER;

        $userfields = fields::for_identity($context, false)->with_userpic();
        ['mappings' => $mappings]  = (array)$userfields->get_sql('u', true);
        $userfields = $userfields->get_required_fields();

        $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);

        $params = [];
        $searchkey1 = 'search01';
        $searchkey2 = 'search02';
        $searchkey3 = 'search03';

        $conditions = [];

        // Search by fullname.
        [$fullname, $fullnameparams] = fields::get_sql_fullname('u', $canviewfullnames);
        $conditions[] = $DB->sql_like($fullname, ':' . $searchkey1, false, false);
        $params = array_merge($params, $fullnameparams);

        // Search by email.
        $email = $DB->sql_like('email', ':' . $searchkey2, false, false);

        if (!in_array('email', $userfields)) {
            $maildisplay = 'maildisplay0';
            $userid1 = 'userid01';
            // Prevent users who hide their email address from being found by others
            // who aren't allowed to see hidden email addresses.
            $email = "(". $email ." AND (" .
                "u.maildisplay <> :$maildisplay " .
                "OR u.id = :$userid1". // Users can always find themselves.
                "))";
            $params[$maildisplay] = self::MAILDISPLAY_HIDE;
            $params[$userid1] = $USER->id;
        }

        $conditions[] = $email;

        // Search by idnumber.
        $idnumber = $DB->sql_like('idnumber', ':' . $searchkey3, false, false);

        if (!in_array('idnumber', $userfields)) {
            $userid2 = 'userid02';
            // Users who aren't allowed to see idnumbers should at most find themselves
            // when searching for an idnumber.
            $idnumber = "(". $idnumber . " AND u.id = :$userid2)";
            $params[$userid2] = $USER->id;
        }

        $conditions[] = $idnumber;

        // Search all user identify fields.
        $extrasearchfields = fields::get_identity_fields(null, false);
        foreach ($extrasearchfields as $fieldindex => $extrasearchfield) {
            if (in_array($extrasearchfield, ['email', 'idnumber', 'country'])) {
                // Already covered above.
                continue;
            }
            // The param must be short (max 32 characters) so don't include field name.
            $param = $searchkey3 . '_ident' . $fieldindex;
            $fieldsql = $mappings[$extrasearchfield];
            $condition = $DB->sql_like($fieldsql, ':' . $param, false, false);
            $params[$param] = "%$usersearch%";

            if (!in_array($extrasearchfield, $userfields)) {
                // User cannot see this field, but allow match if their own account.
                $userid3 = 'userid03_ident' . $fieldindex;
                $condition = "(". $condition . " AND u.id = :$userid3)";
                $params[$userid3] = $USER->id;
            }
            $conditions[] = $condition;
        }

        $where = "(". implode(" OR ", $conditions) .") ";
        $params[$searchkey1] = "%$usersearch%";
        $params[$searchkey2] = "%$usersearch%";
        $params[$searchkey3] = "%$usersearch%";

        return [
            'where' => $where,
            'params' => $params,
        ];
    }

    /**
     * Generates an array of name placeholders for a given user.
     *
     * @param stdClass $user The user object containing name fields.
     * @return array An associative array of name placeholders.
     */
    public static function get_name_placeholders(stdClass $user): array {
        $namefields = [
            'fullname' => fullname($user),
            'alternativefullname' => fullname($user, true),
        ];
        foreach (fields::get_name_fields() as $namefield) {
            $namefields[$namefield] = $user->{$namefield};
        }
        return $namefields;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(user::class, \core_user::class);

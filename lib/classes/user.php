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
                return self::get_noreply_user($strictness);
                break;
            case self::SUPPORT_USER:
                return self::get_support_user($strictness);
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
        }

        if (empty(self::$noreplyuser)) {
            self::$noreplyuser = self::get_dummy_user_record();
            self::$noreplyuser->maildisplay = '1'; // Show to all.
        }
        self::$noreplyuser->emailstop = 1; // Force msg stop for this user.
        return self::$noreplyuser;
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
        }

        // Try sending it to support email if support user is not set.
        if (empty(self::$supportuser) && !empty($CFG->supportemail)) {
            self::$supportuser = self::get_dummy_user_record();
            self::$supportuser->id = self::SUPPORT_USER;
            self::$supportuser->email = $CFG->supportemail;
            if ($CFG->supportname) {
                self::$supportuser->firstname = $CFG->supportname;
            }
            self::$supportuser->username = 'support';
            self::$supportuser->maildisplay = '1'; // Show to all.
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
}

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
 * Class for loading/storing oauth2 linked logins from the DB.
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_oauth2;

use context_user;
use stdClass;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Static list of api methods for auth oauth2 configuration.
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Remove all linked logins that are using issuers that have been deleted.
     *
     * @param int $issuerid The issuer id of the issuer to check, or false to check all (defaults to all)
     * @return boolean
     */
    public static function clean_orphaned_linked_logins($issuerid = false) {
        return linked_login::delete_orphaned($issuerid);
    }

    /**
     * List linked logins
     *
     * Requires auth/oauth2:managelinkedlogins capability at the user context.
     *
     * @param int $userid (defaults to $USER->id)
     * @return boolean
     */
    public static function get_linked_logins($userid = false) {
        global $USER;

        if ($userid === false) {
            $userid = $USER->id;
        }

        if (\core\session\manager::is_loggedinas()) {
            throw new moodle_exception('notwhileloggedinas', 'auth_oauth2');
        }

        $context = context_user::instance($userid);
        require_capability('auth/oauth2:managelinkedlogins', $context);

        return linked_login::get_records(['userid' => $userid, 'confirmtoken' => '']);
    }

    /**
     * See if there is a match for this username and issuer in the linked_login table.
     *
     * @param string $username as returned from an oauth client.
     * @param \core\oauth2\issuer $issuer
     * @return stdClass User record if found.
     */
    public static function match_username_to_user($username, $issuer) {
        $params = [
            'issuerid' => $issuer->get('id'),
            'username' => $username
        ];
        $result = linked_login::get_record($params);

        if ($result) {
            $user = \core_user::get_user($result->get('userid'));
            if (!empty($user) && !$user->deleted) {
                return $result;
            }
        }
        return false;
    }

    /**
     * Link a login to this account.
     *
     * Requires auth/oauth2:managelinkedlogins capability at the user context.
     *
     * @param array $userinfo as returned from an oauth client.
     * @param \core\oauth2\issuer $issuer
     * @param int $userid (defaults to $USER->id)
     * @param bool $skippermissions During signup we need to set this before the user is setup for capability checks.
     * @return bool
     */
    public static function link_login($userinfo, $issuer, $userid = false, $skippermissions = false) {
        global $USER;

        if ($userid === false) {
            $userid = $USER->id;
        }

        if (linked_login::has_existing_issuer_match($issuer, $userinfo['username'])) {
            throw new moodle_exception('alreadylinked', 'auth_oauth2');
        }

        if (\core\session\manager::is_loggedinas()) {
            throw new moodle_exception('notwhileloggedinas', 'auth_oauth2');
        }

        $context = context_user::instance($userid);
        if (!$skippermissions) {
            require_capability('auth/oauth2:managelinkedlogins', $context);
        }

        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->username = $userinfo['username'];
        $record->userid = $userid;
        $existing = linked_login::get_record((array)$record);
        if ($existing) {
            $existing->set('confirmtoken', '');
            $existing->update();
            return $existing;
        }
        $record->email = $userinfo['email'];
        $record->confirmtoken = '';
        $record->confirmtokenexpires = 0;
        $linkedlogin = new linked_login(0, $record);
        return $linkedlogin->create();
    }

    /**
     * Send an email with a link to confirm linking this account.
     *
     * @param array $userinfo as returned from an oauth client.
     * @param \core\oauth2\issuer $issuer
     * @param int $userid (defaults to $USER->id)
     * @return bool
     */
    public static function send_confirm_link_login_email($userinfo, $issuer, $userid) {
        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->username = $userinfo['username'];
        $record->userid = $userid;
        if (linked_login::has_existing_issuer_match($issuer, $userinfo['username'])) {
            throw new moodle_exception('alreadylinked', 'auth_oauth2');
        }
        $record->email = $userinfo['email'];
        $record->confirmtoken = random_string(32);
        $expires = new \DateTime('NOW');
        $expires->add(new \DateInterval('PT30M'));
        $record->confirmtokenexpires = $expires->getTimestamp();

        $linkedlogin = new linked_login(0, $record);
        $linkedlogin->create();

        // Construct the email.
        $site = get_site();
        $supportuser = \core_user::get_support_user();
        $user = get_complete_user_data('id', $userid);

        $data = new stdClass();
        $data->fullname = fullname($user);
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();
        $data->issuername = format_string($issuer->get('name'));
        $data->linkedemail = format_string($linkedlogin->get('email'));

        $subject = get_string('confirmlinkedloginemailsubject', 'auth_oauth2', format_string($site->fullname));

        $params = [
            'token' => $linkedlogin->get('confirmtoken'),
            'userid' => $userid,
            'username' => $userinfo['username'],
            'issuerid' => $issuer->get('id'),
        ];
        $confirmationurl = new moodle_url('/auth/oauth2/confirm-linkedlogin.php', $params);

        $data->link = $confirmationurl->out(false);
        $message = get_string('confirmlinkedloginemail', 'auth_oauth2', $data);

        $data->link = $confirmationurl->out();
        $messagehtml = text_to_html(get_string('confirmlinkedloginemail', 'auth_oauth2', $data), false, false, true);

        $user->mailformat = 1;  // Always send HTML version as well.

        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
    }

    /**
     * Look for a waiting confirmation token, and if we find a match - confirm it.
     *
     * @param int $userid
     * @param string $username
     * @param int $issuerid
     * @param string $token
     * @return boolean True if we linked.
     */
    public static function confirm_link_login($userid, $username, $issuerid, $token) {
        if (empty($token) || empty($userid) || empty($issuerid) || empty($username)) {
            return false;
        }
        $params = [
            'userid' => $userid,
            'username' => $username,
            'issuerid' => $issuerid,
            'confirmtoken' => $token,
        ];

        $login = linked_login::get_record($params);
        if (empty($login)) {
            return false;
        }
        $expires = $login->get('confirmtokenexpires');
        if (time() > $expires) {
            $login->delete();
            return;
        }
        $login->set('confirmtokenexpires', 0);
        $login->set('confirmtoken', '');
        $login->update();
        return true;
    }

    /**
     * Create an account with a linked login that is already confirmed.
     *
     * @param array $userinfo as returned from an oauth client.
     * @param \core\oauth2\issuer $issuer
     * @return bool
     */
    public static function create_new_confirmed_account($userinfo, $issuer) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');

        $user = new stdClass();
        $user->username = $userinfo['username'];
        $user->email = $userinfo['email'];
        $user->auth = 'oauth2';
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->lastname = isset($userinfo['lastname']) ? $userinfo['lastname'] : '';
        $user->firstname = isset($userinfo['firstname']) ? $userinfo['firstname'] : '';
        $user->alternatename = isset($userinfo['alternatename']) ? $userinfo['alternatename'] : '';
        $user->secret = random_string(15);

        $user->password = '';
        // This user is confirmed.
        $user->confirmed = 1;

        $user->id = user_create_user($user, false, true);

        // The linked account is pre-confirmed.
        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->username = $userinfo['username'];
        $record->userid = $user->id;
        $record->email = $userinfo['email'];
        $record->confirmtoken = '';
        $record->confirmtokenexpires = 0;

        $linkedlogin = new linked_login(0, $record);
        $linkedlogin->create();

        return $user;
    }

    /**
     * Send an email with a link to confirm creating this account.
     *
     * @param array $userinfo as returned from an oauth client.
     * @param \core\oauth2\issuer $issuer
     * @param int $userid (defaults to $USER->id)
     * @return bool
     */
    public static function send_confirm_account_email($userinfo, $issuer) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');

        if (linked_login::has_existing_issuer_match($issuer, $userinfo['username'])) {
            throw new moodle_exception('alreadylinked', 'auth_oauth2');
        }

        $user = new stdClass();
        $user->username = $userinfo['username'];
        $user->email = $userinfo['email'];
        $user->auth = 'oauth2';
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->lastname = isset($userinfo['lastname']) ? $userinfo['lastname'] : '';
        $user->firstname = isset($userinfo['firstname']) ? $userinfo['firstname'] : '';
        $user->alternatename = isset($userinfo['alternatename']) ? $userinfo['alternatename'] : '';
        $user->secret = random_string(15);

        $user->password = '';
        // This user is not confirmed.
        $user->confirmed = 0;

        $user->id = user_create_user($user, false, true);

        // The linked account is pre-confirmed.
        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->username = $userinfo['username'];
        $record->userid = $user->id;
        $record->email = $userinfo['email'];
        $record->confirmtoken = '';
        $record->confirmtokenexpires = 0;

        $linkedlogin = new linked_login(0, $record);
        $linkedlogin->create();

        // Construct the email.
        $site = get_site();
        $supportuser = \core_user::get_support_user();
        $user = get_complete_user_data('id', $user->id);

        $data = new stdClass();
        $data->fullname = fullname($user);
        $data->sitename  = format_string($site->fullname);
        $data->admin     = generate_email_signoff();

        $subject = get_string('confirmaccountemailsubject', 'auth_oauth2', format_string($site->fullname));

        $params = [
            'token' => $user->secret,
            'username' => $userinfo['username']
        ];
        $confirmationurl = new moodle_url('/auth/oauth2/confirm-account.php', $params);

        $data->link = $confirmationurl->out(false);
        $message = get_string('confirmaccountemail', 'auth_oauth2', $data);

        $data->link = $confirmationurl->out();
        $messagehtml = text_to_html(get_string('confirmaccountemail', 'auth_oauth2', $data), false, false, true);

        $user->mailformat = 1;  // Always send HTML version as well.

        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        email_to_user($user, $supportuser, $subject, $message, $messagehtml);
        return $user;
    }

    /**
     * Delete linked login
     *
     * Requires auth/oauth2:managelinkedlogins capability at the user context.
     *
     * @param int $linkedloginid
     * @return boolean
     */
    public static function delete_linked_login($linkedloginid) {
        $login = new linked_login($linkedloginid);
        $userid = $login->get('userid');

        if (\core\session\manager::is_loggedinas()) {
            throw new moodle_exception('notwhileloggedinas', 'auth_oauth2');
        }

        $context = context_user::instance($userid);
        require_capability('auth/oauth2:managelinkedlogins', $context);

        $login->delete();
    }

    /**
     * Delete linked logins for a user.
     *
     * @param \core\event\user_deleted $event
     * @return boolean
     */
    public static function user_deleted(\core\event\user_deleted $event) {
        global $DB;

        $userid = $event->objectid;

        return $DB->delete_records(linked_login::TABLE, ['userid' => $userid]);
    }

    /**
     * Is the plugin enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return is_enabled_auth('oauth2');
    }
}

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

        return linked_login::get_records(['userid' => $userid]);
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
        $match = linked_login::get_record($params);

        if ($match) {
            $user = get_complete_user_data('id', $match->get('userid'));

            return $user;
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
     * @return boolean
     */
    public static function link_login($userinfo, $issuer, $userid = false) {
        global $USER;

        if ($userid === false) {
            $userid = $USER->id;
        }

        if (\core\session\manager::is_loggedinas()) {
            throw new moodle_exception('notwhileloggedinas', 'auth_oauth2');
        }

        $context = context_user::instance($userid);
        require_capability('auth/oauth2:managelinkedlogins', $context);

        $record = new stdClass();
        $record->issuerid = $issuer->get('id');
        $record->username = $userinfo['username'];
        $record->email = $userinfo['email'];
        $record->userid = $userid;
        $existing = linked_login::get_record((array)$record);
        if ($existing) {
            return $existing;
        }
        $linkedlogin = new linked_login(0, $record);
        return $linkedlogin->create();
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
}

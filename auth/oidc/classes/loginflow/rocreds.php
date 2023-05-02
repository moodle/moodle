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
 * Resource Owner Password Credentials Grant login flow.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc\loginflow;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

/**
 * Login flow for the oauth2 resource owner credentials grant.
 */
class rocreds extends base {
    /**
     * Check for an existing user object.
     *
     * @param string $o356username
     *
     * @return string If there is an existing user object, return the username associated with it.
     *                If there is no existing user object, return the original username.
     */
    protected function check_objects($o356username) {
        global $DB;

        $user = null;
        if (auth_oidc_is_local_365_installed()) {
            $sql = 'SELECT u.username
                      FROM {local_o365_objects} obj
                      JOIN {user} u ON u.id = obj.moodleid
                     WHERE obj.o365name = ? and obj.type = ?';
            $params = [$o356username, 'user'];
            $user = $DB->get_record_sql($sql, $params);
        }

        return (!empty($user)) ? $user->username : $o356username;
    }

    /**
     * Provides a hook into the login page.
     *
     * @param object &$frm Form object.
     * @param object &$user User object.
     * @return bool
     */
    public function loginpage_hook(&$frm, &$user) {
        global $DB;

        if (empty($frm)) {
            $frm = data_submitted();
        }
        if (empty($frm)) {
            return true;
        }

        $username = $frm->username;
        $password = $frm->password;
        $auth = 'oidc';

        $username = $this->check_objects($username);
        if ($username !== $frm->username) {
            $success = $this->user_login($username, $password);
            if ($success === true) {
                $existinguser = $DB->get_record('user', ['username' => $username]);
                if (!empty($existinguser)) {
                    $user = $existinguser;
                    return true;
                }
            }
        }

        $autoappend = get_config('auth_oidc', 'autoappend');
        if (empty($autoappend)) {
            // If we're not doing autoappend, just let things flow naturally.
            return true;
        }

        $existinguser = $DB->get_record('user', ['username' => $username]);
        if (!empty($existinguser)) {
            // We don't want to prevent access to existing accounts.
            return true;
        }

        $username .= $autoappend;
        $success = $this->user_login($username, $password);
        if ($success !== true) {
            // No o365 user, continue normally.
            return false;
        }

        $existinguser = $DB->get_record('user', ['username' => $username]);
        if (!empty($existinguser)) {
            $user = $existinguser;
            return true;
        }

        // The user is authenticated but user creation may be disabled.
        if (!empty($CFG->authpreventaccountcreation)) {
            $failurereason = AUTH_LOGIN_UNAUTHORISED;

            // Trigger login failed event.
            $event = \core\event\user_login_failed::create(array('other' => array('username' => $username,
                    'reason' => $failurereason)));
            $event->trigger();

            debugging('[client '.getremoteaddr()."]  $CFG->wwwroot  Unknown user, can not create new accounts:  $username  ".
                $_SERVER['HTTP_USER_AGENT']);

            return false;
        }

        $user = create_user_record($username, $password, $auth);
        return true;
    }

    /**
     * This is the primary method that is used by the authenticate_user_login() function in moodlelib.php.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password = null) {
        global $DB;

        $client = $this->get_oidcclient();
        $authparams = ['code' => ''];

        $oidcusername = $username;
        $oidctoken = $DB->get_records('auth_oidc_token', ['username' => $username]);
        if (!empty($oidctoken)) {
            $oidctoken = array_shift($oidctoken);
            if (!empty($oidctoken) && !empty($oidctoken->oidcusername)) {
                $oidcusername = $oidctoken->oidcusername;
            }
        }

        // Make request.
        $tokenparams = $client->rocredsrequest($oidcusername, $password);
        if (!empty($tokenparams) && isset($tokenparams['token_type']) && $tokenparams['token_type'] === 'Bearer') {
            list($oidcuniqid, $idtoken) = $this->process_idtoken($tokenparams['id_token']);

            // Check restrictions.
            $passed = $this->checkrestrictions($idtoken);
            if ($passed !== true) {
                $errstr = 'User prevented from logging in due to restrictions.';
                \auth_oidc\utils::debug($errstr, 'handleauthresponse', $idtoken);
                return false;
            }

            $tokenrec = $DB->get_record('auth_oidc_token', ['oidcuniqid' => $oidcuniqid]);
            if (!empty($tokenrec)) {
                $this->updatetoken($tokenrec->id, $authparams, $tokenparams);
            } else {
                $originalupn = null;
                if (auth_oidc_is_local_365_installed()) {
                    $apiclient = \local_o365\utils::get_api();
                    $userdetails = $apiclient->get_user($oidcuniqid, true);
                    if (!is_null($userdetails) && isset($userdetails['userPrincipalName']) &&
                        stripos($userdetails['userPrincipalName'], '#EXT#') !== false) {
                        $originalupn = $userdetails['userPrincipalName'];
                    }
                }
                $this->createtoken($oidcuniqid, $username, $authparams, $tokenparams, $idtoken, 0, $originalupn);
            }
            return true;
        }
        return false;
    }
}

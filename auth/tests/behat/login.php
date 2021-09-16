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
// phpcs:disable moodle.Files.RequireLogin.Missing
// phpcs:disable moodle.PHP.ForbiddenFunctions.Found

/**
 * Login end point for Behat tests only.
 *
 * @package    core_auth
 * @category   test
 * @author     Guy Thomas
 * @copyright  2021 Class Technologies Inc. {@link https://www.class.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__.'/../../../config.php');
require_once("{$CFG->dirroot}/login/lib.php");

$behatrunning = defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING;
if (!$behatrunning) {
    redirect(new moodle_url('/'));
}

$username = required_param('username', PARAM_ALPHANUMEXT);
$wantsurl = optional_param('wantsurl', null, PARAM_URL);

if (isloggedin()) {
    // If the user is already logged in, log them out and redirect them back to login again.
    require_logout();
    redirect(new moodle_url('/auth/tests/behat/login.php', [
        'username' => $username,
        'wantsurl' => (new moodle_url($wantsurl))->out(false),
    ]));
}

// Note - with behat, the password is always the same as the username.
$password = $username;

$failurereason = null;
$user = authenticate_user_login($username, $password, true, $failurereason, false);
if ($failurereason) {
    switch($failurereason) {
        case AUTH_LOGIN_NOUSER:
            $reason = get_string('invalidlogin');
            break;
        case AUTH_LOGIN_SUSPENDED:
            $reason = 'User suspended';
            break;
        case AUTH_LOGIN_FAILED:
            $reason = 'Login failed';
            break;
        case AUTH_LOGIN_LOCKOUT:
            $reason = 'Account locked';
            break;
        case AUTH_LOGIN_UNAUTHORISED:
            $reason = get_string('unauthorisedlogin', 'core', $username);
            break;
        default:
            $reason = "Unknown login failure: '{$failurereason}'";
            break;

    }

    // Note: Do not throw an exception here as we sometimes test that login does not work.
    // Exceptions are automatic failures in Behat.
    \core\notification::add($reason, \core\notification::ERROR);
    redirect(new moodle_url('/'));
}

if (!complete_user_login($user)) {
    throw new Exception("Failed to login as behat step for $username");
}

if (empty($wantsurl)) {
    $wantsurl = core_login_get_return_url();
}
redirect(new moodle_url($wantsurl));

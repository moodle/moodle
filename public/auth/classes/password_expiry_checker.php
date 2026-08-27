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

namespace core_auth;

/**
 * Determines whether an authenticated user's password is hard-expired, per their authentication
 * plugin's own configuration and API - config->expiration, password_expire(),
 * can_change_password() and change_password_url().
 *
 * This mirrors the detection (but not the presentation) that login/index.php has always done
 * inline, and that {@see \core_auth\validate_user::validate_credentials_not_expired()} also
 * performs for web service/token logins. Neither of those exposes the one distinction a
 * browser-based flow needs in order to react differently: whether the plugin's change-password
 * destination is external to Moodle or not. This class exists only to expose that distinction; it
 * deliberately does not decide, redirect, or otherwise act on the result.
 *
 * This deliberately covers only hard expiry (days-to-expire < 0). The non-blocking warning period
 * that login/index.php also shows is out of scope, and is not represented by
 * {@see password_expiry_check} at all.
 *
 * @package    core_auth
 * @copyright  Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class password_expiry_checker {
    /**
     * Constructor for password_expiry_checker.
     */
    public function __construct(
        /** @var \core\authentication */
        protected readonly \core\authentication $authentication,
    ) {
    }

    /**
     * Check whether the given user's password is hard-expired.
     *
     * @param \stdClass $user A full user record, including ->auth and ->username.
     * @return password_expiry_check
     */
    public function check(\stdClass $user): password_expiry_check {
        if (isguestuser($user)) {
            return password_expiry_check::ok();
        }

        $authplugin = $this->authentication->get_plugin($user->auth);
        if (empty($authplugin->config->expiration) || $authplugin->config->expiration != 1) {
            return password_expiry_check::ok();
        }

        $daystoexpire = intval($authplugin->password_expire($user->username));
        if ($daystoexpire >= 0) {
            // Not expired, or within the non-blocking warning period - both out of scope here.
            return password_expiry_check::ok();
        }

        if (!$authplugin->can_change_password()) {
            // The plugin cannot change the password at all, internally or externally - there is
            // no usable change-password destination to send the user to.
            return password_expiry_check::expired_unsupported();
        }

        $externalurl = $authplugin->change_password_url();
        if ($externalurl) {
            return password_expiry_check::expired_external(
                $externalurl instanceof \core\url ? $externalurl : new \core\url($externalurl),
            );
        }

        return password_expiry_check::expired_internal();
    }
}

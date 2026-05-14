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

use core\authentication;
use core_auth\exception\access_denied_exception;

/**
 * User validation for authentication flows.
 *
 * Note: This class should be fetched using DI.
 *
 * @package    core_auth
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class validate_user {
    /**
     * Constructor.
     *
     * @param authentication $authentication The authentication plugin registry
     */
    public function __construct(
        /** @var authentication The authentication plugin registry */
        protected readonly authentication $authentication,
    ) {
    }

    /**
     * Validate a user before allowing them to login via the web UI.
     *
     * @param \stdClass $user The user object.
     * @throws access_denied_exception If the user is not allowed to login via an external service.
     */
    public function validate_before_web_login(\stdClass $user): void {
        // Maintenance mode is checked later in the login process.
        // Deleted records are not passed in.
        // User confirmation is checked in the login/index.php.
        // Password expiry is checked in login/index.php and treated differently.

        // Suspended users cannot login.
        $this->validate_is_not_suspended($user);

        // Users with a disabled auth method cannot login.
        $this->validate_auth_not_disabled($user);
    }

    /**
     * Validate a user before allowing them to login via an external service.
     *
     * @param \stdClass $user The user object.
     * @throws access_denied_exception If the user is not allowed to login via an external service.
     */
    public function validate_before_external_login(\stdClass $user): void {
        // Cannot authenticate unless maintenance access is granted.
        $this->validate_maintenance_mode_access($user);

        // Deleted users cannot login.
        $this->validate_not_deleted($user);

        // Only confirmed user should be able to call web service.
        $this->validate_is_confirmed($user);

        // Suspended users cannot login.
        $this->validate_is_not_suspended($user);

        // Users with a disabled auth method cannot login.
        $this->validate_auth_not_disabled($user);

        // Ensure that login credentials are not expired.
        $this->validate_credentials_not_expired($user);
    }

    /**
     * Validate a user before allowing them to login using a login token.
     *
     * @param \stdClass $user The user object
     * @throws access_denied_exception If the user is not allowed to login using a token.
     */
    public function validate_before_token_login(\stdClass $user): void {
        // Cannot authenticate unless maintenance access is granted.
        $this->validate_maintenance_mode_access($user);

        // Only confirmed user should be able to call web service.
        $this->validate_is_confirmed($user);
        $this->validate_user_is_not_guest_user($user);
        $this->validate_credentials_not_expired($user);
    }

    /**
     * Validate that the user has maintenance mode access if maintenance mode is enabled.
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\maintenance_mode_enabled_exception If the user does not have maintenance mode access
     */
    public function validate_maintenance_mode_access(\stdClass $user): void {
        global $CFG;

        // Cannot authenticate unless maintenance access is granted.
        if (!empty($CFG->maintenance_enabled)) {
            if (!has_capability('moodle/site:maintenanceaccess', \core\context\system::instance(), $user)) {
                throw new \core_auth\exception\maintenance_mode_enabled_exception($user);
            }
        }
    }

    /**
     * Validate that the user has not been deleted.
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\user_deleted_exception If the user has been deleted
     */
    public function validate_not_deleted(\stdClass $user): void {
        if (!empty($user->deleted)) {
            throw new \core_auth\exception\user_deleted_exception($user);
        }
    }

    /**
     * Validate that the user has been confirmed.
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\user_not_confirmed_exception If the user has not been confirmed
     */
    public function validate_is_confirmed(\stdClass $user): void {
        if (empty($user->confirmed)) {
            throw new \core_auth\exception\user_not_confirmed_exception($user);
        }
    }

    /**
     * Validate that the user has not been suspended.
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\user_suspended_exception If the user has been suspended
     */
    public function validate_is_not_suspended(\stdClass $user): void {
        if (!empty($user->suspended)) {
            throw new \core_auth\exception\user_suspended_exception($user);
        }
    }

    /**
     * Validate that the user's auth method is not disabled (e.g. 'nologin' or a disabled plugin).
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\auth_disabled_exception If the user's auth method is disabled
     */
    public function validate_auth_not_disabled(\stdClass $user): void {
        if ($user->auth === 'nologin' || !$this->authentication->is_enabled($user->auth)) {
            throw new \core_auth\exception\auth_disabled_exception($user);
        }
    }

    /**
     * Validate that the user is not a guest account.
     *
     * @param \stdClass $user
     * @throws exception\user_is_guest_exception If the user is a guest
     */
    public function validate_user_is_not_guest_user(\stdClass $user): void {
        if (isguestuser($user)) {
            throw new \core_auth\exception\user_is_guest_exception($user);
        }
    }

    /**
     * Validate that the user's credentials have not expired.
     *
     * @param \stdClass $user The user object.
     * @throws \core_auth\exception\credentials_expired_exception If credentials have expired
     */
    public function validate_credentials_not_expired(\stdClass $user): void {
        $auth = $this->authentication->get_plugin($user->auth);
        if (!empty($auth->config->expiration) && $auth->config->expiration == 1) {
            $days2expire = $auth->password_expire($user->username);
            if (intval($days2expire) < 0) {
                throw new \core_auth\exception\credentials_expired_exception($user);
            }
        }
    }
}

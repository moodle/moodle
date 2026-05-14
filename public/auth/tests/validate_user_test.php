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
 * Tests for validate_user.
 *
 * @package    core_auth
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(validate_user::class)]
final class validate_user_test extends \advanced_testcase {
    // Tests for validate_before_external_login.

    public function test_valid_user_passes_all_checks_before_login(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 1,
            'deleted' => 0,
            'suspended' => 0,
        ]);

        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    public function test_maintenance_mode_blocks_before_other_checks(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->maintenance_enabled = 1;

        // User is also deleted and unconfirmed, but maintenance check comes first.
        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 0,
            'suspended' => 1,
        ]);
        $user->deleted = 1;

        $this->expectException(exception\maintenance_mode_enabled_exception::class);
        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    public function test_deleted_blocks_before_confirmed(): void {
        $this->resetAfterTest();

        // User is deleted and unconfirmed — deleted check comes first.
        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 0,
        ]);
        $user->deleted = 1;

        $this->expectException(exception\user_deleted_exception::class);
        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    public function test_unconfirmed_blocks_before_suspended(): void {
        $this->resetAfterTest();

        // User is unconfirmed and suspended — unconfirmed check comes first.
        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 0,
            'suspended' => 1,
        ]);

        $this->expectException(exception\user_not_confirmed_exception::class);
        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    public function test_auth_disabled_blocks_in_external_login(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'auth' => 'nologin',
            'confirmed' => 1,
            'suspended' => 0,
        ]);

        $this->expectException(exception\auth_disabled_exception::class);
        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    public function test_expired_credentials_blocks_in_external_login(): void {
        $this->resetAfterTest();

        set_config('expiration', 1, 'auth_manual');
        set_config('expirationtime', 1, 'auth_manual');

        $user = $this->getDataGenerator()->create_user([
            'auth' => 'manual',
            'confirmed' => 1,
            'suspended' => 0,
        ]);

        // Set the password update time to well in the past so it's expired.
        set_user_preference('auth_manual_passwordupdatetime', 1, $user);

        $this->expectException(exception\credentials_expired_exception::class);
        \core\di::get(validate_user::class)->validate_before_external_login($user);
    }

    // Tests for validate_before_token_login.

    public function test_valid_user_passes_all_checks_before_token_login(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 1,
        ]);

        \core\di::get(validate_user::class)->validate_before_token_login($user);
    }

    public function test_token_login_maintenance_mode_blocks_before_other_checks(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->maintenance_enabled = 1;

        // User is also unconfirmed, but maintenance check comes first.
        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 0,
        ]);

        $this->expectException(exception\maintenance_mode_enabled_exception::class);
        \core\di::get(validate_user::class)->validate_before_token_login($user);
    }

    public function test_token_login_unconfirmed_blocks_before_guest_check(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 0,
        ]);

        $this->expectException(exception\user_not_confirmed_exception::class);
        \core\di::get(validate_user::class)->validate_before_token_login($user);
    }

    public function test_token_login_guest_user_throws(): void {
        $this->resetAfterTest();

        $guest = guest_user();

        $this->expectException(exception\user_is_guest_exception::class);
        \core\di::get(validate_user::class)->validate_before_token_login($guest);
    }

    public function test_token_login_expired_credentials_throws(): void {
        $this->resetAfterTest();

        set_config('expiration', 1, 'auth_manual');
        set_config('expirationtime', 1, 'auth_manual');

        $user = $this->getDataGenerator()->create_user([
            'auth' => 'manual',
            'confirmed' => 1,
        ]);

        // Set the password update time to well in the past so it's expired.
        set_user_preference('auth_manual_passwordupdatetime', 1, $user);

        $this->expectException(exception\credentials_expired_exception::class);
        \core\di::get(validate_user::class)->validate_before_token_login($user);
    }

    // Tests for validate_before_web_login.

    public function test_valid_user_passes_all_checks_before_web_login(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'confirmed' => 1,
            'suspended' => 0,
        ]);

        \core\di::get(validate_user::class)->validate_before_web_login($user);
    }

    public function test_web_login_suspended_blocks_before_auth_check(): void {
        $this->resetAfterTest();

        // User is suspended and has nologin auth — suspended check comes first.
        $user = $this->getDataGenerator()->create_user([
            'auth' => 'nologin',
            'suspended' => 1,
        ]);

        $this->expectException(exception\user_suspended_exception::class);
        \core\di::get(validate_user::class)->validate_before_web_login($user);
    }

    public function test_web_login_auth_disabled_throws(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'auth' => 'nologin',
            'suspended' => 0,
        ]);

        $this->expectException(exception\auth_disabled_exception::class);
        \core\di::get(validate_user::class)->validate_before_web_login($user);
    }

    // Tests for validate_auth_not_disabled.

    public function test_enabled_auth_passes(): void {
        $user = (object) ['auth' => 'manual'];
        \core\di::get(validate_user::class)->validate_auth_not_disabled($user);
    }

    public function test_nologin_auth_throws(): void {
        $user = (object) ['auth' => 'nologin', 'username' => 'testuser'];

        $this->expectException(exception\auth_disabled_exception::class);
        \core\di::get(validate_user::class)->validate_auth_not_disabled($user);
    }

    public function test_disabled_auth_plugin_throws(): void {
        $this->resetAfterTest();

        // Disable the email auth plugin.
        set_config('auth', 'manual');

        $user = (object) ['auth' => 'email', 'username' => 'testuser'];

        $this->expectException(exception\auth_disabled_exception::class);
        \core\di::get(validate_user::class)->validate_auth_not_disabled($user);
    }

    // Tests for validate_credentials_not_expired.

    public function test_non_expiring_auth_passes(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        \core\di::get(validate_user::class)->validate_credentials_not_expired($user);
    }

    public function test_expired_credentials_throws(): void {
        $this->resetAfterTest();

        set_config('expiration', 1, 'auth_manual');
        set_config('expirationtime', 1, 'auth_manual');

        $user = $this->getDataGenerator()->create_user(['auth' => 'manual']);

        // Set the password update time to well in the past so it's expired.
        set_user_preference('auth_manual_passwordupdatetime', 1, $user);

        $this->expectException(exception\credentials_expired_exception::class);
        \core\di::get(validate_user::class)->validate_credentials_not_expired($user);
    }

    // Tests for validate_is_confirmed.

    public function test_confirmed_user_passes(): void {
        $user = (object) ['confirmed' => 1];
        \core\di::get(validate_user::class)->validate_is_confirmed($user);
    }

    public function test_unconfirmed_user_throws(): void {
        $user = (object) ['username' => 'testuser', 'confirmed' => 0];

        $this->expectException(exception\user_not_confirmed_exception::class);
        \core\di::get(validate_user::class)->validate_is_confirmed($user);
    }

    // Tests for validate_is_not_suspended.

    public function test_non_suspended_user_passes(): void {
        $user = (object) ['suspended' => 0];
        \core\di::get(validate_user::class)->validate_is_not_suspended($user);
    }

    public function test_suspended_user_throws(): void {
        $user = (object) ['username' => 'testuser', 'suspended' => 1];

        $this->expectException(exception\user_suspended_exception::class);
        \core\di::get(validate_user::class)->validate_is_not_suspended($user);
    }

    // Tests for validate_maintenance_mode_access.

    public function test_maintenance_mode_disabled_passes(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        \core\di::get(validate_user::class)->validate_maintenance_mode_access($user);
    }

    public function test_maintenance_mode_admin_passes(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->maintenance_enabled = 1;

        $admin = get_admin();
        \core\di::get(validate_user::class)->validate_maintenance_mode_access($admin);
    }

    public function test_maintenance_mode_regular_user_throws(): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->maintenance_enabled = 1;

        $user = $this->getDataGenerator()->create_user();

        $this->expectException(exception\maintenance_mode_enabled_exception::class);
        \core\di::get(validate_user::class)->validate_maintenance_mode_access($user);
    }

    // Tests for validate_not_deleted.

    public function test_non_deleted_user_passes(): void {
        $user = (object) ['deleted' => 0];
        \core\di::get(validate_user::class)->validate_not_deleted($user);
    }

    public function test_deleted_user_throws(): void {
        $user = (object) ['username' => 'testuser', 'deleted' => 1];

        $this->expectException(exception\user_deleted_exception::class);
        \core\di::get(validate_user::class)->validate_not_deleted($user);
    }

    public function test_exception_contains_username(): void {
        $user = (object) ['deleted' => 1, 'username' => 'testuser'];

        $this->expectException(exception\user_deleted_exception::class);
        $this->expectExceptionMessageMatches('/testuser/');
        \core\di::get(validate_user::class)->validate_not_deleted($user);
    }

    // Tests for validate_user_is_not_guest_user.

    public function test_validate_user_is_not_guest_user(): void {
        $admin = get_admin();
        \core\di::get(validate_user::class)->validate_user_is_not_guest_user($admin);

        $guest = guest_user();
        $this->expectException(exception\user_is_guest_exception::class);
        \core\di::get(validate_user::class)->validate_user_is_not_guest_user($guest);
    }
}

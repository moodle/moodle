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

namespace auth_email;

/**
 * Tests for email authentication plugin.
 *
 * @package     auth_email
 * @copyright   2026 Moodle Pty Ltd
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \auth_plugin_email
 */
final class auth_test extends \advanced_testcase {
    /**
     * Test that user_confirm() cleans up the auth_email_wantsurl preference
     * when confirming a user for the first time (AUTH_CONFIRM_OK).
     */
    public function test_user_confirm_cleans_up_wantsurl_preference(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Create an unconfirmed user with the email auth method.
        $user = $this->getDataGenerator()->create_user([
            'auth' => 'email',
            'confirmed' => 0,
        ]);
        $secret = random_string(15);
        $DB->set_field('user', 'secret', $secret, ['id' => $user->id]);

        // Simulate the wantsurl preference saved at signup time.
        set_user_preference('auth_email_wantsurl', 'https://example.com/course/view.php?id=42', $user);
        $this->assertTrue(
            $DB->record_exists('user_preferences', ['userid' => $user->id, 'name' => 'auth_email_wantsurl']),
            'Preference should exist in DB before confirmation.'
        );

        $auth = get_auth_plugin('email');
        $result = $auth->user_confirm($user->username, $secret);

        $this->assertEquals(AUTH_CONFIRM_OK, $result);
        $this->assertFalse(
            $DB->record_exists('user_preferences', ['userid' => $user->id, 'name' => 'auth_email_wantsurl']),
            'auth_email_wantsurl preference must be removed from DB after successful confirmation.'
        );
    }

    /**
     * Test that user_confirm() cleans up the auth_email_wantsurl preference
     * even when the user is already confirmed (AUTH_CONFIRM_ALREADY).
     *
     * This covers the edge case where a user clicks the confirmation link
     * a second time — the stale preference should still be cleaned up.
     */
    public function test_user_confirm_already_confirmed_cleans_up_wantsurl_preference(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Create an already-confirmed user with the email auth method.
        $user = $this->getDataGenerator()->create_user([
            'auth' => 'email',
            'confirmed' => 1,
        ]);
        $secret = random_string(15);
        $DB->set_field('user', 'secret', $secret, ['id' => $user->id]);

        // Simulate a stale wantsurl preference left over from signup.
        set_user_preference('auth_email_wantsurl', 'https://example.com/course/view.php?id=42', $user);
        $this->assertTrue(
            $DB->record_exists('user_preferences', ['userid' => $user->id, 'name' => 'auth_email_wantsurl']),
            'Preference should exist in DB before re-confirmation.'
        );

        $auth = get_auth_plugin('email');
        $result = $auth->user_confirm($user->username, $secret);

        $this->assertEquals(AUTH_CONFIRM_ALREADY, $result);
        $this->assertFalse(
            $DB->record_exists('user_preferences', ['userid' => $user->id, 'name' => 'auth_email_wantsurl']),
            'auth_email_wantsurl preference must be removed from DB even when user is already confirmed.'
        );
    }
}
